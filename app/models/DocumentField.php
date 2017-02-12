<?php

class DocumentField extends Eloquent {
    
    public function document() {
        return $this->belongsToMany('Document','document_document_fields');
    }
    
    public function documentFieldValue() {
	return $this->hasMany('DocumentFieldValue');
    }
    
}
