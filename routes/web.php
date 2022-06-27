<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Reports
Route::group(['prefix' => 'reports'], function () {
	Route::get('/employee-list', [
		'uses' => 'ReportController@employee_list',
		'as' => 'report.employee_list',
	]);

	Route::get('/employee-per-position', [
		'uses' => 'ReportController@employee_per_position',
		'as' => 'report.employee_per_position',
	]);

	// for print
	Route::get('/employee-list-print', [
		'uses' => 'ReportController@employee_list_print',
		'as' => 'report.employee_list_print',
	]);

	Route::get('/employee-per-position-print', [
		'uses' => 'ReportController@employee_per_position_print',
		'as' => 'report.employee_per_position_print',
	]);
});

// Purchasing Reports
Route::group(['prefix' => 'purchasing-reports'], function () {
	// ADMIN
	Route::get('/index', [
		'uses' => 'PurchasingReportController@index',
		'as' => 'report.purchasing.index',
	]);
	Route::get('/create', [
		'uses' => 'PurchasingReportController@create',
		'as' => 'report.purchasing.create',
	]);
	Route::post('/store', [
		'uses' => 'PurchasingReportController@store',
		'as' => 'report.purchasing.store',
	]);
	Route::get('{id}/edit', [
		'uses' => 'PurchasingReportController@edit',
		'as' => 'report.purchasing.edit',
	]);
	Route::post('{id}/update', [
		'uses' => 'PurchasingReportController@update',
		'as' => 'report.purchasing.update',
	]);
	Route::get('{id}/trash', [
		'uses' => 'PurchasingReportController@trash',
		'as' => 'report.purchasing.trash',
	]);
	Route::post('{id}/delete', [
		'uses' => 'PurchasingReportController@delete',
		'as' => 'report.purchasing.delete',
	]);

	// AJAX
	Route::post('{id}/{file}/download', [
			'uses' => 'PurchasingReportController@download',
			'as' => 'report.purchasing.download',
		]);
	Route::get('{id}/edit-ajax', [
			'uses' => 'PurchasingReportController@edit_ajax',
			'as' => 'report.purchasing.edit-ajax',
		]);

	Route::get('{id}/subreport', [
		'uses' => 'PurchasingReportController@subreport',
		'as' => 'report.purchasing.subreport',
	]);

	// USER
	Route::get('/view', [
		'uses' => 'PurchasingReportController@view',
		'as' => 'report.purchasing.view',
	]);

	Route::get('{id}/view-subreport', [
		'uses' => 'PurchasingReportController@view_subreport',
		'as' => 'report.purchasing.view_subreport',
	]);
});

// Route::resource('permissions', 'PermissionController');
// Route::resource('roles', 'RoleController');

Route::post('/test', function (Request $req) {
	return $req->all();
})->name('test');

Route::group(['prefix' => 'users/authorizations'], function () {
  Route::get('/index', [
      'uses' => 'AuthorizationController@index',
      'as' => 'authorizations.index',
    ]);
  Route::get('{id}/edit/', [
      'uses' => 'AuthorizationController@edit',
      'as' => 'authorization.edit',
    ]);
  Route::post('{id}/update/', [
      'uses' => 'AuthorizationController@update',
      'as' => 'authorization.update',
    ]);
	Route::get('/assign', [
			'uses' => 'AuthorizationController@assign',
			'as' => 'authorization.assign',
		]);
	Route::post('/assign_proceed', [
			'uses' => 'AuthorizationController@assign_proceed',
			'as' => 'authorization.assign_proceed',
		]);
});

Route::group(['prefix' => 'permissions'], function () {
  Route::get('/index', [
      'uses' => 'PermissionController@index',
      'as' => 'permissions.index',
    ]);
  Route::get('/create', [
      'uses' => 'PermissionController@create',
      'as' => 'permissions.create',
    ]);
  Route::post('/store', [
      'uses' => 'PermissionController@store',
      'as' => 'permissions.store',
    ]);
  Route::get('{id}/edit/', [
      'uses' => 'PermissionController@edit',
      'as' => 'permissions.edit',
    ]);
  Route::post('{id}/update/', [
      'uses' => 'PermissionController@update',
      'as' => 'permissions.update',
    ]);
  Route::get('{id}/trash/', [
      'uses' => 'PermissionController@trash',
      'as' => 'permissions.trash',
    ]);
  Route::post('{id}/destroy/', [
      'uses' => 'PermissionController@destroy',
      'as' => 'permissions.destroy',
    ]);
});

Route::group(['prefix' => 'roles'], function () {
  Route::get('/index', [
      'uses' => 'RoleController@index',
      'as' => 'roles.index',
    ]);
  Route::get('/create', [
      'uses' => 'RoleController@create',
      'as' => 'roles.create',
    ]);
  Route::post('/store', [
      'uses' => 'RoleController@store',
      'as' => 'roles.store',
    ]);
  Route::get('{id}/edit/', [
      'uses' => 'RoleController@edit',
      'as' => 'roles.edit',
    ]);
  Route::post('{id}/update/', [
      'uses' => 'RoleController@update',
      'as' => 'roles.update',
    ]);
  Route::get('{id}/trash/', [
      'uses' => 'RoleController@trash',
      'as' => 'roles.trash',
    ]);
  Route::post('{id}/destroy/', [
      'uses' => 'RoleController@destroy',
      'as' => 'roles.destroy',
    ]);
});

Route::group(['prefix' => '/', 'middleware' => ['auth']], function () {
	Route::get('/', [
			'uses' => 'HomeController@index',
			'as' => 'home',
		]);
});

// -----
// USERS
// -----
Route::group(['prefix' => 'users'], function () {
	Route::get('/index', [
			'uses' => 'UserController@index',
			'as' => 'users.index',
		])->middleware(['auth', 'user_clearance']);
	Route::get('/create', [
			'uses' => 'UserController@create',
			'as' => 'user.create',
		])->middleware(['auth', 'user_clearance']);
	Route::post('/store', [
			'uses' => 'UserController@store',
			'as' => 'user.store',
		])->middleware(['auth', 'user_clearance']);
	Route::get('{id}/edit', [
			'uses' => 'UserController@edit',
			'as' => 'user.edit',
		])->middleware(['auth', 'user_clearance']);
	Route::post('{id}/update', [
			'uses' => 'UserController@update',
			'as' => 'user.update',
		])->middleware(['auth', 'user_clearance']);
	Route::post('{id}/password_reset', [
			'uses' => 'UserController@password_reset',
			'as' => 'user.password_reset',
		])->middleware(['auth', 'user_clearance']);
	Route::get('{id}/trash', [
			'uses' => 'UserController@trash',
			'as' => 'user.trash',
		])->middleware(['auth', 'user_clearance']);
	Route::post('{id}/delete', [
			'uses' => 'UserController@delete',
			'as' => 'user.delete',
		])->middleware(['auth', 'user_clearance']);
	Route::get('profile', [
			'uses' => 'UserController@profile',
			'as' => 'user.profile',
		])->middleware('auth');
  Route::put('profile-update', [
      'uses' => 'UserController@profile_update',
      'as' => 'user.profile-update',
    ])->middleware('auth');
	Route::put('changepass', [
			'uses' => 'UserController@changepass',
			'as' => 'user.changepass',
		])->middleware('auth');
  Route::post('import', [
			'uses' => 'UserController@import',
			'as' => 'user.import',
		])->middleware(['auth', 'user_clearance']);
});

