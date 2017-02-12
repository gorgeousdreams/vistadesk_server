<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


// Using a route group here so that the API can be versioned.
// The "auth401" is just like "auth", and does the same check for authentication
// but returns 401 instead of a login form if the user is not logged in.
Route::group(array('prefix' => 'api/v1', 'before' => 'auth401'), function()
{
Route::get('employees/locations', '\api\EmployeeController@getLocations');
Route::resource('employees', '\api\EmployeeController');
Route::get('companies/statement/{uuid}/{mode?}/{date?}', '\api\CompanyController@getStatement');
Route::get('companies/invoice/{uuid}/{startDate}/{endDate}', '\api\CompanyController@getInvoice');
Route::resource('companies', '\api\CompanyController');
Route::controller('projects-ctrl', '\api\ProjectController');
Route::resource('projects', '\api\ProjectController');
Route::controller('resource-assignments-ctrl', '\api\ResourceAssignmentController');
Route::resource('resource-assignments', '\api\ResourceAssignmentController');
Route::resource('cashflow-plan-items', '\api\CashflowPlanItemController');
Route::resource('cashflow-plan-entries', '\api\CashflowPlanEntryController');
Route::resource('user-profile', '\api\ProfileController');
Route::resource('tenants', '\api\TenantController');
Route::resource('roles', '\api\RoleController');
Route::resource('permissions', '\api\PermissionController');
Route::resource('documents', '\api\DocumentController');
Route::resource('modules', '\api\ModuleController');
Route::resource('moduleinstances', '\api\ModuleInstanceController');
Route::controller('timesheets', '\api\TimesheetController');
Route::resource('timesheets', '\api\TimesheetController');
Route::controller('estimates', '\api\EstimateController');
Route::resource('estimates', '\api\EstimateController');
Route::resource('timesheet-reminders', '\api\TimesheetReminderController');
Route::controller('financials', '\api\FinancialsController');
Route::controller('clientapi', '\api\ClientController');
Route::controller('payroll', '\api\PayrollController');
Route::resource('billing-entries', '\api\BillingEntryController');
Route::get('search', '\api\SearchController@getIndex');

Route::controller('document-fields', '\api\DocumentFieldController');
Route::controller('onboarding', '\api\OnboardingController');

Route::controller('profile-image', '\api\ProfileImageController');

Route::controller('resources', '\api\ResourceController');
Route::resource('resources', '\api\ResourceController');

Route::controller('employees-action', '\api\EmployeeActionController');
Route::controller('employee-activity', '\api\EmployeeActivityController');
});

Route::controller('employees-action', '\api\EmployeeActionController');
Route::controller('company-estimates', '\api\CompanyEstimatesController');
Route::controller('resetpassword', '\api\ResetPasswordController');

// Why is everything in here twice?
Route::group(array('prefix' => 'api/v1',), function()
{
Route::controller('employees', '\api\EmployeeController');
Route::resource('employees', '\api\EmployeeController');
Route::resource('companies', '\api\CompanyController');
Route::resource('tenants', '\api\TenantController');
Route::resource('documents', '\api\DocumentController');

Route::controller('pto', '\api\PtoController');
Route::resource('pto', '\api\PtoController');
});

Route::controller('authenticate', 'AuthenticationController');
Route::resource('authenticate', 'AuthenticationController');

/*
Removed for testing
Route::controller('admin/accounts', 'AccountController');
Route::controller('admin/billing-entries', 'BillingEntryController');
Route::controller('admin/budget-requests', 'BudgetRequestController');
Route::controller('admin/companies', 'CompanyController');
Route::controller('admin/employees', 'EmployeeController');
Route::controller('admin/projects', 'ProjectController');
Route::controller('admin/resources', 'ResourceController');
Route::controller('admin/resource-rates', 'ResourceRateController');
Route::controller('access', 'AccessController');
*/
Route::controller('statements', 'StatementsController');
Route::controller('timesheets', 'TimesheetController');

Route::controller('test','TestController');

// FIXME: REMOVE THIS NEXT LINE
Route::controller('schedule', 'ScheduleController');


Route::controller('core', 'CoreController');
Route::controller('dashboard', 'DashboardController');
Route::controller('payroll', 'PayrollController');

Route::get('login', array('uses' => 'AccessController@showLogin'));
Route::get('logout', array('uses' => 'AccessController@doLogout'));
/*Route::get('/', 'DashboardController@getHome');*/
/* For testing only... */
Route::get('/', function()
	   {
	     return Redirect::to('/index.html');
	   });



Route::when('admin/*', 'auth|admin');
Route::when('api/*', 'auth401');
Route::when('api/*', 'auth401');
/*
Route::when('/', 'auth|admin');
Route::when('dashboard/*', 'auth|admin');
*/
//Route::controller('users', 'UserController');


