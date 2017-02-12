<?php
class EmployeeDocument extends Eloquent 
{
    protected $table = 'employee_documents';
    
    public $timestamps = false;
    
    public function employee() {
        return $this->belongsTo('Employee');
    }
    
    public function document() {
        return $this->belongsTo('Document');
    }
}