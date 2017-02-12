<?php
class TenantSettings extends AppModel {

	use MultiTenantTrait;
	protected $fillable = array('default_billing_frequency', 'default_billing_payable_days');

}