// ---------------
// USER EMPLOYMENT
// ---------------
Route::group(['prefix' => 'users/employment_details'], function () {
	Route::get('/index', [
			'uses' => 'UserEmploymentController@index',
			'as' => 'employment_details.index',
		]);
	Route::get('{id}/edit', [
			'uses' => 'UserEmploymentController@edit',
			'as' => 'employment_detail.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'UserEmploymentController@update',
			'as' => 'employment_detail.update',
		]);
	Route::post('{id}/upload_customer', [
			'uses' => 'UserEmploymentController@upload_customer',
			'as' => 'employment_detail.upload_customer',
		]);
});

// --------
// EMPLOYEE
// --------
Route::group(['prefix' => 'employees'], function () {
	Route::get('/index', [
			'uses' => 'EmployeeController@index',
			'as' => 'employees.index',
		]);
	Route::get('{id}/edit', [
			'uses' => 'EmployeeController@edit',
			'as' => 'employee.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'EmployeeController@update',
			'as' => 'employee.update',
		]);
});

// -------
// REPORTS
// -------
Route::group(['prefix' => 'reports'], function () {
	Route::get('/overtime', [
			'uses' => 'ReportController@overtime',
			'as' => 'report.overtime',
		]);
	Route::get('/dtr', [
			'uses' => 'ReportController@dtr',
			'as' => 'report.dtr',
		]);
	Route::post('/import', [
		'uses' => 'ReportController@import',
			'as' => 'report.import',
		]);
	Route::get('/biometric', [
			'uses' => 'ReportController@biometric',
			'as' => 'report.biometric',
		]);
});

// --------------
// BRANCH REPORTS
// --------------
Route::group(['prefix' => 'branch_reports'], function () {
	Route::get('/overtime', [
			'uses' => 'BranchReportController@overtime',
			'as' => 'breport.overtime',
		]);
	Route::get('/biometric', [
			'uses' => 'BranchReportController@biometric',
			'as' => 'breport.biometric',
		]);
	Route::get('/dtr', [
			'uses' => 'BranchReportController@dtr',
			'as' => 'breport.dtr',
		]);
});

// -------
// REGIONS
// -------
Route::group(['prefix' => 'regions'], function () {
	Route::get('index/{err?}', [
			'uses' => 'RegionController@index',
			'as' => 'regions',
		]);
	Route::get('/create', [
			'uses' => 'RegionController@create',
			'as' => 'region.create',
		]);
	Route::post('/store', [
			'uses' => 'RegionController@store',
			'as' => 'region.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'RegionController@edit',
			'as' => 'region.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'RegionController@update',
			'as' => 'region.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'RegionController@trash',
			'as' => 'region.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'RegionController@delete',
			'as' => 'region.delete',
		]);
});

// --------
// BRANCHES
// --------
Route::group(['prefix' => 'branches', 'middleware' => ['auth']], function () {
	Route::get('index/{err?}', [
			'uses' => 'BranchController@index',
			'as' => 'branches.index',
		]);
	Route::get('/create', [
			'uses' => 'BranchController@create',
			'as' => 'branch.create',
		]);
	Route::post('/store', [
			'uses' => 'BranchController@store',
			'as' => 'branch.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'BranchController@edit',
			'as' => 'branch.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'BranchController@update',
			'as' => 'branch.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'BranchController@trash',
			'as' => 'branch.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'BranchController@delete',
			'as' => 'branch.delete',
		]);
});

// ----------------
// BRANCH SCHEDULES
// ----------------
// Route::resource('branch-schedules', 'BranchScheduleController');
Route::group(['prefix' => 'branch-schedules'], function () {
	Route::get('/index', [
			'uses' => 'BranchScheduleController@index',
			'as' => 'branch-schedules.index',
		]);
	Route::get('/create', [
			'uses' => 'BranchScheduleController@create',
			'as' => 'branch-schedule.create',
		]);
	Route::post('/store', [
			'uses' => 'BranchScheduleController@store',
			'as' => 'branch-schedule.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'BranchScheduleController@edit',
			'as' => 'branch-schedule.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'BranchScheduleController@update',
			'as' => 'branch-schedule.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'BranchScheduleController@trash',
			'as' => 'branch-schedule.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'BranchScheduleController@delete',
			'as' => 'branch-schedule.delete',
		]);
});

// ---------
// DIVISIONS
// ---------
Route::group(['prefix' => 'divisions'], function () {
	Route::get('/index', [
			'uses' => 'DivisionController@index',
			'as' => 'divisions.index',
		]);
	Route::get('/create', [
			'uses' => 'DivisionController@create',
			'as' => 'division.create',
		]);
	Route::post('/store', [
			'uses' => 'DivisionController@store',
			'as' => 'division.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'DivisionController@edit',
			'as' => 'division.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'DivisionController@update',
			'as' => 'division.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'DivisionController@trash',
			'as' => 'division.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'DivisionController@delete',
			'as' => 'division.delete',
		]);
});

// -----------
// DEPARTMENTS
// -----------
Route::group(['prefix' => 'departments'], function () {
	Route::get('/index', [
			'uses' => 'DepartmentController@index',
			'as' => 'departments.index',
		]);
	Route::get('/create', [
			'uses' => 'DepartmentController@create',
			'as' => 'department.create',
		]);
	Route::post('/store', [
			'uses' => 'DepartmentController@store',
			'as' => 'department.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'DepartmentController@edit',
			'as' => 'department.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'DepartmentController@update',
			'as' => 'department.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'DepartmentController@trash',
			'as' => 'department.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'DepartmentController@delete',
			'as' => 'department.delete',
		]);
});

