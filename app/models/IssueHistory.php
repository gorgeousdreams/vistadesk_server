<?php
class IssueHistory extends AppModel
{

	protected $table = 'issue_history';

	public function issue() {
		return $this->belongsTo('Issue', 'issue_id');
	}

}