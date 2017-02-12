<?php
class Setting extends AppModel
{

	public static function getValue($settingName, $tenantId = null, $defaultValue = null) {
		$setting = Setting::where('name', '=', $settingName)->where('tenant_id','=', $tenantId)->first();
		return $setting == null ? null : $setting->value;
	}

	public static function setValue($settingName, $value, $tenantId = null) {
		$setting = Setting::where('name', '=', $settingName)->first();
		if ($setting == null) {
			$setting = new Setting();
		}

		$setting->name = $settingName;
		$setting->value = $value;
		$setting->tenant_id = $tenantId;

		$setting->save();
	}

}