// ---------
// POSITIONS
// ---------
Route::group(['prefix' => 'positions'], function () {
	Route::get('/index', [
			'uses' => 'PositionController@index',
			'as' => 'positions.index',
		]);
	Route::get('/create', [
			'uses' => 'PositionController@create',
			'as' => 'position.create',
		]);
	Route::post('/store', [
			'uses' => 'PositionController@store',
			'as' => 'position.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'PositionController@edit',
			'as' => 'position.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'PositionController@update',
			'as' => 'position.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'PositionController@trash',
			'as' => 'position.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'PositionController@delete',
			'as' => 'position.delete',
		]);
});

// -------------
// ACCESS CHARTS
// -------------
Route::group(['prefix' => 'access_charts'], function () {
	Route::get('/index', [
			'uses' => 'AccessChartController@index',
			'as' => 'access_charts.index',
		]);
	Route::get('/create', [
			'uses' => 'AccessChartController@create',
			'as' => 'access_chart.create',
		]);
	Route::post('/store', [
			'uses' => 'AccessChartController@store',
			'as' => 'access_chart.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'AccessChartController@edit',
			'as' => 'access_chart.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'AccessChartController@update',
			'as' => 'access_chart.update',
		]);

	Route::get('{id}/officers', [
			'uses' => 'AccessChartController@officers',
			'as' => 'access_chart.officers',
		]);
	Route::get('{id}/officer_create', [
			'uses' => 'AccessChartController@officer_create',
			'as' => 'access_chart.officer_create',
		]);
});

Route::group(['prefix' => 'access_chart_users'], function () {
	Route::post('{id}/store', [
			'uses' => 'AccesschartUserMapController@store',
			'as' => 'access_chart_user.store',
		]);
	Route::post('{id}/store-bdp', [
			'uses' => 'AccesschartUserMapController@store_bdp',
			'as' => 'access_chart_user.store_bdp',
		]);
	Route::get('{id}/edit', [
			'uses' => 'AccesschartUserMapController@edit',
			'as' => 'access_chart_user.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'AccesschartUserMapController@update',
			'as' => 'access_chart_user.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'AccesschartUserMapController@trash',
			'as' => 'access_chart_user.trash',
		]);
	Route::get('{id}/delete', [
			'uses' => 'AccesschartUserMapController@delete',
			'as' => 'access_chart_user.delete',
		]);
	Route::post('{id}/assign-to', [
			'uses' => 'AccesschartUserMapController@assign_to',
			'as' => 'access_chart_user.assign_to',
		]);
	Route::get('{id}/assigned-users', [
				'uses' => 'AccesschartUserMapController@assigned_users',
				'as' => 'access_chart_user.assigned_users',
			]);
});

Route::group(['prefix' => 'access_levels'], function () {
	Route::get('{accesschart_id}/edit', [
			'uses' => 'AccessLevelController@edit',
			'as' => 'access_level.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'AccessLevelController@update',
			'as' => 'access_level.update',
		]);
});

Route::group(['prefix' => 'approving_officers'], function () {
	Route::get('/', [
			'uses' => 'AccesschartUserMapController@chart',
			'as' => 'approving_officer.chart',
		]);
});

// --------------
// PRODUCT BRANDS
// --------------
Route::group(['prefix' => 'product/brands'], function () {
	Route::get('index/{err?}', [
			'uses' => 'ProductBrandController@index',
			'as' => 'brands',
		]);
	Route::get('/create', [
			'uses' => 'ProductBrandController@create',
			'as' => 'brand.create',
		]);
	Route::post('/store', [
			'uses' => 'ProductBrandController@store',
			'as' => 'brand.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'ProductBrandController@edit',
			'as' => 'brand.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'ProductBrandController@update',
			'as' => 'brand.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'ProductBrandController@trash',
			'as' => 'brand.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'ProductBrandController@delete',
			'as' => 'brand.delete',
		]);
});

// ----------------
// PRODUCT CATEGORY
// ----------------
Route::group(['prefix' => 'product/categories'], function () {
	Route::get('index/{err?}', [
			'uses' => 'ProductCategoryController@index',
			'as' => 'categories',
		]);
	Route::get('/create', [
			'uses' => 'ProductCategoryController@create',
			'as' => 'category.create',
		]);
	Route::post('/store', [
			'uses' => 'ProductCategoryController@store',
			'as' => 'category.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'ProductCategoryController@edit',
			'as' => 'category.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'ProductCategoryController@update',
			'as' => 'category.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'ProductCategoryController@trash',
			'as' => 'category.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'ProductCategoryController@delete',
			'as' => 'category.delete',
		]);
});

// ------------
// PRODUCT ITEM
// ------------
Route::group(['prefix' => 'product/items'], function () {
	Route::get('index/{err?}', [
			'uses' => 'ProductItemController@index',
			'as' => 'items',
		]);
	Route::get('/create', [
			'uses' => 'ProductItemController@create',
			'as' => 'item.create',
		]);
	Route::post('{computerware_id?}/store', [
			'uses' => 'ProductItemController@store',
			'as' => 'item.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'ProductItemController@edit',
			'as' => 'item.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'ProductItemController@update',
			'as' => 'item.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'ProductItemController@trash',
			'as' => 'item.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'ProductItemController@delete',
			'as' => 'item.delete',
		]);

	// FOR COMPUTERWARE TICKET
	Route::get('/create/computerware/create', [
			'uses' => 'ProductItemController@computerware_create_newitem',
			'as' => 'item.create.computerware.create',
		]);
	Route::get('{id}/create/computerware/edit', [
			'uses' => 'ProductItemController@computerware_edit_newitem',
			'as' => 'item.create.computerware.edit',
		]);
});

// ---------------------
// TICKET - COMPUTERWARE
// ---------------------
Route::group(['prefix' => 'ticket/computerwares'], function () {
	Route::get('index/{err?}', [
			'uses' => 'ComputerwareTicketController@index',
			'as' => 'ticket.computerwares',
		]);
	Route::get('/create', [
			'uses' => 'ComputerwareTicketController@create',
			'as' => 'ticket.computerware.create',
		]);
	Route::post('/store', [
			'uses' => 'ComputerwareTicketController@store',
			'as' => 'ticket.computerware.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'ComputerwareTicketController@edit',
			'as' => 'ticket.computerware.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'ComputerwareTicketController@update',
			'as' => 'ticket.computerware.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'ComputerwareTicketController@trash',
			'as' => 'ticket.computerware.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'ComputerwareTicketController@delete',
			'as' => 'ticket.computerware.delete',
		]);
});

