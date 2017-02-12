<?php
namespace API;
/*
Scaffolded REST API calls.
*/
class APIController extends \BaseController {

	protected $defaultPageSize = 1000;

	// ResourceController methods
	public function index() {
		return $this->getIndex();
	}

	public function create() {
		return $this->getEdit(0);
	}

	public function store() {
		return $this->postSave();
	}

	public function show($id = 0) {
		return $this->getView($id);
	}

	// Form for editing - will return metadata in JSON
	public function edit($id = 0 )
	{
		return $this->getEdit($id);
	}

	public function update($id = 0)
	{
		return $this->postSave($id);
	}
	public function destroy($id = 0)
	{
		return $this->postDelete($id);
	}
	
	public function arrayToPaginationObject($ary) {
		$o = new \stdClass;
		$o->total = sizeof($ary);
		$o->per_page = 0;
		$o->current_page = 1;
		$o->last_page = 1;
		$o->from = 1;
		$o->to = $o->total;
		$o->data = $ary;
		return $o;
	}

	// Get a list of domain entities
	// GET /entity  (full list)       or      GET /entity/1      (single entity)
	public function getIndex($id = null) {
		if ($id != null) return $this->getView($id);
		$className = $this->getClassForController();
		// Check permissions first
		if ( !\Authority::can('read', $className) ) return \Response::json(['meta' => ['message' => "Not authorized to read"]], 403);

		$class = new $className();
		// Get the paginated list of domain entities


		if (method_exists($className,'RESTQueryBuilder')) {
			$list = $className::RESTQueryBuilder(null, $this->defaultPageSize);
		} else {
			$list = $className::paginate($this->defaultPageSize);	// 25 = default page size
		}

		// Remove/modify information based on the user's authorization
		$this->applyACLToList($class, $list, isset($class->adminSettings['acl']) ? $class->adminSettings['acl'] : null);
		// Allow subclass controllers to perform further processing on the list
		return $this->beforeList($list);

	}

	// Store a domain entity
	// POST /entity/1
	public function postSave($id = 0) {

//		dd(\Input::json()->all());
		$className = $this->getClassForController();
		$object = $className::find($id);

		if ( !\Authority::can('update', $className) ) return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);

		if ($object == null) {
			if ($id != 0) throw new InvalidArgumentException("Invalid " . $className);
			$object = new $className();
		}

