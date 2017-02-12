<?php
class Role extends Eloquent {
	//use MultiTenantTrait;			// Only allow users to see instances for their tenant id
    protected $fillable = array('name');
}