// ----------------
// SERVICE PROVIDER
// ----------------
Route::group(['prefix' => 'service/providers'], function () {
	Route::get('index/{err?}', [
			'uses' => 'ServiceProviderController@index',
			'as' => 'service_providers',
		]);
	Route::get('/create', [
			'uses' => 'ServiceProviderController@create',
			'as' => 'service_provider.create',
		]);
	Route::post('/store', [
			'uses' => 'ServiceProviderController@store',
			'as' => 'service_provider.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'ServiceProviderController@edit',
			'as' => 'service_provider.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'ServiceProviderController@update',
			'as' => 'service_provider.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'ServiceProviderController@trash',
			'as' => 'service_provider.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'ServiceProviderController@delete',
			'as' => 'service_provider.delete',
		]);

	// FOR CONNECTIVITY TICKET
	Route::get('/create/connectivity/create', [
			'uses' => 'ServiceProviderController@connectivity_create_newservice',
			'as' => 'service_provider.create.connectivity.create',
		]);
	Route::get('{id}/create/connectivity/edit', [
			'uses' => 'ServiceProviderController@connectivity_edit_newservice',
			'as' => 'service_provider.create.connectivity.edit',
		]);
});

// ------------
// SERVICE TYPE
// ------------
Route::group(['prefix' => 'service/types'], function () {
	Route::get('index/{err?}', [
			'uses' => 'ServiceTypeController@index',
			'as' => 'service_types',
		]);
	Route::get('/create', [
			'uses' => 'ServiceTypeController@create',
			'as' => 'service_type.create',
		]);
	Route::post('/store', [
			'uses' => 'ServiceTypeController@store',
			'as' => 'service_type.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'ServiceTypeController@edit',
			'as' => 'service_type.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'ServiceTypeController@update',
			'as' => 'service_type.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'ServiceTypeController@trash',
			'as' => 'service_type.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'ServiceTypeController@delete',
			'as' => 'service_type.delete',
		]);
});

// ----------------
// SERVICE CATEGORY
// ----------------
Route::group(['prefix' => 'service/categories'], function () {
	Route::get('index/{err?}', [
			'uses' => 'ServiceCategoryController@index',
			'as' => 'service_categories',
		]);
	Route::get('/create', [
			'uses' => 'ServiceCategoryController@create',
			'as' => 'service_category.create',
		]);
	Route::post('/store', [
			'uses' => 'ServiceCategoryController@store',
			'as' => 'service_category.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'ServiceCategoryController@edit',
			'as' => 'service_category.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'ServiceCategoryController@update',
			'as' => 'service_category.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'ServiceCategoryController@trash',
			'as' => 'service_category.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'ServiceCategoryController@delete',
			'as' => 'service_category.delete',
		]);
});

// ---------------------
// TICKET - CONNECTIVITY
// ---------------------
Route::group(['prefix' => 'ticket/connectivities'], function () {
	Route::get('index/{err?}', [
			'uses' => 'ConnectivityTicketController@index',
			'as' => 'ticket.connectivities',
		]);
	Route::get('/create', [
			'uses' => 'ConnectivityTicketController@create',
			'as' => 'ticket.connectivity.create',
		]);
	Route::post('/store', [
			'uses' => 'ConnectivityTicketController@store',
			'as' => 'ticket.connectivity.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'ConnectivityTicketController@edit',
			'as' => 'ticket.connectivity.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'ConnectivityTicketController@update',
			'as' => 'ticket.connectivity.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'ConnectivityTicketController@trash',
			'as' => 'ticket.connectivity.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'ConnectivityTicketController@delete',
			'as' => 'ticket.connectivity.delete',
		]);

	// for branch
	Route::get('index-branch', [
			'uses' => 'ConnectivityTicketController@index_branch',
			'as' => 'ticket.branch.connectivities',
		]);

	Route::get('{id}/confirm', [
			'uses' => 'ConnectivityTicketController@confirm',
			'as' => 'ticket.connectivity.confirm',
		]);

	Route::post('{id}/confirm-proceed', [
			'uses' => 'ConnectivityTicketController@confirm_proceed',
			'as' => 'ticket.connectivity.confirm_proceed',
		]);
	
	Route::get('{id}/rate', [
			'uses' => 'ConnectivityTicketController@rate',
			'as' => 'ticket.connectivity.rate',
		]);

	Route::post('{id}/rate-proceed', [
			'uses' => 'ConnectivityTicketController@rate_proceed',
			'as' => 'ticket.connectivity.rate_proceed',
		]);
});

// ------------------
// POWER INTERRUPTION
// ------------------
Route::group(['prefix' => 'power_interruption'], function () {
	Route::get('index/{err?}', [
			'uses' => 'PowerInterruptionController@index',
			'as' => 'power_interruptions',
		]);
	Route::get('/create', [
			'uses' => 'PowerInterruptionController@create',
			'as' => 'power_interruption.create',
		]);
	Route::post('/store', [
			'uses' => 'PowerInterruptionController@store',
			'as' => 'power_interruption.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'PowerInterruptionController@edit',
			'as' => 'power_interruption.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'PowerInterruptionController@update',
			'as' => 'power_interruption.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'PowerInterruptionController@trash',
			'as' => 'power_interruption.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'PowerInterruptionController@delete',
			'as' => 'power_interruption.delete',
		]);
});

// ------
// THEMES
// ------
Route::group(['prefix' => 'themes'], function () {
	Route::post('/{id}/update', [
			'uses' => 'ThemeController@update',
			'as' => 'themes.update',
		]);
});

