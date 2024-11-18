<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController,
	App\Http\Controllers\CajaController,
	App\Http\Controllers\ProductosController,
	App\Http\Controllers\InventariosController,
	App\Http\Controllers\EntradaSalidaController,
	App\Http\Controllers\MayoreoController,
	App\Http\Controllers\VentasController,
	App\Http\Controllers\ClientController,

	App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\HistorialController;

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

//use App\Models\Caja;

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/', function () {

	return view('auth.login');


	/*
	//seleccionar las cajas que hayan quedado abiertas
	$resZero= Caja::where("status",0)->get();
	//seleccionar las cajas que esten desocupadas
	$res= Caja::where("status",1)->get();	 
	$resZ=count($resZero);
	if($resZ>0){
	 	//var_dump("hay turno abierto");
	 	return view('cajas.turnoOpen');
	} elseif ($resZ<=0) {
		return view('auth.login')->with('res', $res);
	}
	*/
});
//USUARIOS
//USUARIOS

Route::get('/viewUser', [UserController::class, 'viewUsers'])->name('viewUser');
Route::get('/allUser', [UserController::class, 'allUsers'])->name('allUser');
Route::get('/viewEditUser/{id}', [UserController::class, 'viewEdit'])->name('viewEditUser');
Route::get('/deleteUser/{id}', [UserController::class, 'deleteUser'])->name('delete');
//Route::get('viewChange', 'UserController@viewPass')->name('viewChange');
Route::get('viewChange', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('viewChange');

Route::post('saveUser', [UserController::class, 'saveEditUser'])->name('saveUser');
Route::get('/viewAdd', [UserController::class, 'viewAdduser'])->name('viewAdd');
Route::post('/addUsers', [UserController::class, 'addUser'])->name('addUsers');



//entradas
//Route::get('/entrada', [EntradasController::class,'viewEntrada'])->name('entrada');
//
Route::get('/welcome', [HomeController::class, 'welcome'])->name('welcome');

//CAJA
Route::post('/envia', [CajaController::class, 'envia'])->name('envia');

Route::post('/caja', [CajaController::class, 'operacionCaja'])->name('caja');

Route::get('/viewcaja', [CajaController::class, 'vistaCaja'])->name('viewcaja');
Route::post('/newcaja', [CajaController::class, 'newCaja'])->name('newcaja');
Route::get('/allcaja', [CajaController::class, 'allCajas'])->name('allcaja');
Route::get('/cajasAjax', [CajaController::class, 'cajasAjax'])->name('cajasAjax');
Route::get('/deleteCj/{id}', [CajaController::class, 'deleteCaja'])->name('deleteCj');
Route::get('/editCj/{id}', [CajaController::class, 'editCajas'])->name('editCj');
Route::post('/saveC', [CajaController::class, 'saveCangecajas'])->name('saveC');
Route::get('/regInicial', [CajaController::class, 'registroIni'])->name('regInicial');
Route::get('/turnoOpen', [CajaController::class, 'turnoOpen'])->name('turnoOpen');
Route::post('/verificar', [CajaController::class, 'altaRegistroFin'])->name('verificar');

//PRODUCTOS
Route::get('procedure', [ProductosController::class, 'procedimiento'])->name('procedure');
Route::get('procedure1', [ProductosController::class, 'procedimiento1'])->name('procedure1');
Route::post('/saveProduct', [ProductosController::class, 'saveNewProduct'])->name('saveProduct');
Route::get('/productos', [ProductosController::class, 'all'])->name('productos');
Route::get('/altaProd', [ProductosController::class, 'altaProductos'])->name('altaProd');
Route::post('/saveChang', [ProductosController::class, 'saveCambios'])->name('savecChang');
Route::get('/editProd/{id}', [ProductosController::class, 'editProductos'])->name('editProd');
Route::get('/action', [ProductosController::class, 'action'])->name('action');
Route::get('/viewFiltro', [ProductosController::class, 'viewFiltro'])->name('viewFiltro');
Route::get('/deletePr/{id}', [ProductosController::class, 'deleteProd'])->name('deletePr');

// INVENTARIOS
Route::get('/viewInv/{id}', [InventariosController::class, 'viewInvent'])->name('viewInv');

// VENTAS
Route::get('/viewVents', [VentasController::class, 'viewVentas'])->name('viewVents');
Route::post("/buscarProducto", [VentasController::class, 'buscarProducto'])->name("buscarProducto");

Route::post("/terminarOCancelarVenta", [VentasController::class, 'terminarOCancelarVenta'])->name("terminarOCancelarVenta");
Route::post('/agregaCantidad ', [VentasController::class, 'agregarCantidadProducto'])->name('agregaCantidad');

Route::post('/addDelete', [VentasController::class, 'addOrDeletProduct'])->name('addDelete');

Route::get('/producto', [VentasController::class, 'producto'])->name('producto');
Route::get('/verifica', [VentasController::class, 'verificaPrecio'])->name('verifica');
Route::get('/agrega', [VentasController::class, 'agregaVarios'])->name('agrega');
Route::get('/cobrar', [VentasController::class, 'cobrar'])->name('cobrar');
Route::get('/cobrar', [VentasController::class, 'cobrar'])->name('cobrar');


//MAYOREO
Route::get('/mayoreo', [MayoreoController::class, 'viewMayoreo'])->name('mayoreo');
Route::post('/recibe', [MayoreoController::class, 'recibeDatosmayoreo'])->name('recibe');
Route::get('/llenar', [MayoreoController::class, 'llenarInput'])->name('llenar');
Route::get('/allM', [MayoreoController::class, 'allmayoreo'])->name('allM');
Route::get('/newM', [MayoreoController::class, 'newmayoreo'])->name('newM');
Route::get('/viewEdit/{id}', [MayoreoController::class, 'viewEditmayoreo'])->name('viewEdit');
Route::post('/datosM', [MayoreoController::class, 'recibeMayoreo'])->name('datosM');
Route::get('/deleteM/{id}', [MayoreoController::class, 'deleteMayoreo'])->name('deleteM');


//CLIENTES


Route::resource('client', ClientController::class);

Route::get('/viewClientes', [ClientController::class, 'index'])->name('viewClientes');
Route::get('/deleteClients', [ClientController::class, 'destroy'])->name('deleteClients');

// REPORTES
Route::get('/viewReportes', [ReportController::class, 'ViewReportes'])->name('viewReportes');
Route::get('/viewHistorial', [ReportController::class, 'viewHistorial'])->name('viewHistorial');
Route::get('/reporte', [ReportController::class, 'reporte'])->name('reporte');
Route::post('/pdfs', [ReportController::class, 'getGenerar'])->name('pdfs');

//ENTRADAS Y SALIDAS
Route::get('/entrada', [EntradaSalidaController::class, 'viewEntrada'])->name('entrada');
Route::post('/save', [EntradaSalidaController::class, 'saveMovimientos'])->name('save');
//search

Route::get('/search', [VentasController::class, 'search'])->name('search');
//historial
Route::get('/gets', [HistorialController::class, 'gets'])->name('gets');

//Route::get('/actions', [HistorialController::class, 'actione'])->name('actions');
//Route::get('/actio', [HistorialController::class, 'action'])->name('actio');