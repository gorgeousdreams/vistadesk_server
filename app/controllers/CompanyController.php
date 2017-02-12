<?php

class CompanyController extends \BaseController {
	use ScaffoldController;		// Add the scaffolding actions for quick & easy CRUD

	public function getIndex() {
		if( Authority::can('manage', 'Company') ) {
			$className = $this->getClassForController();
			$companies = Company::queryBuilderWithFunds()
			->where('companies.active', '=', 1)
			->orderBy('companies.name', 'ASC')
			->paginate(25);

			return View::make('scaffold.index')
			->with('objects', $companies)	
			->with('class', new $className());
		} else {
			throw new Exception("Not authorized");
		}
	}

} 