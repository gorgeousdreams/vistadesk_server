<?php
class Address extends AppModel
{
    
    protected $fillable = ['street1','street2','city','state','postal','country'];
    
    public static function getAddValidation() {
        $validation = array(
            'street1'              => 'required',
            'city'              => 'required',
            'postal'            =>  'required|digits:5',
        );
        return $validation;
    }
}