		if (\Request::isMethod('post') || \Request::isMethod('put')) {
			// If editable fields have been specified on the model, then only those fields can be saved. Otherwise all of them are saved.
			if (!empty($object->adminSettings) && !empty($object->adminSettings['edit'])) {
				$inputs = $this->filterInputs(\Input::json()->all(), $object->adminSettings['edit']['fields']);
				$object->fill($inputs);
			} else {
				$object->fill(\Input::json()->all());
			}
			if (!$object->exists) {
			     if (method_exists($object,'inTenantScope')) {
				$tenantId = \MultiTenantScope::$tenantId;
				if ($tenantId == null) {
					$currentUser = \Auth::user();
					if ($currentUser == null) throw new Exception("Unable to obtain valid tenant, user is not logged in.");
					$tenantId = $currentUser->tenant_id;
				} 
				if ($tenantId != "all") {
					$object->tenant_id = $tenantId;
				}
			     }
			}
			return $this->save($object);
		}

	}

	// GET /entity/view/1
	public function getView($id) {
		$className = $this->getClassForController();
		$isUUID = (strpos($id, "-") !== false);
		if ( !\Authority::can('read', $className) ) return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);

		if (method_exists($className,'RESTQueryBuilder')) {
			$obj = $className::RESTQueryBuilder($id);
		} else {
			if ($isUUID) {
				$obj = $className::where('uuid', '=', $id);
			} else {
				$obj = $className::find($id);
			}
		}

		if ($obj == null) return \Response::json(['meta' => ['message' => "Not Found"]], 404);

		// Check that tenant scope matches (or tenant scoping is disabled)
		if (method_exists($obj,'inTenantScope')) {
			if (!$obj->inTenantScope()) return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);
		}

		$this->applyACLToModel($obj, isset($obj->adminSettings['acl']) ? $obj->adminSettings['acl'] : null);
		return $this->beforeView($obj);
	}


	// DELETE /entity/1
	public function deleteIndex($id) {
		return postDelete($id);
	}

	// POST /entity/delete/1 (alias for DELETE)
	public function postDelete($id) {
		$className = $this->getClassForController();
		if ( !\Authority::can('delete', $className) ) return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);
		$object = $className::find($id);
		$object->delete();
		return array();
	}

	protected function getClassForController() {
		$controllerName = $this->stripNamespaceFromClassName(get_class($this));
		if (endsWith($controllerName, "Controller")) {
			$controllerName = substr($controllerName, 0, strlen($controllerName)-10);
		}
		return $controllerName;
	}

	private function stripNamespaceFromClassName($className)
	{
		if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
			$className = $matches[1];
		}
		return $className;
	}

	protected function findUnauthorizedFields($acl) {
		$removeFields = array();
		foreach (array_keys($acl) as $key) {
			$roles = explode(",",$acl[$key]);
			$authorized = false;
			foreach ($roles as $role) {
				if (\Auth::check() && \Auth::user()->hasRole($role)) {
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
			unset($obj->{$field});
			if (isset($obj->adminSettings['view']) && isset($obj->adminSettings['view']['fields'])) {
				array_remove($field, $obj->adminSettings['view']['fields']);
			}
			if (isset($obj->adminSettings['edit']) && isset($obj->adminSettings['edit']['fields'])) {
				array_remove($field, $obj->adminSettings['edit']['fields']);
			}
		}	
	}

	protected function applyACLToList(&$class, &$list, $acl) {
		if ($acl == null) return;
		$removeFields = $this->findUnauthorizedFields($acl);

		foreach($list as &$obj) {
			foreach($removeFields as $field) {
				if (!empty($class->adminSettings['list']['fields'])) {
					array_remove($field, $class->adminSettings['list']['fields']);
				} 
				foreach ($list as &$obj) {
					unset($obj->{$field});
				}
			}
		}
	}

	/* Not planning to use this, but just in case... */
	protected function getPaginationParams(&$start, &$pageSize) {
		$start = 0;
		$pageSize = $defaultPageSize;
		$range = \Request::header('Content-Range');
		if (!empty($range)) {
			$vals = explode('-', $range);
			$start = intval($vals[0]);
			if (sizeof($vals) > 1) {
				$pageSize = intval($vals[1]);
			}
		}
	}



	public function getEdit($id = 0) {
		$className = $this->getClassForController();
		$object = $className::find($id);

		if ($object == null) {
			if ($id != 0) throw new InvalidArgumentException("Invalid Company");
			$object = new $className();
		}

		// $fields = getFieldsForClass($className);

		$pluralHumanName = \AppModel::pluralHumanName($object);
		$singularHumanName = \AppModel::singularHumanName($object);
		$controllerPath = \AppModel::controllerPath($object);
		$actions = (isset($object->adminSettings["edit"]["actions"])) ? $object->adminSettings["edit"]["actions"] : array('delete');
		$displayName = isset($object->adminSettings["displayField"]) ? $object->{$object->adminSettings["displayField"]} : null;

		$ret = new \stdClass;
		$ret->allowedActions = $actions;
		$ret->displayName = $displayName;
		$ret->singularHumanName = $singularHumanName;
		$ret->pluralHumanName = $pluralHumanName;
		$ret->fields = $this->getFieldsForObject($object);

		return  \Response::json($ret);		
	}

	protected function getFieldsForObject($parentObject, $columns = null, $prefix = "") {
		$parentFormats = $this->getDataFormatsForObject($parentObject);

		if (!isset($columns) || $columns == null || $columns == "default") {
			$columns = (isset($parentObject->adminSettings["edit"]["fields"])) ? $parentObject->adminSettings["edit"]["fields"] : \AppModel::getColumns($parentObject);
		}
		$fields = array();

		foreach ($columns as $key => $fieldValue) { 
			$object = $parentObject;
			$formats = $parentFormats;
			$newField = new \stdClass;
			if (is_array($fieldValue)) {
				$field = $key;
				$newField->name = $key;
			} else {
				$field = $fieldValue;
				$newField->name = $prefix . $field;
			}
			$newField->hidden = false;
			$newField->label = null;

			if (isset($formats[$field])) {
				$newField->format = $formats[$field];
			} else $newField->format = "text";
			if ($field == "created_at") continue;
			if ($field == "id") {
				$newField->hidden = true;
			} else if (is_array($fieldValue)) {
				$otherColumns = $fieldValue;
				$field = $key;
				if (method_exists($object, $field)) {
					// Get the related object's class
					$relationClass = $object->getRelatedClass($field);
					$relationObj = new $relationClass;
					$otherObj = $relationObj;
				}
				$otherFields = $this->getFieldsforObject($otherObj, $otherColumns, $field . ".");
				$fields = array_merge($fields, $otherFields);
			}
			else {
				$newField->label = \AppModel::humanName($field);

				if (endsWith($field, "_id")) {			// This field is probably a foreign key... See what we can do.
					// Figure out what kind of object or relation this is
					$tmpField = \AppModel::camelize(substr($field,0,strlen($field)-3));
					if (is_object($object->{$tmpField}) || method_exists($object, $tmpField)) {
						// Get the related object's class
						$otherClass = $object->getRelatedClass($tmpField);

						// Figure out what to call it/how to display it (the name of the relation)...
						$displayField = "name";
						$otherObj = new $otherClass;
						if(!empty($otherObj->adminSettings) && !empty($otherObj->adminSettings['displayField'])) $displayField = $otherObj->adminSettings['displayField'];
						$values = $otherClass::all()->lists($displayField, 'id');

						// Include the default value
						$defaultValue = null;
						if (isset($_GET[$field])) $defaultValue = $_GET[$field];
						$newField->defaultValue = $defaultValue;
						$newField->options = $values;
					} else {
						// if not a relation, this will be output as a text field
					}
				} else if (endsWith($field, "_by")) {                            
					$tmpField = \AppModel::camelize($tmpField);
					if (is_object($object->{$tmpField})) {
						// TODO: Perhaps include this in the "_id" block above? (it should work)
					}					
				} 
			}
			$fields[] = $newField;
		}
		return $fields;
	}

	protected function getDataFormatsForObject($object) {
		$formats = (isset($object->adminSettings) && isset($object->adminSettings["formats"])) ? $object->adminSettings["formats"] : array();

		$dataTypes = \DB::select( \DB::raw("SHOW COLUMNS FROM ".$object->getTable()));
		foreach ($dataTypes as $dataType) {
			if (!isset($formats[$dataType->Field])) {
				if ($dataType->Type == "datetime") $formats[$dataType->Field] = "date";
				else if ($dataType->Type == "tinyint(1)") $formats[$dataType->Field] = "boolean";
			}
		}
		return $formats;
	}

	// Returns a list of inputs filtered by (intersection of) $filterArray
	protected function filterInputs($inputs, $filter) {
		$included = array();
		foreach($filter as $key => $val) {
			if (is_array($val)) {
				if (isset($inputs[$key])) {
					$included[$key] = $this->filterInputs($inputs[$key], $val);
				}
			} else {
				if (isset($inputs[$val])) {
					$included[$val] = $inputs[$val];
				}
			}
		}
		return $included;
	}

}