<?php

namespace API;

// REST services for clients, which allows for fine-grained control over data visibility.
 class ClientController extends \API\APIController { 	

 	public function getProjects() {
		if(!\Auth::user()->profile->company != null) {
			return \Response::json(['meta' => ['message' => "Not Authorized - API is for client users only"]], 403);
		}


		return \Response::json($this->arrayToPaginationObject(\Client::getProjects(\Auth::user()->profile->company->id)));


 	}

 	public function getProjectOverage($projectId) {
 		if(!\Auth::user()->hasRole('finance')) {
 			return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);
 		}

 		return \Response::json(\Project::getProjectOverageEntries($projectId));

 	}

 	

 }



