<?php
class BudgetRequest extends AppModel
{
	public $timestamps = true;

	public $adminSettings = array(
		'list'=>array(
			      'fields'=>array('id', 'created_at', 'summary', 'project', 'amount'),			
			),
		'displayField' => 'summary',
		'edit'=>array(
			'fields'=>array('project_id','summary', 'amount', 'description')
			),
		'formats' => array(
			'amount' => 'currency',
			'description' => 'html'
			)
		);

	// https://github.com/laravelbook/ardent
	public static $rules = array(
        'description' => 'required',		// |alpha|min:3  etc...
        'summary' => 'required',
        'amount'  => 'required|numeric'		// Whoever thought | should be used instead of & for "and" was a jackass. 
        // .. more rules here ..
        );

	public function project() {
		return $this->belongsTo('Project');
	}

	public function creator() {
		return $this->belongsTo('User');
	}

}