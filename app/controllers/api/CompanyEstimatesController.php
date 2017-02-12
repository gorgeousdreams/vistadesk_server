<?php

namespace API;

class CompanyEstimatesController extends \BaseController {
	/**
     * Authentification by company token
     * @param string $token
	 * @return array  "companyId" - company id in succesful auth and false if fail, "errorMessage" error message
   */


	private static function CompanyTokenAuth ($token) {
		if (empty($token)) {
            return array("companyId"=> false, "errorMessage" => ['message' => ['error' => "token is empty"]]);
        }

		$companyToken = \CompanyToken::where('token', '=', $token)->first();
		if (empty($companyToken->id)) {
			return array("companyId"=> false, "errorMessage" => ['message' => ['error' => "token not found"]]);
        }
		if (!empty($companyToken->expires_at)) {
			$currentTime = new \DateTime();
			$liveTime = new \DateTime($companyToken->expires_at);
			if ($currentTime > $liveTime) {
				return array("companyId"=> false, "errorMessage" => ['message' => ['error' => "token expired"]]);
			}
		}
		return array("companyId"=> $companyToken->company_id);
	}

	public function postEstimatedIssues() {

		$input = \Input::json()->all();
		$companyTokenAuth = Self::CompanyTokenAuth($input['token']);
		if ($companyTokenAuth["companyId"] === false) {
			return \Response::json($companyTokenAuth["errorMessage"] , 400);
		} else {
			$companyId = $companyTokenAuth["companyId"];
		}
		\MultiTenantScope::disable();
		$company = \Company::find($companyId);
		$companyProjects = $company->projects()->get();
		$counter = 0;
		foreach ($companyProjects as $projectKey => $project) {
			$issues = \Issue::where('project_id','=',$project->id)
						->whereNotNull('estimate')
						->where('estimate',"<>",'0')
						->whereHas('IssueHistory', function($q)
						{
							$q->where('entry_type', '=', 'ManagerApproved');
						})
						->get();
			foreach ($issues as $issueKey => $issue) {
				$lastIssueHistory = $issue->lastIssueHistory();
				if ($lastIssueHistory->entry_type == 'ManagerApproved') {
					$issues[$issueKey]->issueHistory = $lastIssueHistory;
				} else {
					unset($issues[$issueKey]);
				}
			}
			if (empty($issues) || count($issues)<1) {
				unset($companyProjects[$projectKey]);
			} else {
				$companyProjects[$projectKey]->issues = $issues;
			}

		}
		return \Response::json(["company" => $company, "projects" => $companyProjects], 201);
	}

	public function postClientApproveIssuesList() {
		$input = \Input::json()->all();
		$companyTokenAuth = Self::CompanyTokenAuth($input['token']);
		if ($companyTokenAuth["companyId"] === false) {
			return \Response::json($companyTokenAuth["errorMessage"] , 400);
		} else {
			$companyId = $companyTokenAuth["companyId"];
		}
		\MultiTenantScope::disable();
		$company = \Company::find($companyId);
		$approvedIssueIds = $input['issues'];
		$currentDate = new \DateTime;
		$issueHistories = \IssueHistory::whereIn('id', $approvedIssueIds)->get();
		if (count($issueHistories ) < 1) {
			return \Response::json(['message' => ['error' => 'No issues to approve']], 400);
		}
		foreach ($issueHistories as $issueHistory) {
			$newIssueHistory = new \IssueHistory;
			$newIssueHistory->issue_id = $issueHistory->issue_id;
			$newIssueHistory->estimate = $issueHistory->estimate;
			$newIssueHistory->created_by = $company->billing_email;
			$newIssueHistory->entry_type = 'ClientApproved';
			$newIssueHistory->created_at = $currentDate->format('Y-m-d H:i:s');
			$newIssueHistory->save();
			\IssueHistory::where('id', $newIssueHistory->issue_id)->update(array('estimate' => $newIssueHistory->estimate));
		}

		$issueHistoriesArray = \IssueHistory::whereIn('id', $approvedIssueIds)->with('issue.project')->get()->toArray();
		$companyProjects = array_map("unserialize", array_unique(array_map("serialize", array_map( function($o) {
			return $o['issue']['project'];
		},$issueHistoriesArray))));
		foreach ($companyProjects as $projectKey => $project) {
			$issueHistoriesForProject = array_filter($issueHistoriesArray, function($a) use($project){
				return $a['issue']['project']['id']==$project['id'];
			});
			$companyProjects[$projectKey]['issueHistories'] = $issueHistoriesForProject;
		}
		$users = \User::admins()->where("tenant_id", $company->tenant_id)->get();
		Self::sendApproveNotification($company, $companyProjects, $users);
		return \Response::json(['message' => ['success' => 'issues were approved by client successfully'], "company" => $company, "projects" => $companyProjects], 201);
	}

		/**
     * Sending Emails with estimates data for managers, that client pprove estimate
     * @param array $company Array with company data
	 * @param array $projects Array with projects data
	 * @param array $users List of managers, which receive notification
    */
	private static function sendApproveNotification($company, $projects, $users) {
		if ($projects == null || !isset($projects) || count($projects) < 1) {
		 return;
		}
		if ($users == null || !isset($users) || count($users) < 1) {
		 return;
		}
		$projectsTitle = "";
		foreach ($projects as $project) {
		 $projectsTitle .= $project["name"]."; ";
		}
		$subject = "Estimates Approves from ".$company["name"]." on " . $projectsTitle;

		foreach ($users as $user) {
			if (empty($user['profile']['email'])) {
				$recips = "sasha@ngdcorp.com";
				$subject = "(NO Company EMAIL) ".$subject;
			} else {
				$recips = $user['profile']['email'];
			}

			$cc = null;
			$bcc = null;


			\Mail::send('emails.estimates.approve_client',
				array('company' => $company, 'projects' => $projects, 'user' => $user),
				function($message) use ($recips, $cc, $bcc, $subject) {
					$message->to($recips)->from("info@ngdcorp.com", "Northgate Digital")->subject($subject);
					if ($cc != null) $message->cc($cc);
					if ($bcc != null) $message->bcc($bcc);
			   });

		}
	 }

}


