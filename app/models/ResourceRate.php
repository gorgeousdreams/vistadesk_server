<?php
class ResourceRate extends AppModel
{

	public $adminSettings = array(
		// Field-level authorization, array of fields and the roles that can view/edit those fields.
		'acl' => array('rate' => 'finance,admin'),
		// Formatting for fields. Possible formats include date, currency, number
		'formats'=>array('rate'=>'currency')

		);

	public function company() {
		return $this->belongsTo('Company');
	}

	public function resource() {
		return $this->belongsTo('Resource');
	}


}