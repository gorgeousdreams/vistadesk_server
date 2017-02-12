<?php
class UserProfile extends Eloquent {
    public $timestamps = false;
    
    protected $fillable = ['email','first_name','last_name','date_of_birth','gender'];
    
    public static function getAddValidation() {
        $validation = array(
            'email'              => 'required|email',
            'first_name'         => 'required',
            'last_name'          => 'required',
            'gender'             => 'required|in:M,F',
            'date_of_birth'      => 'required|date',
        );
        return $validation;
    }
        
    public function address() {
        return $this->belongsTo('Address');
    }
}