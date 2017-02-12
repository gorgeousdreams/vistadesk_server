<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	public function error400($message) {
		return \Response::json(['message' => $message], 400);
	}

	public function beforeSave(&$model) {
		return $model;
	}

	public function afterSave(&$model) {
		return $model;
	}

	public function beforeView(&$model) {
		return $model;
	}
	
	public function beforeList(&$list) {
		return $list;
	}

	public function save(&$model) {
		if( $model->save()) {
			return $model;
		}
		else {
			return \Response::json(['meta' => ['message' => "Invalid Data", 'errors'=>$model->errors()]], 400);
		}
	}

	public function validate($input, $rules, $messages = null) {
		if ($messages == null) $validator = Validator::make($input, $rules);
		else $validator = Validator::make($input, $rules, $messages);
		if ($validator->fails()) {
			return Response::json([
				'errors' => [$validator->messages()],
				], 400);
		}
		return true;
	}

}
