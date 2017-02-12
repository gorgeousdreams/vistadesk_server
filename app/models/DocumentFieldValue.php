<?php
class DocumentFieldValue extends Eloquent {

    protected $table = 'document_field_values';
    
    protected $fillable = ['document_field_id','employee_id','value'];
    
    
    public function documentField() {
	return $this->belongsTo('DocumentField', 'document_field_id');
    }
    
    public function employee() {
	return $this->belongsTo('Employee', 'employee_id');
    }
    
}