// ---------
// INVENTORY
// ---------
Route::group(['prefix' => 'inventories'], function () {
	Route::get('/', [
			'uses' => 'InventoryController@index',
			'as' => 'inventories',
		]);
	Route::get('{id}/breakdown_view', [
			'uses' => 'InventoryController@breakdown_view',
			'as' => 'inventory.breakdown_view',
		]);
	Route::get('{id}/discrepancy', [
			'uses' => 'InventoryController@discrepancy',
			'as' => 'inventory.discrepancy',
		]);
	Route::get('/create', [
			'uses' => 'InventoryController@create',
			'as' => 'inventory.create',
		]);
	Route::post('/store', [
			'uses' => 'InventoryController@store',
			'as' => 'inventory.store',
		]);
	Route::get('{id}/trash', [
			'uses' => 'InventoryController@trash',
			'as' => 'inventory.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'InventoryController@delete',
			'as' => 'inventory.delete',
		]);

	// For Branch Users
	Route::get('{id}/create_branch', [
			'uses' => 'InventoryController@create_branch',
			'as' => 'inventory.create_branch',
		])->middleware('auth');
	Route::get('{id}/duplicate_branch', [
			'uses' => 'InventoryController@duplicate_branch',
			'as' => 'inventory.duplicate_branch',
		])->middleware('auth');
	Route::get('{id}/edit_branch', [
			'uses' => 'InventoryController@edit_branch',
			'as' => 'inventory.edit_branch',
		])->middleware('auth');
	Route::post('{id}/store_branch', [
			'uses' => 'InventoryController@store_branch',
			'as' => 'inventory.store_branch',
		])->middleware('auth');
	Route::post('{id}/update_branch', [
			'uses' => 'InventoryController@update_branch',
			'as' => 'inventory.update_branch',
		])->middleware('auth');
	Route::post('{id}/update_branch_qty', [
			'uses' => 'InventoryController@update_branch_qty',
			'as' => 'inventory.update_branch_qty',
		])->middleware('auth');
	Route::post('{id}/trash_branch', [
			'uses' => 'InventoryController@trash_branch',
			'as' => 'inventory.trash_branch',
		])->middleware('auth');
	Route::post('{id}/delete_branch', [
			'uses' => 'InventoryController@delete_branch',
			'as' => 'inventory.delete_branch',
		])->middleware('auth');
	Route::get('{id}/get_raw', [
			'uses' => 'InventoryController@get_raw',
			'as' => 'inventory.get_raw',
		])->middleware('auth');
	Route::get('{id}/view', [
			'uses' => 'InventoryController@view',
			'as' => 'inventory.view',
		])->middleware('auth');
	Route::get('{id}/import_branch', [
			'uses' => 'InventoryController@import_branch',
			'as' => 'inventory.import_branch',
		])->middleware('auth');
	Route::post('{id}/import_proceed', [
			'uses' => 'InventoryController@import_proceed',
			'as' => 'inventory.import_proceed',
		])->middleware('auth');
});

// ------------
// MESSAGE CAST
// ------------
Route::group(['prefix' => 'settings/message_casts'], function () {
	Route::get('/', [
			'uses' => 'MessageCastSettingController@index',
			'as' => 'settings.message_casts',
		]);
	Route::post('/update', [
			'uses' => 'MessageCastSettingController@update',
			'as' => 'setting.message_cast.update',
		]);
});

Route::group(['prefix' => 'contact_lists/message_casts'], function () {
	Route::get('/', [
			'uses' => 'ContactListController@index',
			'as' => 'contact_lists.message_casts',
		]);
	Route::get('/create', [
			'uses' => 'ContactListController@create',
			'as' => 'contact_list.message_cast.create',
		]);
	Route::post('/store', [
			'uses' => 'ContactListController@store',
			'as' => 'contact_list.message_cast.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'ContactListController@edit',
			'as' => 'contact_list.message_cast.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'ContactListController@update',
			'as' => 'contact_list.message_cast.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'ContactListController@trash',
			'as' => 'contact_list.message_cast.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'ContactListController@delete',
			'as' => 'contact_list.message_cast.delete',
		]);
});

Route::group(['prefix' => 'messages/message_casts'], function () {
	Route::get('/', [
			'uses' => 'MessageController@index',
			'as' => 'messages.message_casts',
		]);
	Route::post('/send', [
			'uses' => 'MessageController@send',
			'as' => 'message.message_cast.send',
		]);
	Route::get('{id}/check_status', [
			'uses' => 'MessageController@check_status',
			'as' => 'message.message_cast.check_status',
		]);

	// AJAX
	Route::post('{user_id}/contacts_ajax', [
			'uses' => 'MessageController@contacts_ajax',
			'as' => 'message.message_cast.contacts_ajax',
		]);
});

// ---------
// FILE TYPE
// ---------

Route::group(['prefix' => 'file-types'], function () {
	Route::get('/index', [
		'uses' => 'FileTypeController@index',
		'as' => 'file.type.index',
	]);
	Route::get('/create', [
		'uses' => 'FileTypeController@create',
		'as' => 'file.type.create',
	]);
	Route::post('/store', [
		'uses' => 'FileTypeController@store',
		'as' => 'file.type.store',
	]);
	Route::get('{id}/edit', [
		'uses' => 'FileTypeController@edit',
		'as' => 'file.type.edit',
	]);
	Route::post('{id}/update', [
		'uses' => 'FileTypeController@update',
		'as' => 'file.type.update',
	]);
	Route::get('{id}/trash', [
		'uses' => 'FileTypeController@trash',
		'as' => 'file.type.trash',
	]);
	Route::post('{id}/delete', [
		'uses' => 'FileTypeController@delete',
		'as' => 'file.type.delete',
	]);
});

// --------------
// CUSTOMER PHOTO
// --------------
Route::group(['prefix' => 'customers'], function () {
	Route::any('/', [
			'uses' => 'CustomerController@index',
			'as' => 'customers',
		]);
	Route::get('/basic', [
			'uses' => 'CustomerController@basic',
			'as' => 'customer.basic',
		]);
	Route::post('/store', [
			'uses' => 'CustomerController@store',
			'as' => 'customer.store',
		]);
	Route::get('{id}/printimage', [
			'uses' => 'CustomerController@printimage',
			'as' => 'customer.printimage',
		]);
	Route::get('{id}/printimage2', [
			'uses' => 'CustomerController@printimage2',
			'as' => 'customer.printimage2',
		]);
	Route::get('{id}/printimage3', [
			'uses' => 'CustomerController@printimage3',
			'as' => 'customer.printimage3',
		]);
	Route::get('{id}/edit', [
			'uses' => 'CustomerController@edit',
			'as' => 'customer.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'CustomerController@update',
			'as' => 'customer.update',
		]);
	Route::get('/import', [
			'uses' => 'CustomerController@import',
			'as' => 'customer.import',
		]);
	Route::post('/import_proceed', [
			'uses' => 'CustomerController@import_proceed',
			'as' => 'customer.import_proceed',
		]);

	// Additional for revision
	Route::get('{camera_id}/files', [
			'uses' => 'CustomerController@files',
			'as' => 'customer.files',
		]);
	Route::get('{camera_id}/files/add', [
			'uses' => 'CustomerController@file_add',
			'as' => 'customer.file_add',
		]);
	Route::post('{camera_id}/files/supply', [
			'uses' => 'CustomerController@file_store',
			'as' => 'customer.file_store',
		]);
	Route::get('{camera_id}/files/{file_id}/alter', [
			'uses' => 'CustomerController@file_edit',
			'as' => 'customer.file_edit',
		]);
	Route::post('{camera_id}/files/{file_id}/revise', [
			'uses' => 'CustomerController@file_update',
			'as' => 'customer.file_update',
		]);
	Route::get('{camera_id}/files/{file_id}/bin', [
			'uses' => 'CustomerController@file_trash',
			'as' => 'customer.file_trash',
		]);
	Route::post('{camera_id}/files/{file_id}/destroy', [
			'uses' => 'CustomerController@file_delete',
			'as' => 'customer.file_delete',
		]);
	Route::get('{camera_id}/files/{file_id}/download', [
			'uses' => 'CustomerController@file_download',
			'as' => 'customer.file_download',
		]);

	Route::get('/sync/index', [
			'uses' => 'CustomerController@sync_index',
			'as' => 'customer.sync.index',
		]);

	Route::get('{id}/sync/proceed', [
			'uses' => 'CustomerController@sync_proceed',
			'as' => 'customer.sync.proceed',
		]);
});

