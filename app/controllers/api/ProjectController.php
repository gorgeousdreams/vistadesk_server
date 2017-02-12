<?php

namespace API;

// REST services for tenants
class ProjectController extends \API\APIController {

	public function beforeView(&$project) {
		$project->company;
		$project->budgetRequests;
		$project->account;
		return $project;
	}

	public function getCompanyProjects($company_id) {
		$projects = \Project::where("company_id",$company_id)->get();
		if (count($projects) > 0) {
			return \Response::json($projects, 201);
		} else {
			return \Response::json(['message' => ["error" => "No project in this company"]], 400);
		}

	}

	
}



