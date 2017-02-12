<?php

namespace API;

class RoleController extends \API\APIController {

	// Roles cannot be multi-tenant objects because they need to be loaded before we know what tenant the user is in.
	// So we do this instead...
	public function beforeList(&$roles) {
		$roleArray = array();
		foreach ($roles as $key=>$role) {
			if ($role->tenant_id == \MultiTenantScope::getTenantId()) {
				array_push($roleArray, $role);
			}
		}
		$roles->setItems($roleArray);
		return $roles;
	}

}



