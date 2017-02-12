<?php
trait ScaffoldController {

	public function getIndex() {
		$className = $this->getClassForController();
		if( Authority::can('manage', $className) ) {
			$class = new $className();
			$list = $className::paginate(25);
			$this->applyACLToList($class, $list, isset($class->adminSettings['acl']) ? $class->adminSettings['acl'] : null);
			$this->beforeList($list);
			return View::make('scaffold.index')
		->with('objects', $list)		// Oh, this is fucking confusing. Why not {$class} like the rest of PHP? Wow.
		->with('class', $class);
	} else {
		throw new AuthenticationException("Not authorized");
	}
}

public function getView($id) {
	$className = $this->getClassForController();
	$obj = $className::find($id);
	$this->applyACLToModel($obj, isset($obj->adminSettings['acl']) ? $obj->adminSettings['acl'] : null);
	$this->beforeView($obj);
	return View::make('scaffold.view')
	->with('object', $obj);
}

public function anyEdit($id = 0) {
	$className = $this->getClassForController();
	$object = $className::find($id);

	if ($object == null) {
		if ($id != 0) throw new InvalidArgumentException("Invalid Company");
		$object = new $className();
	}

	if (Request::isMethod('post') || Request::isMethod('put')) {
		$object->fill(Input::all());
		$this->beforeSave($object);
		if ($object->save()) {
			return Redirect::back();
		} 
/*		if ($id == 0) $object->create(Input::all());
		else $object->update(Input::all());
		return Redirect::back();*/
	}

	return View::make('scaffold.form')
	->with('object', $object)
	->with('class', new $className())
	->withErrors($object->errors());
}

public function deleteDelete($id) {
	return postDelete($id);
}

public function postDelete($id) {
	$className = $this->getClassForController();
	$object = $className::find($id);
	$object->delete();
	return Redirect::back();
}

protected function getClassForController() {
	$controllerName = get_class($this);
	if (endsWith($controllerName, "Controller")) {
		$controllerName = substr($controllerName, 0, strlen($controllerName)-10);
	}
	return $controllerName;
}

protected function findUnauthorizedFields($acl) {
	$removeFields = array();
	foreach (array_keys($acl) as $key) {
		$roles = explode(",",$acl[$key]);
		$authorized = false;
		foreach ($roles as $role) {
			if (Auth::check() && Auth::user()->hasRole($role)) {
				$authorized = true;
				break;
			}
		}
		if (!$authorized) $removeFields[] = $key;
	}
	return $removeFields;	
}

protected function applyACLToModel(&$obj, $acl) {
	if ($acl == null) return;
	$removeFields = $this->findUnauthorizedFields($acl);

	foreach($removeFields as $field) {
		array_remove($field, $obj->adminSettings['view']['fields']);
		array_remove($field, $obj->adminSettings['edit']['fields']);
	}	
}

protected function applyACLToList(&$class, &$list, $acl) {
	if ($acl == null) return;
	$removeFields = $this->findUnauthorizedFields($acl);

	foreach($list as &$obj) {
		foreach($removeFields as $field) {
			array_remove($field, $class->adminSettings['list']['fields']);
		}
	}
}

public abstract function beforeSave(&$model);
public abstract function beforeView(&$model);
public abstract function beforeList(&$list);

}