// --------
// PENDINGS
// --------
Route::group(['prefix' => 'pendings'], function () {
	Route::get('/index', [
			'uses' => 'PendingController@index',
			'as' => 'pendings',
		]);
	Route::get('/ci', [
			'uses' => 'PendingController@ci',
			'as' => 'pending.ci',
		]);
	Route::post('/show', [
			'uses' => 'PendingController@show',
			'as' => 'pending.show',
		]);
	Route::post('/show_ci', [
			'uses' => 'PendingController@show_ci',
			'as' => 'pending.show_ci',
		]);
	Route::get('/create', [
			'uses' => 'PendingController@create',
			'as' => 'pending.create',
		]);
	Route::get('/create_previous', [
			'uses' => 'PendingController@createPrev',
			'as' => 'pending.createprev',
		]);
	Route::post('/store', [
			'uses' => 'PendingController@store',
			'as' => 'pending.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'PendingController@edit',
			'as' => 'pending.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'PendingController@update',
			'as' => 'pending.update',
		]);
	Route::get('{id}/{pending_id}/delete', [
			'uses' => 'PendingController@delete',
			'as' => 'pending.delete',
		]);
	Route::get('{id}/readd', [
			'uses' => 'PendingController@readd',
			'as' => 'pending.readd',
		]);
	Route::get('/exportexcel', [
			'uses' => 'PendingController@excel',
			'as' => 'pending.exportexcel',
		]);
	Route::get('{id}/index_as', [
			'uses' => 'PendingController@index_as',
			'as' => 'pending.index_as',
		]);
	Route::get('/add_as', [
			'uses' => 'PendingController@add_as',
			'as' => 'pending.add_as',
		]);
	Route::get('{id}/{pending_id}/edit_as', [
			'uses' => 'PendingController@edit_as',
			'as' => 'pending.edit_as',
		]);
	Route::get('{id}/readd_as', [
			'uses' => 'PendingController@readd_as',
			'as' => 'pending.readd_as',
		]);
	Route::get('{id}/{date?}/add_all', [
			'uses' => 'PendingController@add_all',
			'as' => 'pending.add_all',
		]);

  // Chart
	Route::get('branch/{id}/chart', [
			'uses' => 'PendingController@chart',
			'as' => 'pending.branch.chart',
		]);
	Route::post('branch/{id}/filtered_chart', [
			'uses' => 'PendingController@filtered_chart',
			'as' => 'pending.branch.filtered_chart',
		]);

  Route::get('branch/{id}/{date}/breakdown', [
			'uses' => 'PendingController@breakdown',
			'as' => 'pending.branch.breakdown',
		]);
	Route::get('{id}/{pending_id}/readd_breakdown', [
			'uses' => 'PendingController@readd_breakdown',
			'as' => 'pending.readd_breakdown',
		]);
	Route::post('{id}/store_breakdown', [
			'uses' => 'PendingController@store_breakdown',
			'as' => 'pending.store_breakdown',
		]);
});

// -------
// Charts
// -------
Route::group(['prefix' => 'charts/pendings'], function () {
	Route::get('/overall', [
			'uses' => 'PendingChartController@overall',
			'as' => 'charts.pendings.overall',
		]);
});

// -------------------
// Interview Schedules
// -------------------
Route::group(['prefix' => 'schedules/interviews'], function () {
	Route::get('/index', [
			'uses' => 'InterviewScheduleController@index',
			'as' => 'interview_scheds.index',
		]);
  Route::get('/create', [
			'uses' => 'InterviewScheduleController@create',
			'as' => 'interview_sched.create',
		]);
  Route::post('/store', [
			'uses' => 'InterviewScheduleController@store',
			'as' => 'interview_sched.store',
		]);
  Route::get('{id}/edit', [
			'uses' => 'InterviewScheduleController@edit',
			'as' => 'interview_sched.edit',
		]);
  Route::post('{id}/update', [
			'uses' => 'InterviewScheduleController@update',
			'as' => 'interview_sched.update',
		]);
  Route::get('{id}/trash', [
			'uses' => 'InterviewScheduleController@trash',
			'as' => 'interview_sched.trash',
		]);
  Route::post('{id}/delete', [
			'uses' => 'InterviewScheduleController@delete',
			'as' => 'interview_sched.delete',
		]);

	// Admin Only
	Route::get('/add', [
			'uses' => 'InterviewScheduleController@add',
			'as' => 'interview_sched.add',
		]);
	Route::get('{id}/complete', [
			'uses' => 'InterviewScheduleController@complete',
			'as' => 'interview_sched.complete',
		]);
	Route::post('{id}/complete_proceed', [
			'uses' => 'InterviewScheduleController@complete_proceed',
			'as' => 'interview_sched.complete_proceed',
		]);
});

// ---------
// Overtimes
// ---------
Route::group(['prefix' => 'overtimes', 'middlware' => ['auth']], function () {
	Route::get('/', [
			'uses' => 'OvertimeController@index',
			'as' => 'overtimes',
		]);
	Route::get('/create', [
			'uses' => 'OvertimeController@create',
			'as' => 'overtime.create',
		]);
	Route::post('/store', [
			'uses' => 'OvertimeController@store',
			'as' => 'overtime.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'OvertimeController@edit',
			'as' => 'overtime.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'OvertimeController@update',
			'as' => 'overtime.update',
		]);
	Route::get('{id}/trash', [
			'uses' => 'OvertimeController@trash',
			'as' => 'overtime.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'OvertimeController@delete',
			'as' => 'overtime.delete',
		]);
	Route::get('{id}/remove_reject', [
			'uses' => 'OvertimeController@remove_reject',
			'as' => 'overtime.remove_reject',
		]);
});

