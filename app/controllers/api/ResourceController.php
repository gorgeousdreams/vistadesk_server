<?php

namespace API;

class ResourceController extends \API\APIController {

	public function getResourceAssignments($companyId = null) {
		$company = \Company::find($companyId);
		// Verify tenant
		if ($company == null) return \Response::json(['meta' => ['message' => "Company not Found"]], 404);

		$resources = \ResourceAssignment::with('company', 'employee', 'employee.profile')->where('company_id','=', $companyId)->get();

		$this->applyACLToList(new \ResourceAssignment(), $resources, isset($class->adminSettings['acl']) ? $class->adminSettings['acl'] : null);

		return \Response::json($resources);
	}

}



