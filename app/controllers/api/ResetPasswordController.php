<?php

namespace API;

// REST services for tenants
class ResetPasswordController extends \API\APIController {
	
    
    public function postRequest() {
        $input = \Input::json()->all();
        
        $user= \User::where('username', '=', $input['email'])->first();
        if (empty($user->id)) {
	    return \Response::json(['message' => ['error' => "No such user!"]], 400);
	}
        \UserToken::where("user_id",$user->id)->where("token_type",'pwreset')->delete();
        $userToken = \UserToken::createUserToken($user,new \DateInterval('P1D'),'pwreset');        
        $this->sendResetPasswordEmail($user, $userToken);
                
        return \Response::json([
			'message' => ['success' => "Reset password link successfully send!"]],			
			201
			);

    } 
    
    public function postReset() {
        $input = \Input::json()->all();
        if (empty($input['user']['token'])) {
            return \Response::json(['message' => ['error' => "user_token is empty"]], 400);
        }

        $userToken = \UserToken::where('token', '=', $input['user']['token'])->first();

        if (empty($userToken->id)) {
            return \Response::json(['message' => ['error' => "user_token not found"]], 400);
        }
        
        $currentTime = new \DateTime();
        $liveTime = new \DateTime($userToken->expires_at);
        if ($currentTime>$liveTime) {
            return \Response::json(['message' => ['error' => "user_token expired"]], 400);
        }
        
        if ($input['user']['password']!=$input['user']['password1']) {
            return \Response::json(['message' => ['error' => "passwords are not same"]], 400);
        }
        
        $validatorPassword = \Validator::make(
            array('password' => $input['user']['password']),
            array('password' => array('required', 'regex:((?=.*\d)(?=.*[a-z]).{6,20})'))
        );
        
        if ($validatorPassword->fails()) {
            return \Response::json(['message' => $validatorPassword->messages()], 400);
        }
        
        $user = \User::where('id', '=', $userToken->user_id)->first();
        $user->password = \Hash::make($input['user']['password']);
        $user->save();
        $userToken->delete();
        if (\Auth::attempt(array('username'=>$user->username, 'password'=>$input['user']['password']))) {
            return \Response::json(['message' => ['success' => 'password set successfully']], 201);	
        } else {
            return \Response::json(['message' => ['error' => "password set fail "]], 400);	
        }

    }

    
    private function sendResetPasswordEmail($user, $userToken) {
        $data = array(
            'link' => "http://".$_SERVER['SERVER_NAME']."/v3/index.html#/access/resetpass?resetpass=".$userToken->token,
            );
        \Mail::send('emails.resetpassword', $data, function($message) use ($user) {
            $message->from('admin@fusepath.com', 'FusePath');
            $message->to($user->username, $user->username)->subject('FusePath reset password');
        });
    }

}



