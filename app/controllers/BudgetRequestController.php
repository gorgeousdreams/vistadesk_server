<?php

class BudgetRequestController extends \BaseController {
	use ScaffoldController;		// Add the scaffolding actions for quick & easy CRUD

	public function beforeSave(&$entry) {
		if ($entry->user_id == 0) {
			$entry->user_id = Auth::user()->id;
		}
	}


} 