<?php
class Account extends AppModel
{
	public $adminSettings = array(
		'list'=>array(
			'fields'=>array('id', 'name'),
			'actions'=>array('add','view','edit')
			),
		'view'=>array(		
			'actions'=>array('edit')
			),

		'displayField' => 'name'
		);

	public function company() {
		return $this->belongsTo('Company');
	}

}