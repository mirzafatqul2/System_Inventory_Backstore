<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'dashboardutama';
$route['datasales/sales_target'] = 'DataSales/SalesTarget/index';
$route['datasales/sales_target/addData'] = 'DataSales/SalesTarget/addData';
$route['datasales/sales_target/updateData'] = 'DataSales/SalesTarget/updateData';
$route['datasales/sales_target/deleteData/(:num)'] = 'DataSales/SalesTarget/deleteData/$1';
$route['datasales/sales_target/get'] = 'DataSales/SalesTarget/getDataById';
$route['datasales/sales_target/(:any)'] = 'DataSales/SalesTarget/$1';
$route['datasales/sales_target/exportExcel'] = 'Datasales/SalesTarget/exportExcel';

$route['datasales/sales_achievement'] = 'DataSales/SalesAchievement/index';
$route['datasales/sales_achievement/dailyAchievement/(:any)'] = 'DataSales/SalesAchievement/dailyAchievement/$1';
$route['datasales/sales_achievement/checkTanggal'] = 'DataSales/SalesAchievement/checkTanggal';
$route['datasales/sales_achievement/inputDailySales'] = 'DataSales/SalesAchievement/inputDailySales';
$route['datasales/sales_achievement/exportExcel'] = 'Datasales/SalesAchievement/exportExcel';
$route['datasales/sales_achievement/exportDailyExcel/(:any)'] = 'DataSales/SalesAchievement/exportDailyExcel/$1';
$route['datasales/sales_achievement/checkDailyData/(:any)'] = 'DataSales/SalesAchievement/checkDailyData/$1';


$route['datasales/sales_dashboard'] = 'DataSales/SalesDashboard/index';
$route['SalesDashboard/(:any)'] = 'DataSales/SalesDashboard/$1';

$route['databarang/list_departement'] = 'DataBarang/ListDepartement/index';
$route['databarang/list_departement/addData'] = 'DataBarang/ListDepartement/addData';
$route['databarang/list_departement/updateData'] = 'DataBarang/ListDepartement/updateData';
$route['databarang/list_departement/deleteData/(:num)'] = 'DataBarang/ListDepartement/deleteData/$1';
$route['databarang/list_departement/get'] = 'DataBarang/ListDepartement/getDataById';
$route['databarang/list_departement/(:any)'] = 'DataBarang/ListDepartement/$1';

$route['databarang/dashboard_barang'] = 'DataBarang/DashboardBarang/index';

$route['databarang/list_barang'] = 'DataBarang/ListBarang/index';
$route['databarang/list_barang/ajax_list'] = 'DataBarang/ListBarang/ajax_list';
$route['databarang/list_barang/importExcel'] = 'Databarang/ListBarang/importExcel';
$route['databarang/list_barang/checkBrownboxData'] = 'Databarang/ListBarang/checkBrownboxData';
$route['databarang/list_barang/exportExcel'] = 'Databarang/ListBarang/exportExcel';

$route['databarang/master_keepstock/printsheet/(:any)'] = 'DataBarang/MasterKeepstock/printsheet/$1';
$route['databarang/master_keepstock'] = 'DataBarang/MasterKeepstock/index';
$route['databarang/master_keepstock/ajax_list'] = 'DataBarang/MasterKeepstock/ajax_list';
$route['databarang/master_keepstock/addData'] = 'DataBarang/MasterKeepstock/addData';
$route['databarang/master_keepstock/importExcel'] = 'Databarang/MasterKeepstock/importExcel';
$route['databarang/master_keepstock/get_detail'] = 'databarang/MasterKeepstock/get_detail';
$route['databarang/master_keepstock/update'] = 'databarang/MasterKeepstock/update';


$route['databarang/data_refill'] = 'DataBarang/RefillKeepstock/index';
$route['databarang/data_refill/ajax_list'] = 'DataBarang/RefillKeepstock/ajax_list';
$route['databarang/data_refill/add'] = 'DataBarang/RefillKeepstock/add';
$route['databarang/data_refill/exportExcel'] = 'DataBarang/RefillKeepstock/exportExcel';

$route['dataomnimbus/dashboard_omnimbus'] = 'DataOmnimbus/DashboardOmnimbus/index';

$route['dataomnimbus/data_damage'] = 'DataDamage/Damage/index';
$route['dataomnimbus/data_damage/importExcel'] = 'DataDamage/Damage/importExcel';
$route['damage/(:any)'] = 'DataDamage/Damage/$1';

$route['dataomnimbus/data_ceklist'] = 'StockCeklist/Stockceklist/index';
$route['dataomnimbus/data_ceklist/importExcel'] = 'StockCeklist/Stockceklist/importExcel';
$route['Stockceklist/(:any)'] = 'StockCeklist/Stockceklist/$1';

$route['dataomnimbus/datang_barang'] = 'DataOmnimbus/DatangBarang/index';
$route['dataomnimbus/datang_barang/(:any)'] = 'DataOmnimbus/DatangBarang/$1';

$route['pettycash/claim_pettycash'] = 'PettyCash/Pettycash/index';
$route['pettycash/ajax_list'] = 'PettyCash/Pettycash/ajax_list';
$route['pettycash/addData'] = 'PettyCash/Pettycash/addData';
$route['pettycash/updateData'] = 'PettyCash/Pettycash/updateData';
$route['pettycash/getById/(:num)'] = 'PettyCash/Pettycash/getById/$1';
$route['pettycash/deleteData/(:num)'] = 'PettyCash/Pettycash/deleteData/$1';
$route['pettycash/export_excel'] = 'PettyCash/Pettycash/export_excel';
$route['pettycash/dashboard_pettycash'] = 'PettyCash/PettycashDashboard/index';
$route['pettycashdashboard/ajax_list'] = 'PettyCash/PettycashDashboard/ajax_list';
$route['pettycash/updateStatus'] = 'PettyCash/Pettycash/updateStatus';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
