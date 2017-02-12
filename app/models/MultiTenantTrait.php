<?php

trait MultiTenantTrait {

    /**
     * Boot the multitenant trait for a model.
     *
     * @return void
     */
    public static function bootMultiTenantTrait()
    {
        static::addGlobalScope(new MultiTenantScope);
    }

	public function inTenantScope() {
		if (MultiTenantScope::$tenantId != null && MultiTenantScope::$tenantId == "all") return true;	// 'all' = no tenant check.

		$tenant = MultiTenantScope::$tenantId;
		// Null tenant (default) means use the current user.
		if ($tenant == null) {
			$currentUser = Auth::user();
			if ($currentUser == null) throw new Exception("Unable to obtain valid tenant, user is not logged in.");
			$tenant = $currentUser->tenant_id;
		}
		// Non-null tenant means use the tenant id set
		else {
			$tenant = intval(MultiTenantScope::$tenantId);
		}
		return ($tenant == $this->tenant_id);
	}


}