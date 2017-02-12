<?php

namespace API;

// REST services for tenants
class TenantController extends \API\APIController {

    
//	public function getIndex($id = null) {
//		if (\Auth::user()->hasRole('Root')) {
//			return \Tenant::all();
//		} else {
//			return \Tenant::find(\Auth::user()->tenant_id);
//		}
//	}
	
	public function beforeList(&$list) {
		if (\MultiTenantScope::$tenantId != "all" && !(\Auth::user()->hasRole('Root'))) {
			return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);
		}
                // add mimic token to tenant list for Root 
                if (\Auth::user()->hasRole('Root')) {
                    $list_items=$list->getItems();
                    foreach ($list_items as $list_item) {
                        $user = \User::where('tenant_id','=',$list_item->id)->first();
                        if (!empty($user)) {
                            $list_item->mimic_token = $user->userTokens()->where('token_type','mimic')->first();
                        }
                    }
                    $list->setItems($list_items);
                }
		return $list;
	}
        

	public function beforeView(&$tenant) {
		$tenant->address;
		return $tenant;
	}

	public function save(&$tenant) {
		if ($tenant->id != \Auth::user()->tenant_id && !(\Auth::user()->hasRole('Root'))) {
			return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);			
		}
		$input = \Input::json()->all();
		$tenant->address->fill($input['address']);
		if ($tenant->push()) return $tenant;
		return \Response::json(['meta' => ['message' => "Invalid Data", 'errors'=>$object->errors()]], 400);
	}

	
}



