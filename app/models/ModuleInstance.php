<?php
class ModuleInstance extends AppModel
{	
    protected $table = 'module_instances';

    public function module() {
        return $this->hasOne('Module', "id", "module_id");
    }

}
