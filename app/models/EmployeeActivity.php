<?php
class EmployeeActivity extends Eloquent {
    public $timestamps = true;
    protected $fillable = ['employee_id','action_user_id','content','comment'];

    public function actionUser() {
        return $this->hasOne('User', "id", "action_user_id");
    }

    public function employee() {
        return $this->hasOne('Employee', "id", "employee_id");
    }

}