// ---------
// Approvals
// ---------
Route::group(['prefix' => 'approvals'], function () {
	Route::get('/overtimes/pending', [
			'uses' => 'OvertimeApprovalController@pending',
			'as' => 'approval.pending',
		]);
	Route::get('{id}/overtimes/approve', [
			'uses' => 'OvertimeApprovalController@approve',
			'as' => 'approval.approve',
		]);
	Route::post('{id}/overtimes/proceed_approve', [
			'uses' => 'OvertimeApprovalController@proceed_approve',
			'as' => 'approval.proceed_approve',
		]);
	Route::get('{id}/overtimes/return', [
			'uses' => 'OvertimeApprovalController@oreturn',
			'as' => 'approval.return',
		]);
	Route::post('{id}/overtimes/proceed_return', [
			'uses' => 'OvertimeApprovalController@proceed_return',
			'as' => 'approval.proceed_return',
		]);
	Route::get('{id}/overtimes/reject', [
			'uses' => 'OvertimeApprovalController@reject',
			'as' => 'approval.reject',
		]);
	Route::post('{id}/overtimes/proceed_reject', [
			'uses' => 'OvertimeApprovalController@proceed_reject',
			'as' => 'approval.proceed_reject',
		]);

	// Maintenance Request
  Route::get('/maint-requests/pending', [
			'uses' => 'MaintRequestApprovalController@pending',
			'as' => 'maint_request.approval.pending',
		]);
	Route::get('{id}/maint-requests/approve', [
			'uses' => 'MaintRequestApprovalController@approve',
			'as' => 'maint_request.approval.approve',
		]);
	Route::post('{id}/maint-requests/proceed_approve', [
			'uses' => 'MaintRequestApprovalController@proceed_approve',
			'as' => 'maint_request.approval.proceed_approve',
		]);
	Route::get('{id}/maint-requests/escalate', [
			'uses' => 'MaintRequestApprovalController@escalate',
			'as' => 'maint_request.approval.escalate',
		]);
	Route::post('{id}/maint-requests/proceed_escalate', [
			'uses' => 'MaintRequestApprovalController@proceed_escalate',
			'as' => 'maint_request.approval.proceed_escalate',
		]);
	Route::get('{id}/maint-requests/cancel', [
			'uses' => 'MaintRequestApprovalController@cancel',
			'as' => 'maint_request.approval.cancel',
		]);
	Route::post('{id}/maint-requests/proceed_cancel', [
			'uses' => 'MaintRequestApprovalController@proceed_cancel',
			'as' => 'maint_request.approval.proceed_cancel',
		]);
	
	// Files
  Route::get('/purchase-orders/files/pending', [
			'uses' => 'PurchaseOrderFileApprovalController@pending',
			'as' => 'po.file.approval.pending',
		]);
	Route::get('{id}/purchase-orders/files/approve', [
			'uses' => 'PurchaseOrderFileApprovalController@approve',
			'as' => 'po.file.approval.approve',
		]);
	Route::post('{id}/purchase-orders/files/proceed_approve', [
			'uses' => 'PurchaseOrderFileApprovalController@proceed_approve',
			'as' => 'po.file.approval.proceed_approve',
		]);
	Route::get('{id}/purchase-orders/files/reject', [
			'uses' => 'PurchaseOrderFileApprovalController@reject',
			'as' => 'po.file.approval.reject',
		]);
	Route::post('{id}/purchase-orders/files/proceed_reject', [
			'uses' => 'PurchaseOrderFileApprovalController@proceed_reject',
			'as' => 'po.file.approval.proceed_reject',
		]);

	// BOD FUNCTIONS
	// otloa
	Route::get('/overtimes/overlook', [
			'uses' => 'OvertimeApprovalController@overlook',
			'as' => 'approval.overlook',
		]);

	// mrf
	Route::get('/maint-requests/overlook', [
			'uses' => 'MaintRequestApprovalController@overlook',
			'as' => 'maint_request.approval.overlook',
		]);

	// po file
	Route::get('/files/overlook', [
			'uses' => 'PurchaseOrderFileApprovalController@overlook',
			'as' => 'po.file.approval.overlook',
		]);
  
});

// --------------------
// MAINTENANCE REQUESTS
// --------------------
Route::group(['prefix' => 'maint-requests'], function () {
	Route::get('index/{err?}', [
			'uses' => 'MaintRequestController@index',
			'as' => 'maint_requests',
		]);
	Route::get('/create', [
			'uses' => 'MaintRequestController@create',
			'as' => 'maint_request.create',
		]);
	Route::post('/store', [
			'uses' => 'MaintRequestController@store',
			'as' => 'maint_request.store',
		]);
	Route::get('{id}/edit', [
			'uses' => 'MaintRequestController@edit',
			'as' => 'maint_request.edit',
		]);
	Route::post('{id}/update', [
			'uses' => 'MaintRequestController@update',
			'as' => 'maint_request.update',
    ]);
	Route::get('{id}/completion', [
      'uses' => 'MaintRequestController@completion',
      'as' => 'maint_request.completion',
    ]);
  Route::post('{id}/completion_proceed', [
      'uses' => 'MaintRequestController@completion_proceed',
      'as' => 'maint_request.completion_proceed',
    ]);
	Route::get('{id}/trash', [
			'uses' => 'MaintRequestController@trash',
			'as' => 'maint_request.trash',
		]);
	Route::post('{id}/delete', [
			'uses' => 'MaintRequestController@delete',
			'as' => 'maint_request.delete',
		]);
	Route::post('{id}/files/{file_id}/delete', [
			'uses' => 'MaintRequestController@file_delete',
			'as' => 'maint_request.file.delete',
		]);

	// Admin & User
	Route::get('{id}/view', [
			'uses' => 'MaintRequestController@view',
			'as' => 'maint_request.view',
		]);
	Route::get('/approved-mrfs', [
			'uses' => 'MaintRequestController@approved_mrfs',
			'as' => 'maint_request.approved_mrfs',
		]);
	Route::get('{id}/view-approved', [
			'uses' => 'MaintRequestController@view_approved',
			'as' => 'maint_request.view_approved',
		]);
});

// ---------
// COMPANIES
// ---------
Route::group(['prefix' => 'companies'], function () {
  Route::get('/index', [
      'uses' => 'CompanyController@index',
      'as' => 'companies.index',
    ]);
  Route::get('{id}/edit', [
      'uses' => 'CompanyController@edit',
      'as' => 'company.edit',
    ]);
  Route::put('{id}/update', [
      'uses' => 'CompanyController@update',
      'as' => 'company.update',
    ]);
  Route::get('{id}/trash', [
      'uses' => 'CompanyController@trash',
      'as' => 'company.trash',
    ]);
  Route::delete('{id}/delete', [
      'uses' => 'CompanyController@delete',
      'as' => 'company.delete',
    ]);
  Route::post('/store-ajax', [
      'uses' => 'CompanyController@store_ajax',
      'as' => 'company.store-ajax',
    ]);
});

