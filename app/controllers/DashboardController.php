<?php

class DashboardController extends \BaseController {

	public function getHome() {
		return View::make('dashboard.home');
	}

	public function getStats($startDate=null, $endDate=null) {
	       
	  if ($startDate == null) {
	    $startDate = 'last sunday -30 days';
	  }
	  if ($endDate == null) {
	    $endDate = 'last sunday';
	  }

		if(!\Auth::user()->hasRole('finance')) {
			return \Response::json(['meta' => ['message' => "Not Authorized"]], 403);
		}

		return Response::json(array(

			'lastThirty' => \Dashboard::getFinancials($startDate, $endDate),
			'lastNinety' => \Dashboard::getFinancials('last sunday -90 days', 'last sunday'),
			'projects' => \Dashboard::getProjects('last sunday -30 days', 'last sunday')


			),
		200
		);
	}

	public function getTest() {
		return Employee::all();
	}

} 