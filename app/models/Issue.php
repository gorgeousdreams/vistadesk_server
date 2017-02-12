<?php
class Issue extends AppModel
{

	public function project() {
		return $this->belongsTo('Project');
	}

	public function issueHistory() {
		return $this->hasMany('IssueHistory','issue_id');
	}

	public function lastIssueHistory() {
		return $this->hasMany('IssueHistory','issue_id')->orderBy('created_at','desc')->take(1)->first();
	}

}