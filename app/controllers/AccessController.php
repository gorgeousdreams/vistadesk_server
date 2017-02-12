<?php

class AccessController extends \BaseController {

	public static $testvar = 0;

	public function getLogin() {
		return View::make('access.login');
	}

	public function showLogin() {
		return $this->getLogin();
	}

	public function doLogout() {
		Auth::logout(); // log the user out of our application
		return Redirect::to('login'); // redirect the user to the login screen
	}

	public function getTest() {
		dd(Setting::setValue('testval', 2));
	}

	public function getInsert() {
/*
	  		$user = new User();
		$user->username ='tester@ngdcorp.com';
		$user->status = 'Active';
		$user->password = Hash::make('test');
		$user->tenant_id = 1;
		$user->save();
*/	
	}

	/** Log the user in by uuid */
	public function getAxm($uuid = null) {
		if ($uuid != null) {

		}
		throw new Exception("Not authorized");
	}


	public function postSignin() {
		MultiTenantScope::disable();
		if (Auth::attempt(array('username'=>Input::get('email'), 'password'=>Input::get('password')))) {
			return Redirect::to('dashboard/home')->with('message', 'You are now logged in!');
		} else {
			Session::flash('flash_message', 'Username / password incorrect.');
			return Redirect::to('access/login')
			->with('message', 'Your username/password combination was incorrect')
			->withInput();
		}
	}

	public function getSyncWorklogs() {
	  //		for ($week = 0; $week < 8; $week++) {
	  //			$date = strtotime('2014-09-01 +'.$week." weeks");
		$date = strtotime('2014-10-26');
		foreach (Employee::all() as $employee) {
			Worklog::copyWorklogs($employee, date('Y-m-d', $date));
		}
			//		}
	}

} 