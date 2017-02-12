<?php

namespace API;

class EstimateController extends \API\APIController {

	public function index() {
		return $this-> getCompaniesForTenant();
	}


	public function getTest() {
		$issueHistoriesFull = \IssueHistory::with('issue.project.company');
		$comamyIdsArray = array_map("unserialize", array_unique(array_map("serialize", array_map(function($o) { return $o['issue']['project']['company']; }, $issueHistoriesFull->get()->toArray()))));
		return $comamyIdsArray;
	}

	public function getCompaniesForTenant() {
		$currentUser = \Auth::user();
		$companies = \Company::where('tenant_id','=',$currentUser->tenant_id)->where('active','=','1')->get(array('id', 'name', 'external_id', 'uuid'));
		foreach ($companies as $key => $company) {
			$companies[$key]->projects = $company->projects()->get();
			$counter = 0;
			foreach ($companies[$key]->projects as $projectKey => $project) {
				// Import all issues for each company's project
				$this->getProjectImport($project->external_id, $project->id);

				$maxRates = \ResourceAssignment::getMaxRates();

				$issues = \Issue::where('project_id','=',$project->id)
							->whereNotNull('estimate')
							->where('estimate',"<>",'0')
							->whereHas('IssueHistory', function($q)
							{
								$q->where('entry_type', '=', 'New');
							})
							->get();
				foreach ($issues as $issueKey => &$issue) {
					$issue->estimatedCost = 0;

					// Estimated cost = max rate for client, times the estimated hours, plus 20% for PM / QA.
					// FIXME / TODO: Make this 20% overhead a configurable setting

					if (array_key_exists($company->id, $maxRates)) {							
 							$issue->estimatedCost = (($issue->estimate / 3600) * $maxRates[$company->id] * 1.2);
					}
					
					$lastIssueHistory = $issue->lastIssueHistory();
					if ($lastIssueHistory->entry_type == 'New') {
						$issues[$issueKey]->issueHistory = $lastIssueHistory;
					} else {
						unset($issues[$issueKey]);
					}
				}
				if (empty($issues) || count($issues)<1) {
					unset($companies[$key]->projects[$projectKey]);
				} else {
					$companies[$key]->projects[$projectKey]->issues = $issues;
				}

			}
			if (empty($companies[$key]->projects) || count($companies[$key]->projects) < 1) {
				unset($companies[$key]);
			}
		}
		return \Response::json($companies, 201);
	}


	public function postManagerApproveIssues() {
		$input = \Input::json()->all();
		$approvedIssueIds = $input['issues'];
		$currentDate = new \DateTime;
		$currentUser = \Auth::user();
		$issueHistories = \IssueHistory::whereIn('id', $approvedIssueIds)
				->update(array('entry_type' => 'ManagerApproved', 'created_by' => $currentUser->username, 'created_at' => $currentDate->format('Y-m-d H:i:s')));
		if (count($issueHistories ) < 1) {
			return \Response::json(['message' => ['error' => 'No issues to approve']], 400);
		}
		$issueHistoriesArray = \IssueHistory::whereIn('id', $approvedIssueIds)->with('issue.project.company')->get()->toArray();
		$companies = array_map("unserialize", array_unique(array_map("serialize", array_map(function($o) { return $o['issue']['project']['company']; }, $issueHistoriesArray))));
		foreach ($companies as $companyKey => $company) {
			$issueHistoriesForCompany = array_filter($issueHistoriesArray, function($a) use($company){
			  return $a['issue']['project']['company']['id']==$company['id'];
			});
			$companyProjects = array_map("unserialize", array_unique(array_map("serialize", array_map( function($o) {
				unset ($o['issue']['project']['company']);
				return $o['issue']['project'];
			},$issueHistoriesForCompany))));
			foreach ($companyProjects as $projectKey => $project) {
				$issueHistoriesForProject = array_filter($issueHistoriesForCompany, function($a) use($project){
					return $a['issue']['project']['id']==$project['id'];
				});
				$companyProjects[$projectKey]['issueHistories'] = $issueHistoriesForProject;
			}
			$companies[$companyKey]['projects'] = $companyProjects;
			$companyToken = \CompanyToken::createCompanyToken($company['id'], new \DateInterval('P7D'));
			Self::sendApproveNotification($company, $companyProjects, $companyToken->token);

		}
		return \Response::json(['message' => ['success' => 'issues approved by manager successfully'], 'companies' => $companies], 201);
	}



	/**
     * Sending Emails with estimates data for company billing contacts
     * @param array $company Array with company data
	 * @param array $projects Array with projects data
	 * @param string $token
    */
	private static function sendApproveNotification($company, $projects, $token) {
		 if ($projects == null || !isset($projects) || count($projects) < 1) {
			 return;
		 }
		 $projectsTitle = "";
		 foreach ($projects as $project) {
			 $projectsTitle .= $project["name"]."; ";
		 }
		 $subject = "Estimates from NGD on " . $projectsTitle;
		 if (empty($company['billing_email'])) {
			 $recips = "sasha@ngdcorp.com";
			 $subject = "(NO Company EMAIL) ".$subject;
		 } else {
			 $recips = $company['billing_email'];
		 }

		 $cc = null;
		 $bcc = null;


		 \Mail::send('emails.estimates.approve',
			 array('company' => $company, 'projects' => $projects, 'token' => $token),
			 function($message) use ($recips, $cc, $bcc, $subject) {
				 $message->to($recips)->from("info@ngdcorp.com", "Northgate Digital")->subject($subject);
				 if ($cc != null) $message->cc($cc);
				 if ($bcc != null) $message->bcc($bcc);
			 });
	 }

/**
     * Import projects data from Jira database
     * @param int $projectExternalId
	 * @param int $projectId
    */

	private function getProjectImport($projectExternalId, $projectId) {
		\MultiTenantScope::$tenantId = "all";
		$issues = \Jira::getActiveIssuesForProject($projectExternalId);
		if (!empty($issues)) {
			foreach ($issues as $key => $issueItem) {
				$checkIssue = \Issue::where('external_id','=',$issueItem->ID)->first();
				if ($checkIssue == null) {
					$issue = new \Issue();

					$issue->external_id = $issueItem->ID;
					$issue->pkey = $issueItem->pkey;
					$issue->project_id = $projectId;
					$issue->summary = $issueItem->SUMMARY;
					$issue->description = $issueItem->DESCRIPTION;
					$issue->estimate = $issueItem->TIMEORIGINALESTIMATE;
					$issue->save();

					$issueHistory = new \IssueHistory();
					$issueHistory->issue_id = $issue->id;
					$issueHistory->created_by = $issueItem->REPORTER;
					$currentDate = new \DateTime;
					$issueHistory->created_at = $currentDate->format('Y-m-d H:i:s');
					$issueHistory->estimate = $issueItem->TIMEORIGINALESTIMATE;
					$issueHistory->save();

				} else {
					if ($issueItem->TIMEORIGINALESTIMATE > $checkIssue->lastIssueHistory()->estimate) {
						$issueHistory = new \IssueHistory;
						$issueHistory->issue_id = $checkIssue->id;
						$issueHistory->created_by = $issueItem->REPORTER;
						$currentDate = new \DateTime;
						$issueHistory->created_at = $currentDate->format('Y-m-d H:i:s');
						$issueHistory->estimate = $issueItem->TIMEORIGINALESTIMATE;
						$issueHistory->save();
					}
				}
			}

			return true;
		} else {
			return false;
		}
	}


}