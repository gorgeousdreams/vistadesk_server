<?php

namespace API;

// REST services for tenants
class SearchController extends \BaseController {

	public function getIndex() {
		$companies = \Company::where('active','=',1);
		$q = isset($_GET['q']) ? $_GET['q'] : null;

		$result = new \stdClass;
		$result->companies = array();
		$result->employees = array();

		if ($q != null) {
			$searchTerms = explode(' ', $q);
			$query = \Company::where('active','=',1);
			foreach($searchTerms as $term)
			{
				$query->where('name', 'LIKE', '%'. $term .'%');
			}
			$result->companies = $query->get();

			

			$result->employees = array();
			foreach($searchTerms as $term)
			{
				$emps = \DB::select( \DB::raw("select e.id from employees e, profiles p where e.tenant_id = '".\MultiTenantScope::getTenantId()."' and e.status != 'Inactive' and p.id = e.profile_id and (p.first_name like :first_name OR p.last_name like :last_name)"), array('first_name'=>('%'.$term.'%'), 'last_name'=>('%'.$term.'%')));
				foreach ($emps as $emp) {
					$e = \Employee::find($emp->id);
					$e->profile;
					$result->employees[] = $e;
				}
			}

/*	The code below did not work and was replaced with the crap above. 
	The below code resulted in a query with the wrong precedence on the "orWhere":

	where profile.id = ? and first_name like '%' or last_name like '%'
	when it should have been:
	where profile.id = ? and (first_name like '%' or last_name like '%')

*/
/*		    $query = \Employee::where('Status','=','Active');
			foreach($searchTerms as $term)
			{
			  $query->whereHas('profile', function($query) use ($term) {
					$query->where(('last_name', 'LIKE', '%'. $term .'%')
					->orWhere('first_name', 'LIKE', '%'. $term .'%');
				});
			}
			$result->employees = $query->get();
*/
		}
		return \Response::json($result);
	}
	
}


