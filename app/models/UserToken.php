<?php
class UserToken extends Eloquent {
        
    public static function createUserToken($user, $expireTime, $tokenType) {
        $userToken = new UserToken;
        if ($expireTime) {
            $liveTime = new \DateTime(); 
            $liveTime->add($expireTime);
            $userToken->expires_at = $liveTime->format('Y-m-d H:i:s');
        }
        $userToken->token = generateUUID();
        $userToken->token_type = $tokenType;
        $userToken->user_id = $user->id;
        $userToken->save();
        return $userToken;
    }	
    

    public function user() {
        return $this->belongsTo('User');
    }
}