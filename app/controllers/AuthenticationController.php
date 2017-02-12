<?php

class AuthenticationController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	private static function userFieldsFilling() 
	{
		Auth::user()->profile;
		Auth::user()->roles;
		Auth::user()->permissions;
		Auth::user()->profile->employee;
		Auth::user()->profile->company;
//			Auth::user()->profile->employee->employeeDocuments;
		Auth::user()->tenant;
		Auth::user()->tenant->address;
		if (Auth::user()->hasRole('Admin')) {
			$timesheetReminder = TimesheetReminder::where('user_id',  Auth::user()->id)->first();
			if ($timesheetReminder) {
				Auth::user()->timesheetReminder_id = $timesheetReminder->id; 
			}
		}
	}

	public function index()
	{
		if (Auth::check()) {
			self::userFieldsFilling();
			return Response::json([
				'user' => Auth::user()->toArray()],
				202
				);

		} else {
			return Response::json([
				'message' => 'Authentication failed'],
				401
				);

		}
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Creates a new tenant account and sends a welcome/activation email. Invoke with:
	 * { 
	 * "companyname": "Acme Corp",
	 * "fullname": "John Smith",
	 * "password": "12345",
	 * "email": "jsmith@acmecorp.com"
	 * }
	 *
	 */
	public function postRegister() {
		$input = Input::json()->all();

		// Validate inputs

		$validationResult = self::validate($input, [
			'email' => 'required|unique:users,username',
			'fullname' => 'required',
			'password' => 'required',
			'companyname' => 'required'
			]);
		if ($validationResult !== true ) return $validationResult;
		if ($input["companyname"] == 'Tenant Template') return self::error400("Unsupported company name. You get an 'E' for effort though!");

		// All valid input, ready to go!

		// Fill/create domain objects
		$nameSegments = explode(" ", $input["fullname"]);

		$firstName = $nameSegments[0];
		$lastName = "";
		for ($i = 1; $i < sizeof($nameSegments); $i++) {
			if ($i > 1) $lastName .= " ";
			$lastName .= $nameSegments[$i];
		}

		$tenant = Tenant::createNewTenant($input["companyname"], $input["fullname"], $input["email"]);
		$user = User::createNewUser($firstName, $lastName, $input["email"], $input["password"], $tenant->id, "Pending");

		// Send activation email

		$userToken = UserToken::createUserToken($user,new DateInterval('P7D'),'activation');

		self::sendNewTenantWelcomeEmail($tenant, $user->profile, $userToken);

		return Response::json([
			'user' => $user->toArray(), 'tenant' => $tenant],			
			201
			);
	}

	public function getActivate($token = null) {
		$userToken = \UserToken::where('token', '=', $token)->where('token_type', '=', 'activation')->first();
		if ($userToken == null) return self::error400("Invalid token.");
		
		$user = \User::where('id', '=', $userToken->user_id)->first();
		if ($user == null) return self::error400("Invalid token - unable to locate user record.");

		$user->status = 'Active';
		$user->save();
//		$userToken->delete();

		Auth::loginUsingId($user->id);
		self::userFieldsFilling();
		return Response::json(Auth::user()->toArray(),
			202
			);

	}

	private static function sendNewTenantWelcomeEmail($tenant, $profile, $userToken) {
		$data = array(
			'link' => "http://".$_SERVER['SERVER_NAME']."/access/activate?t=".$userToken->token,
			'profile' => $profile,
			'tenant' => $tenant,
			'recipient' => $profile->first_name . ($profile->last_name == null ? '' : ' ' . $profile->last_name)
			);
		Mail::send('emails.addcompany', $data, function($message) use ($profile, $tenant) {
			$message->from('no-reply@vistadesk.com', 'VistaDesk');
			$message->to($profile->email, $profile->first_name." ".$profile->last_name)->subject('Welcome to VistaDesk!');
		});
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		MultiTenantScope::disable();
		$token = Input::get('token');

		if (Auth::attempt(array('username'=>Input::get('email'), 'password'=>Input::get('password')))) {
			if (!Auth::user()->status) {
				return Response::json([
					'message' => 'User disabled. Please contact administrator.'],
					401
					);
			}
			self::userFieldsFilling(); 
			if (Auth::user()->profile->employee && Auth::user()->profile->employee->employeeDocuments) {
				$needing_documents_filled = true;
				foreach (Auth::user()->profile->employee->employeeDocuments as $needing_document) {
					if (!$needing_document->filled) {
						$needing_documents_filled = false;
					}
				}
			}
			if (!empty(Auth::user()->profile->employee->onboarding)) {
				Auth::user()->filled_all = (
					Auth::user()->profile->employee->onboarding->basic_info && 
					Auth::user()->profile->employee->onboarding->contact_info &&
					$needing_documents_filled
					); 
			}

			return Response::json([
				'user' => Auth::user()->toArray()],
				202
				);
		} else {
			if (!$token) {
				return Response::json([
					'message' => 'Invalid login. Please try again.'],
					401
					);
			} else  {
				$set_session_token = true;
				if ($token == 'return_to_root') {
					$delete_session_token = false;
					$token = Session::get('return_from_mimic_to_root_token');
					Session::forget('return_from_mimic_to_root_token');
				}
				$userToken = UserToken::where('token', $token)->where('token_type', 'mimic')->first();

				if (empty($userToken->id)) {
					return Response::json(['message' => "user_token not found"], 401);
				}
				if ($set_session_token) {
					Session::put('return_from_mimic_to_root_token', 
						UserToken::where('user_id', 
							Auth::user()->id)->where('token_type', 'mimic')->first()->token
						);   
				}
                            //$return_token = UserToken::where('user_id', Auth::user()->id)->where('token_type', 'mimic')->first()->token;
				Auth::login(User::find($userToken->user_id));
				self::userFieldsFilling(); 
				Auth::user()->employee;
				if ($set_session_token) {
					Auth::user()->return_token = Array ('token' => 'return_to_root');
				}
				return Response::json([
					'user' => Auth::user()->toArray()],
					202
					);
			}
		}

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
