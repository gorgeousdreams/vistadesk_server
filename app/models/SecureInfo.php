<?php
class SecureInfo extends EncryptedModel 
{
    protected $table = 'secure_info';
    protected $fillable = array('ssn', 'bank_account', 'bank_routing', 'bank_account_type', 'fein');    
    protected $encrypt = array('ssn','bank_account','bank_routing', 'fein');

    public $timestamps = false;
    
    public function employee() {
        return $this->belongsTo('Employee');
    }

}