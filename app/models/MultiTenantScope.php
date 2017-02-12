<?php 

class MultiTenantScope implements \Illuminate\Database\Eloquent\ScopeInterface {

	/** Default to 0 (no tenant) so no results will be returned unless the tenant id set
	 * to a non-zero id, or "all"
	 */
	static public $tenantId = null;

	public static function disable() {
		MultiTenantScope::$tenantId = "all";
	}

	public static function getTenantId() {
		if (MultiTenantScope::$tenantId != null && MultiTenantScope::$tenantId == "all") return "all";
		// Null tenant (default) means use the current user.
		$currentUser = Auth::user();
		$tenant = MultiTenantScope::$tenantId;
		if ($tenant == null) {
			if ($currentUser == null) throw new Exception("Unable to obtain valid tenant, user is not logged in.");
			$tenant = $currentUser->tenant_id;			
		}
		// Non-null tenant means use the tenant id set
		else {
			$tenant = intval(MultiTenantScope::$tenantId);
		}
		return $tenant;
	}

	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function apply(\Illuminate\Database\Eloquent\Builder $builder)
	{
		$model = $builder->getModel();

                //die("<pre>".print_r($model->attributesToArray()["tenant_id"],1));
		
		if (MultiTenantScope::$tenantId != null && MultiTenantScope::$tenantId == "all") return;	// 'all' = no tenant check.

                // We check statement $model->attributesToArray()["tenant_id"] to check if we add new tenant
		if (isset($model->attributesToArray()["tenant_id"])) 
			$tenant = $model->attributesToArray()["tenant_id"]; 
		else  
			$tenant = MultiTenantScope::$tenantId;

		// Null tenant (default) means use the current user.
		$currentUser = Auth::user();
		if ($tenant == null) {
			if ($currentUser == null) throw new Exception("Unable to obtain valid tenant, user is not logged in.");
			$tenant = $currentUser->tenant_id;			
		}
		// Non-null tenant means use the tenant id set
		else {
			$tenant = intval(MultiTenantScope::$tenantId);
		}

		// Now apply this tenant check to the query builder.
                // If user role is "ROOT" - checking disable
		if (!($currentUser->hasRole('Root'))) {
			$builder->where("tenant_id", '=', $tenant);
		}
	}

	/**
	 * Remove the scope from the given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function remove(\Illuminate\Database\Eloquent\Builder $builder)
	{
		$column = "tenant_id";

		$query = $builder->getQuery();

		foreach ((array) $query->wheres as $key => $where)
		{
			if ($this->isTenantConstraint($where, $column))
			{
				$this->removeWhere($query, $key);
				$this->removeBinding($query, $key);
			}
		}
	}

	protected function removeWhere($query, $key)
	{
		unset($query->wheres[$key]);

		$query->wheres = array_values($query->wheres);
	}


	protected function removeBinding($query, $key)
	{
		$bindings = $query->getRawBindings()['where'];

		unset($bindings[$key]);

		$query->setBindings(array_values($bindings));
	}

	protected function isTenantConstraint(array $where, $column)
	{
		return $where['type'] == 'Basic' && $where['column'] == $column;
	}


}
