<?php
class Resource extends AppModel
{

	use MultiTenantTrait;

	public $adminSettings = array(
		// Field-level authorization, array of fields and the roles that can view/edit those fields.
		'acl' => array('base_rate' => 'finance,admin'),
		// Formatting for fields. Possible formats include date, currency, number
		'formats'=>array('base_rate'=>'currency'),
		'displayField' => 'name'
		);

	function employees() {
		return $this->hasMany('Employee');
	}

	public function resourceRates() {
		return $this->hasMany('ResourceRate');
	}


}