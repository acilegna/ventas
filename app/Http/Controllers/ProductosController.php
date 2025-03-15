<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventario;
use App\Procedures\Procedure;
use Psy\Readline\Hoa\Console;

class ProductosController extends Controller
{

  /*
  //ejemplo de PROCEDIMIENTOS   SIN PARAMETRO
   public function procedimiento()
  {
   $procedimientos = new Procedure;
   $resProcedure= $procedimientos->getProcedimiento();
   var_dump($resProcedure);
   foreach ($resProcedure as $res){
     var_dump($res->precio);
   }
  }
  //ejemplo de PROCEDIMIENTOS  CON PARAMETRO 
 public function procedimiento1()
  {
   $procedimientos1 = new Procedure;
   $resProcedure1= $procedimientos1->getProcedimiento1(44);
   var_dump($resProcedure1);
    
  } */
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function all()
  {
    //obtener datos de tu base de datos
    $productos = Product::all();

    foreach ($productos as $product) {
      echo $product->id;
    }
  }

  public function saveCambios(Request $request)
  {
    //var_dump($recibe=$request->all());

    $inventarios = new Inventario;
    //obtener ultimo id antes de insertar en inventarios
    $data = Inventario::latest('id')->first();
    $ids = $data["id"];
    //obtener el costo del ultimo id insertado 
    $inv = Inventario::where("id", "=", $ids)->first()->toArray();

    $costo_u = $inv["costo_unitario"];
    $costo_d = $inv["costo_despues"];
    $costo_compra = $request->inputPrecioc;

    $hay = $request->inputHay_hi;

    $insertedId = $request->inputId;
    $inventarios = new Inventario;
    $productos = Product::find($insertedId);
    $user = \Auth::user();
    $id_user = $user->id_employee;
    $descripcion = "Modificación de producto";

    //insertar
    $inventarios->id_usuario = $id_user;
    $inventarios->id_producto = $insertedId;
    $inventarios->descripcion = $descripcion;
    $inventarios->cantidad_inicial = $hay;
    if ($costo_d != $costo_compra) {
      $inventarios->costo_unitario = $costo_d;
      $inventarios->costo_despues = $costo_compra;
    }
    if ($costo_d == $costo_compra) {
      $inventarios->costo_unitario = $costo_u;
      $inventarios->costo_despues = $costo_d;
    }

    $inventarios->save();


    //modificar producto   
    // $productos->descripcion  = $request->inputDescripcion;
    //$productos->categoria   = $request->inputCategoria;
    $productos->p_compra    = $request->inputPrecioc;
    $productos->ganancia    = $request->inputGanancia;
    $productos->p_venta     = $request->inputPreciov;

    $productos->save();
    //despues de insertar y modificar retornar a vista productos
    return redirect('viewFiltro');
  }

  public function saveCantidadagregada(Request $request)
  {

    //var_dump($recibe=$request->all()); 
    $hay = $request->inputHay_hi;
    $cantAgregada = $request->inputAgregar;
    $insertedId = $request->inputId;
    $inventarios = new Inventario;
    //obtener ultimo id antes de insertar en inventarios
    $data = Inventario::latest('id')->first();
    $ids = $data["id"];
    //obtener el costo del ultimo id insertado 
    $inv = Inventario::where("id", "=", $ids)->first()->toArray();
    $costo_u = $inv["costo_unitario"];
    $costo_d = $inv["costo_despues"];

    $productos = Product::find($insertedId);
    $user = \Auth::user();
    $id_user = $user->id_employee;
    $descripcion = "Recepción de inventario";


    //insertar
    $inventarios->id_usuario = $id_user;
    $inventarios->id_producto = $insertedId;
    $inventarios->descripcion = $descripcion;
    $inventarios->cantidad_inicial = $hay;
    $inventarios->cantidad_agregada = $cantAgregada;
    $inventarios->costo_unitario = $costo_u;
    $inventarios->costo_despues = $costo_d;
    $inventarios->save();

    //modificar tabla productos

    //suma existencia mas can_agregar
    $total = $hay + $cantAgregada;
    $productos->existencia  = $total;
    $productos->save();
  }
  public function saveNewProduct(Request $request)
  {
    //recibe todo  lo que manda el formulario



    $productos = new Product;
    $inventarios = new Inventario;
    //Recibir valor de boton
    $value_new = $request->input('btn1');
    $value_alta = $request->input('btn2');

    $user = \Auth::user();
    $id_user = $user->id_employee;
    //alta de inventario
    if ($value_alta == "alta") {

      $this->saveCantidadagregada($request);
      return redirect('viewFiltro');
    }

    if ($value_new == "new") {


      $descripcion = "Alta inicial de producto";

      $validate = $this->validate($request, [
        'inputCodigo' => 'required|min:4|unique:productos,codigo',
        'inputDescripcion' => 'required|string|max:50|unique:productos,descripcion',
        'inputCategoria' => 'required|string|max:255',
        'inputPrecioc' => 'required',
        'inputGanancia' => 'required',
        'inputPreciov' => 'required',
        'inputExiste' => 'required'
      ]);
      // Recoger datos del formulario

      $productos->codigo      = $request->inputCodigo;
      $productos->descripcion = $request->inputDescripcion;
      $productos->categoria   = $request->inputCategoria;
      $productos->p_compra    = $request->inputPrecioc;
      $productos->ganancia    = $request->inputGanancia;
      $productos->p_venta     = $request->inputPreciov;

      $productos->existencia  = $request->inputExiste;
      $productos->status      = $request->codcaja;

      $productos->save();

       
      //obtener ultimo id insertado
      $insertedId = $productos->id;

      //guardar en tabla Inventarios
      $inventarios->id_usuario = $id_user;
      $inventarios->id_producto = $insertedId;
      $inventarios->descripcion = $descripcion;
      $inventarios->cantidad_inicial = $request->inputExiste;
      $inventarios->cantidad_agregada = 0;
      $inventarios->costo_unitario = $request->inputPrecioc;
      $inventarios->costo_despues = $request->inputPrecioc;
      $inventarios->save();
      return redirect('viewFiltro');
    }
  }

  public function  altaProductos()
  {
    return view('productos.productos');
  }

  public function  editProductos($param)
  {
    $consulta = Product::searchEditProduct($param);

    return view('productos.editproductos', ['consulta' => $consulta]);
  }

  public function  viewFiltro()
  {
    return view('productos.allProductos');
  }
  public function action(Request $request)
  {

    if ($request->ajax()) {
      $output = '';
      $query = $request->get('query');
      if ($query != '') {
        //hace el filtro
        $data = Product::searchProduct($query);
      } else {
        //muestra todos los datos
        $data = Product::searchAllProduct();
      }
      $total_row = $data->count();
      if ($total_row > 0) {
        $output = $data;
      } else {
        $output = ["No hay registros"];
      }
      $data = array(
        'table_data'  => $output,
        'total_data'  => $total_row
      );
      echo json_encode($data);
    }
  }

  public function deleteProd($parameters)
  {
    $productos = Product::find($parameters);
    $productos->delete();
    return redirect('viewFiltro');
  }
}