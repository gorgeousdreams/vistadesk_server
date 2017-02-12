<?php
class TimesheetReminder extends AppModel {
    public $timestamps = true;
    
    protected $fillable = ['grace_period','email_content','email_cc'];
    
    public function tenant() {
        return $this->belongsTo('Tenant');
    }
    
    public function user() {
        return $this->belongsTo('User');
    }
    
    public static function getAddValidation() {
        
        Validator::extend('multipleEmails', function($attribute, $value, $parameters)
        {
            if (empty($value) || $value == '' || $value == null) {
                return true;
            }
            $emailRegexp = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/i";
            if (preg_match($emailRegexp, $value)) {
                return true;
            }
            $separators = array(';',',',' ');
            $isElementsInArray = false;
            foreach ($separators as $separator) {
                $valueArray = explode($separator, $value);
                if (count($valueArray)>1) {
                    $isElementsInArray = true;
                    foreach ($valueArray as $valueArrayElement) {
                        if (!preg_match($emailRegexp, $valueArrayElement)) {
                            return false;
                        }
                    }
                }
            }
            if ($isElementsInArray) { 
                return true; 
            } else {
                return false; 
            }
        });
        
        $validation = array(
            'grace_period'          => 'required|min:0|max:14',
            'email_content'         => 'required',
            'email_cc'              => 'required|multipleEmails'
            );
        return $validation;
    }
    
    public static $validationMessages = array('email_cc.multiple_emails' => 'Not correct multiple emails');

}