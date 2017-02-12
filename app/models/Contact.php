<?php

class Contact extends Eloquent {
    public $timestamps = false;
    
    public function profile() {
        return $this->belongsTo('Profile');
    }
    
}
