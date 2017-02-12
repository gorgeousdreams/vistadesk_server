<?php
class Document extends Eloquent {
    protected $table = 'documents';
    
    public function documentField() {
        return $this->belongsToMany('DocumentField','document_document_fields');
    }
    
    public function employeeDocuments() {
	return $this->hasMany('EmployeeDocument');
    }
}