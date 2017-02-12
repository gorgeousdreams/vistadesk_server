<?php
class Tenant extends AppModel
{
	protected $fillable = ['name','contact_name','contact_email', 'contact_phone','number_of_employees','number_of_contractors'];

	public function address() {
		return $this->belongsTo('Address');
	}

	public function users() {
		return $this->hasMany('User');
	}

	public static function getAddValidation() {
		$validation = array(
			'name'                  => 'required|unique:tenants'
			);
		return $validation;
	}

	public static function validation() {
		$validation = array(
			'name'              => 'required|unique:tenants',
			);
		return $validation;
	}

	public static function createNewTenant($companyName, $contactName, $contactEmail) {
		MultiTenantScope::disable();
		$address = new Address();
		$address->address_type = 2; 	// Commercial
		$address->status = 1;			// Active 
		$address->save();
		$tenant = new Tenant();
		$tenant->name = $companyName;
		$tenant->contact_name = $contactName;
		$tenant->contact_email = $contactEmail;
		$tenant->address_id = $address->id;
		$tenant->save();

		Tenant::copyTemplateToTenant($tenant->id);

		$tenantSettings = new TenantSettings;
		$tenantSettings->payperiod_start = date('Y-m-d', strtotime('last sunday'));
		$tenantSettings->payperiod_frequency = 'Biweekly';
		$tenantSettings->default_billing_frequency = 'Monthly';
		$tenantSettings->default_billing_payable_days = '30';
		$tenantSettings->tenant_id = $tenant->id;
		$tenantSettings->save();

		return $tenant;
	}

	public static function copyTemplateToTenant($tenantId) {
		MultiTenantScope::disable();
		$tpl = Tenant::where('name', '=', 'Tenant Template')->first();
		if ($tpl != null) {
			$roles = Role::where('tenant_id', '=', $tpl->id)->get();
			foreach ($roles as $trole) {
				$role = new Role();
				$role->name = $trole->name;
				$role->tenant_id = $tenantId;
				$role->save();
			}
		}
	}


	public function inTenantScope() {

		if (MultiTenantScope::$tenantId != null && MultiTenantScope::$tenantId == "all") return true;	// 'all' = no tenant check.

		$tenant = MultiTenantScope::$tenantId;
		// Null tenant (default) means use the current user.
		$currentUser = Auth::user();
                // Root user can access all tenants
		if ($currentUser->hasRole('Root')) {
			return true;
		}
		if ($tenant == null) {
			
			if ($currentUser == null) throw new Exception("Unable to obtain valid tenant, user is not logged in.");
			$tenant = $currentUser->tenant_id;
		}
		// Non-null tenant means use the tenant id set
		else {
			$tenant = intval(MultiTenantScope::$tenantId);
		}
		return ($tenant == $this->id);
	}
	
	public function tenantSettings() {
		return $this->hasOne('TenantSettings');
	}

}