// ---------------
// PURCHASE ORDERS
// ---------------
Route::group(['prefix' => 'purchase-orders'], function () {
	// files
  Route::group(['prefix' => 'files'], function () {
		// user
		Route::get('/view', [
			'uses' => 'PurchaseOrderFileController@view',
			'as' => 'purchase_orders.files.view',
		]);

		// admin
		Route::get('/view-approved', [
				'uses' => 'PurchaseOrderFileController@view_approved',
				'as' => 'purchase_orders.files.view_approved',
			]);
		Route::get('/index', [
				'uses' => 'PurchaseOrderFileController@index',
				'as' => 'purchase_orders.files.index',
			]);
		Route::get('/create', [
				'uses' => 'PurchaseOrderFileController@create',
				'as' => 'purchase_order.file.create',
			]);
		Route::post('/store', [
				'uses' => 'PurchaseOrderFileController@store',
				'as' => 'purchase_order.file.store',
			]);
		Route::get('{id}/edit', [
				'uses' => 'PurchaseOrderFileController@edit',
				'as' => 'purchase_order.file.edit',
			]);
		Route::put('{id}/update', [
				'uses' => 'PurchaseOrderFileController@update',
				'as' => 'purchase_order.file.update',
			]);
		Route::get('{id}/trash', [
				'uses' => 'PurchaseOrderFileController@trash',
				'as' => 'purchase_order.file.trash',
			]);
		Route::delete('{id}/delete', [
				'uses' => 'PurchaseOrderFileController@delete',
				'as' => 'purchase_order.file.delete',
			]);
		Route::post('{id}/download', [
				'uses' => 'PurchaseOrderFileController@download',
				'as' => 'purchase_order.file.download',
			]);
		Route::post('{id}/seen', [
				'uses' => 'PurchaseOrderFileController@seen',
				'as' => 'purchase_order.file.seen',
			]);
		
		// AJAX
		Route::post('/store-ajax', [
			'uses' => 'PurchaseOrderFileController@download',
			'as' => 'purchase_order.file.store-ajax',
			]);

		Route::get('{id}/edit-ajax', [
				'uses' => 'PurchaseOrderFileController@edit_ajax',
				'as' => 'purchase_order.file.edit-ajax',
			]);
	});

	// file settings
	Route::group(['prefix' => 'file-settings'], function () {
		Route::get('/', [
				'uses' => 'FileSettingController@settings',
				'as' => 'purchase_orders.file-settings.index',
			]);
		Route::post('/update-ajax', [
				'uses' => 'FileSettingController@update_ajax',
				'as' => 'purchase_order.file-setting.update-ajax',
			]);
		Route::get('{email}/{email_notif}/new-email-ajax', [
				'uses' => 'FileSettingController@new_email_ajax',
				'as' => 'purchase_order.file-setting.new-email-ajax',
			]);
	});
});

// CONCERNS
Route::group(['prefix' => 'concerns'], function () {
	Route::get('/index', [
			'uses' => 'ConcernController@index',
			'as' => 'concerns.index'
		]);
	Route::get('/create', [
			'uses' => 'ConcernController@create',
			'as' => 'concern.create'
		]);
	Route::post('/store', [
			'uses' => 'ConcernController@store',
			'as' => 'concern.store'
		]);
	Route::get('{id}/edit', [
			'uses' => 'ConcernController@edit',
			'as' => 'concern.edit'
		]);
	Route::post('{id}/update', [
			'uses' => 'ConcernController@update',
			'as' => 'concern.update'
		]);
	Route::get('{id}/trash', [
			'uses' => 'ConcernController@trash',
			'as' => 'concern.trash'
		]);
	Route::post('{id}/delete', [
			'uses' => 'ConcernController@delete',
			'as' => 'concern.delete'
		]);

	// TYPES
	Route::group(['prefix' => 'types'], function () {
		Route::get('/index', [
				'uses' => 'ConcernTypeController@index',
				'as' => 'concerns.types.index'
			]);
		Route::get('/create', [
				'uses' => 'ConcernTypeController@create',
				'as' => 'concern.type.create'
			]);
		Route::post('/store', [
				'uses' => 'ConcernTypeController@store',
				'as' => 'concern.type.store'
			]);
		Route::get('{id}/edit', [
				'uses' => 'ConcernTypeController@edit',
				'as' => 'concern.type.edit'
			]);
		Route::post('{id}/update', [
				'uses' => 'ConcernTypeController@update',
				'as' => 'concern.type.update'
			]);
		Route::get('{id}/trash', [
				'uses' => 'ConcernTypeController@trash',
				'as' => 'concern.type.trash'
			]);
		Route::post('{id}/delete', [
				'uses' => 'ConcernTypeController@delete',
				'as' => 'concern.type.delete'
			]);
	});

	// CATEGORY
	Route::group(['prefix' => 'categories'], function () {
		Route::get('/index', [
				'uses' => 'ConcernCategoryController@index',
				'as' => 'concerns.categories.index'
			]);
		Route::get('/create', [
				'uses' => 'ConcernCategoryController@create',
				'as' => 'concern.category.create'
			]);
		Route::post('/store', [
				'uses' => 'ConcernCategoryController@store',
				'as' => 'concern.category.store'
			]);
		Route::get('{id}/edit', [
				'uses' => 'ConcernCategoryController@edit',
				'as' => 'concern.category.edit'
			]);
		Route::post('{id}/update', [
				'uses' => 'ConcernCategoryController@update',
				'as' => 'concern.category.update'
			]);
		Route::get('{id}/trash', [
				'uses' => 'ConcernCategoryController@trash',
				'as' => 'concern.category.trash'
			]);
		Route::post('{id}/delete', [
				'uses' => 'ConcernCategoryController@delete',
				'as' => 'concern.category.delete'
			]);
	});
});

Route::group(['prefix' => 'announcements'], function () {
	Route::get('/index', [
		'uses' => 'AnnouncementController@index',
		'as' => 'announcements.index',
	]);
	Route::get('/view', [
		'uses' => 'AnnouncementController@view',
		'as' => 'announcements.view',
	]);
	Route::get('/create', [
		'uses' => 'AnnouncementController@create',
		'as' => 'announcement.create',
	]);
	Route::post('/store', [
		'uses' => 'AnnouncementController@store',
		'as' => 'announcement.store',
	]);
	Route::get('{id}/edit', [
		'uses' => 'AnnouncementController@edit',
		'as' => 'announcement.edit',
	]);
	Route::post('{id}/update', [
		'uses' => 'AnnouncementController@update',
		'as' => 'announcement.update',
	]);
	Route::get('{id}/trash', [
		'uses' => 'AnnouncementController@trash',
		'as' => 'announcement.trash',
	]);
	Route::post('{id}/delete', [
		'uses' => 'AnnouncementController@delete',
		'as' => 'announcement.delete',
	]);
});