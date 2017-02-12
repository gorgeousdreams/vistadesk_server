<?php namespace Webdesk\Composers;

class DashboardRevenueComposer {

	public function compose($view)
	{
	if (\Auth::check() && \Auth::user()->hasRole('Admin')) {
		$view
		->with('lastThirty', \Dashboard::getFinancials('last sunday -30 days', 'last sunday'))
		->with('lastNinety', \Dashboard::getFinancials('last sunday -90 days', 'last sunday'))
		->with('projects', \Dashboard::getProjects('last sunday -30 days', 'last sunday'));
	}
	else {
		$view->with('lastThirty', null)->with('lastNinety', null);
	}
	}

}