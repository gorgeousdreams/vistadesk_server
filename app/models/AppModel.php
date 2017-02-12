<?php
class AppModel extends \LaravelBook\Ardent\Ardent {

	public $timestamps = false;

	protected static $_cache = array();
	protected $guarded = array('uuid','id');

	// Laravel loses about 1,000 points for making me write this shit.
	public function saveAll() {
		DB::beginTransaction();
		$this->save();
		$relations = AppModel::getAllRelations($this);
		foreach($relations as $rel) {
			$list = $this->{$rel->dynamicProperty};
			if (is_a($list, 'Illuminate\Database\Eloquent\Collection')) {
				foreach ($list as $item) {
					$rel->save($item);
				}
			}
		}
		DB::commit();
		return true;
	}

	public function fillAndSave($inputs) {
		$this->fillAllowedFields($inputs);
		$this->save();
	}

	public function fillAllowedFields($inputs) {

		if (!empty($this->adminSettings) && !empty($this->adminSettings['edit'])) {
			$allowedInputs =  $this->filterInputs($inputs, $this->adminSettings['edit']['fields']);
		} else {
			$allowedInputs =  $inputs;
		}
		$this->fill($allowedInputs);
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


	/* Fills a model. Laravel loses another 1,000 points for making me write this too. */
	public function fillAll($inputs) {
		$modelFields = $this->filterRelations($inputs, false);
		foreach ($this->filterRelations($inputs, true) as $relation => $relationArray) {
			$this->fillRelation($relation, $relationArray);
		}
	}

	// Not working yet. Probably not going to finish. The idea was to do Cake's saveAll().
	// Probably too much of a pain in the ass in Laravel, instead I'll let developers do
	// this in their subclasses. Sorry guys!

	protected function fillRelation($relationName, $data) {
		if (method_exists($this, $relationName)) {
			$relation = $this->$relationName();
			$otherClass = $this->getRelatedClass($relationName);
			if ($otherObj->id != 0) {
				$otherObj = $className::find($id);
			}
			else {
				$otherObj = new $otherClass();
			}
			$otherObj->fill($data);

			if (is_a($relation, 'Illuminate\Database\Eloquent\Relations\BelongsTo')) {
				// associate. 
				$otherObj->save();
				$relation->associate($otherObj);
			} else if (is_a($relation, 'Illuminate\Database\Eloquent\Relations\HasOne')) {
				// attach? no docs
				$relation->attach($otherObj);
			} else if (is_a($relation, 'Illuminate\Database\Eloquent\Relations\HasMany')) {
				// add
				$relation->add($otherObj);
			} else if (is_a($relation, 'Illuminate\Database\Eloquent\Relations\BelongsToMany')) {
				// FIXME: who knows. Laravel sucks.
			}
		}
	}

	/* Gets the class of the relationship */
	public function getRelatedClass($fieldName) {
		return is_object($this->{$fieldName}) ? get_class($this->{$fieldName}) : get_class($this->$fieldName()->getRelated());
	}

	/* 
	Returns the inputs, either only/except relationships. Useful for filling objects with non-relationship data
	or populating relationships separately.
	*/
	protected function filterRelations($inputs, $includeRelations) {
		$ret = array();
		foreach ($inputs as $k => $v) {
			if ($includeRelations == is_array($v)) $ret[$k] = $v;
		}
		return $ret;
	}


	public static function humanize($columnName) {
		if (endsWith($columnName, "_id")) $columnName = str_replace("_id", "", $columnName);
		$columnName = AppModel::humanName($columnName);
		if ($columnName == "id") return "ID";
		return ucfirst($columnName);
	}

	// billing-entries
	public static function controllerPath($modelObject) {
		return str_plural(AppModel::toSlug($modelObject));
	}

	// billing entry
	public static function humanName($modelObject) {
		return ucwords(str_replace('-', ' ', AppModel::toSlug($modelObject)));
	}

	// billing_entry
	public static function columnName($modelObject) {
		return ucwords(str_replace('-', '_', AppModel::toSlug($modelObject)));
	}

	// Billing entries
	public static function pluralHumanName($modelObject) {
		return str_plural(AppModel::humanName($modelObject));
	}

	// Billing entry
	public static function singularHumanName($modelObject) {
		return str_singular(AppModel::humanName($modelObject));
	}

	// BillingEntries
	public static function className($modelObject) {
		return str_replace(' ', '', ucwords(str_replace('-', ' ', AppModel::toSlug($modelObject))));
	}

	// BillingEntry
	public static function singularClassName($modelObject) {
		return str_singular(str_replace(' ', '', ucwords(str_replace('-', ' ', AppModel::toSlug($modelObject)))));
	}

	// billing-entry
	public static function toSlug($modelObject) {
		if (!is_string($modelObject)) {
			$modelName = $modelObject->getTable();
		} else {
			$modelName = AppModel::deCamelize($modelObject);
		}

		$slug = str_replace("_", "-", strtolower($modelName));
		$slug = str_replace(" ", "-", strtolower($slug));
		$slug = preg_replace('/[^\w\d\-\_]/i', '', $slug);
		return $slug;
	}

	public static function deCamelize($camelCasedWord) {
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '-\\1', $camelCasedWord));
	}

	public static function camelize($modelObject) {
		return str_replace(" ", "", lcfirst(ucwords(AppModel::humanName($modelObject))));
	}

	public static function getAllRelations($model) {
		$all = get_class_methods($model);
		$unique = array_diff($all, get_class_methods('Eloquent'));
		$unique = array_diff($unique, get_class_methods('AppModel'));
		$relations = array();
		$class_reflection = new ReflectionClass(get_class($model));

		foreach($unique AS $method)
		{
			if($class_reflection->getMethod($method)->getNumberOfParameters() == 0) {
				$relation = $model->$method();
				if(is_a($relation, 'Illuminate\Database\Eloquent\Relations\Relation'))
				{
					$methodName = $method;
					if (startsWith($methodName, "get")) $methodName = substr($methodName, 3);
					$relations[AppModel::className($methodName)] = $relation;
					$relations[AppModel::className($methodName)]->dynamicProperty = $methodName;
				}
			}
		}
		return $relations;
	}

	public static function getColumns($class)
	{
		$tableName = $class->getTable();
		switch (DB::connection()->getConfig('driver')) {
			case 'pgsql':
			$query = "SELECT column_name FROM information_schema.columns WHERE table_name = '".$tableName."'";
			$column_name = 'column_name';
			$reverse = true;
			break;

			case 'mysql':
			$query = 'SHOW COLUMNS FROM '.$tableName;
			$column_name = 'Field';
			$reverse = false;
			break;

			case 'sqlsrv':
			$parts = explode('.', $tableName);
			$num = (count($parts) - 1);
			$table = $parts[$num];
			$query = "SELECT column_name FROM ".DB::connection()->getConfig('database').".INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'".$table."'";
			$column_name = 'column_name';
			$reverse = false;
			break;

			default: 
			$error = 'Database driver not supported: '.DB::connection()->getConfig('driver');
			throw new Exception($error);
			break;
		}

		$columns = array();

		foreach(DB::select($query) as $column)
		{
			$columns[] = $column->$column_name;
		}

		if($reverse)
		{
			$columns = array_reverse($columns);
		}

		return $columns;
	}

}