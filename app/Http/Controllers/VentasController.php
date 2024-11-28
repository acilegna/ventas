<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Product;
use App\Models\Sell;
use App\Models\SellProduct;
use App\Models\Mayoreo;
use App\Models\MovePayment;

class VentasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function funcion()
    {

        $alert = "";
        session(['alert' => $alert]);
        $alert = session('alert');
    }

    public function terminarOCancelarVenta(Request $request)
    {

        //  $ip  = getenv("COMPUTERNAME") ;
        if ($request->input("accion") == "terminar") {

            $incrementa = 0;
            $pago_venta = $request->input("pago");
            $cambio_venta = $request->input("cambio");

            $total_produtos = $this->totalProductos();
            $venta = new Sell();

            $venta->pago = $pago_venta;
            $venta->cambio = $cambio_venta;
            $venta->total = 0;
            $venta->cantProducts = $total_produtos;
            $venta->hora = date("h:i:s");
            $venta->fecha = date('y-m-d');
            $venta->save();

            $incrementa++;
            $id_user = Auth()->user()->id_employee;

            $id_usu_openturno = MovePayment::getTurnoOpen($id_user);

            $usuario = $id_usu_openturno[0]->id_usu;

            $nventas = $id_usu_openturno[0]->numero_ventas;
            $numeroV = ($incrementa + $nventas);
            MovePayment::updatVentas($usuario, $numeroV);
            return $this->terminarVenta($request);
        } else {
            return $this->cancelarVenta();
        }
    }

    public function terminarVenta(Request $request)
    {
        //$id_cli= $request->input("id_cliente");
        $total_venta = $request->input("totalP");

        $productos = $this->obtenerProductos();


        // Recorrer arreglo carrito de compras
        foreach ($productos as $producto) {
            // El producto que se vende mandar datos para agregar en venta...  
            $venta = Sell::latest('id')->first();
            $id_venta = $venta["id"];

            /*  $host  = getenv("COMPUTERNAME");
            $client = CashBox::getnameclient($host);
            $idcaja = $client[0]['id'];

            $ticket = new Ticket();

            $ticket->fill([
                "id_caja" =>  $idcaja
            ]);
            //guardar en 
            $ticket->saveOrFail();
            $lastticket = Ticket::latest('id')->first();
            $id_tickt = $lastticket["id"];
 */

            $producto_vendido = new SellProduct();
            $producto_vendido->fill([
                "id_venta" => $id_venta,
                "id_producto" => $producto->id,
                "id_ticket" => 0,
                "precio" => $producto->p_venta,
                "cantidad" => $producto->cantidad,
            ]);

            //MOVIMIENTOS CAJA 
            //consultar turnos abiertos
            $movimientos = MovePayment::turnOpen();
            $id = $movimientos[0]->id;
            $acomuladoVentas = $movimientos[0]->acomulado_ventas;
            $acomuladoVentas += $total_venta;
            //modificar em movimientos de caja
            $move = MovePayment::find($id);
            $move->acomulado_ventas = $acomuladoVentas;
            $move->save();

            //VENTAS
            //modificar en ventas 
            $venta->total = $total_venta;
            $venta->save();
            //guardar en 
            $producto_vendido->saveOrFail();
            // restar al original
            $productoActualizado = Product::find($producto->id);
            $productoActualizado->existencia -=  $producto_vendido->cantidad;
            $productoActualizado->saveOrFail();
        }

        //sacar ultimo registro de producto vendid
        $host  = getenv("COMPUTERNAME");
        $client = CashBox::getnameclient($host);
        $idcaja = $client[0]['id'];
        $userLog = auth()->id();

        $ticket = new Ticket();

        $ticket->fill([
            "id_caja" =>  $idcaja,
            "id_user" =>  $userLog
        ]);
        //guardar en 
        $ticket->saveOrFail();
        $lastticket = Ticket::latest('id')->first();
        $id_tickt = $lastticket["id"];

        //update productos vendidos
        $lastId = Sell::latest('id')->first();
        $id_sell = $lastId["id"];

        SellProduct::updateTicket($id_tickt, $id_sell);

        $this->vaciarProductos();
        return redirect()->route("viewVents")->with("mensaje", "Venta terminada");
    }

    public function cancelarVenta()
    {
        $this->vaciarProductos();
        return redirect()
            ->route("viewVents")
            ->with("mensaje", "Venta cancelada");
    }

    private function obtenerProductos()
    {

        //sacar ´productos  almacenada en sesion        
        $productos = session("productos");

        //"Si no es algo".
        //if(!$variable)es lo mismo if($variable == false)que comprueba si $ variable es falsa.
        if (!$productos) {
            $productos = [];
        }

        return $productos;
    }

    private function vaciarProductos()
    {
        $this->guardarProductos(null);
    }

    private function guardarProductos($productos)
    {
        //guardar productos     sesion
        session([
            "productos" => $productos,
        ]);
    }

    public function buscarProducto(Request $request)
    {
        //cantidad de productos agregados
        $cantidad = $request->post("cantidad");
        $codigo_b = $request->post("codigoProducto");
        //var_dump($cantidad);

        //recibir valor de boton presionado
        $value_varios = $request->input('agregarVarios');
        $value_uno = $request->input('addOne');

        $producto = Product::where("codigo", "=", $codigo_b)->first();


        //"Si no hay algo..esta vacio"
        if (!$producto) {
            return redirect()
                ->route("viewVents")
                ->with("mensaje", "Producto no encontrado");
        }

        $this->agregarProductoTabla($producto, $codigo_b, $cantidad, $value_varios, $value_uno);
        return redirect()->route("viewVents");
    }


    private function agregarProductoTabla(
        $producto,
        $codigo_b,
        $cantidad = 0,
        $value_varios = "0"
        //$value_uno = "0"
    ) {

        //verificar si  el producto tiene existencia como 0
        if ($producto->existencia <= 0) {
            return redirect()->route("viewVents")
                ->with([
                    "mensaje" => "No hay existencias del producto",
                    "tipo" => "danger"
                ]);
        }

        $productos = $this->obtenerProductos();


        $codigo_b = $producto->codigo;
        $indice = $this->buscarIndice($codigo_b, $productos);


        if ($indice === NULL and $cantidad < 1) {
            array_push($productos, $producto);
            $this->guardarProductos($productos);
            $producto->cantidad = 1;
        } elseif ($indice === NULL and $cantidad > 1) {
            //agregar varios            
            array_push($productos, $producto);
            $this->guardarProductos($productos);
            $producto->cantidad = $cantidad;
            //para mayoreo

        } elseif ($indice !== NULL and $cantidad < 2) {

            if ($productos[$indice]->cantidad  + 1 > $producto->existencia) {
                return redirect()->route("viewVents")
                    ->with([
                        "mensaje" => "No se pueden agregar más productos de este tipo, se quedarían sin existencia",
                        "tipo" => "danger"
                    ]);
            }
            $productos[$indice]->cantidad++;
        } elseif ($indice !== NULL and $value_varios == "1") {


            if ($productos[$indice]->cantidad  + $cantidad > $producto->existencia) {
                return redirect()->route("viewVents")
                    ->with([
                        "mensaje" => "No se pueden agregar más productos de este tipo, se quedarían sin existencia",
                        "tipo" => "danger"
                    ]);
            }
            $productos[$indice]->cantidad = $productos[$indice]->cantidad + $cantidad;
        } //fin else if

    } //fin funcion  


    // buscar indice dentro del arreglo
    private function buscarIndice($codigo, $productos)
    {

        foreach ($productos as $indice => $producto) {
            if ($producto->codigo === $codigo) {
                //manda la variable para que incremente la cantidad agregada
                //$sumaProduct=$producto->cantidad++;
                //$indice=array($indice,$sumaProduct);
                return  $indice;
            }
        }
    }

    public function addOrDeletProduct(Request $request)
    {


        if ($request->input("accion") == "agrega") {


            $codigo = $request->input("codigo");

            $producto = Product::where("codigo", "=", $codigo)->first();
            $this->agregarProductoTabla($producto, $codigo);
        } else if ($request->input("elimina") == "elimina") {
            $codigo = $request->input("codigo");
            $indice = $request->input("indice");
            $cantidad = $this->quitarProductoDeCaja($codigo, $indice);
            if ($cantidad < 1) {
                $productos = $this->obtenerProductos();
                array_splice($productos, $indice, 1);
                $this->guardarProductos($productos);
            }
        }
        return redirect()->route("viewVents");
    }
    public function quitarProductoDeCaja($codigo, $indice)
    {
        $productos = $this->obtenerProductos();
        foreach ($productos as  $producto) {
            if ($producto->codigo == $codigo) {
                $cantidad = $producto->cantidad;
                $cantidad = $cantidad - 1;
                $producto->cantidad = $cantidad;
                return  $cantidad;
            }
        }
    }


    public function producto(Request $request)
    {
        if ($request->ajax()) {
            $output = '';
            $query = $request->get('query');

            if ($query != '') {
                //hace el filtro
                $data = Product::searchProduct($query);
            }

            //si existe 
            if (isset($data)) {
                $total_row = $data->count();
                if ($total_row > 0) {
                    $output = $data;
                } else {
                    $output = ["No hay registros"];
                }
                $data = array('table_data'  => $output, 'total_data'  => $total_row);
                echo json_encode($data);
            }
        }
    }

    public function verificaPrecio(Request $request)
    {
        if ($request->ajax()) {
            $output = '';
            $query = $request->get('query');

            if ($query != '' and strlen($query) >= 4) {
                $data = Product::searchByCodigo($query);
            }
            if (isset($data)) {
                $total_row = $data->count();
                if ($total_row > 0) {
                    $output = $data;
                } else {
                    $output = ["Registro no encontrado en la Base de Datos"];
                }
                $data = array(
                    'table_datos'  => $output,
                    'total_datos'  => $total_row
                );
                echo json_encode($data);
            }
        }
    }

    public function agregaVarios(Request $request)
    {
        if ($request->ajax()) {

            $output = '';
            $data = '';
            //query es el input  txtbusca
            $query = $request->get('query');
            //si no esta vacio y su longitud es mayorigual a 4   
            if ($query != '' and strlen($query) >= 4) {

                $data = Product::searchByCodigo($query);
            }
            if ($data != '') {
                $total_row = $data->count();
                if ($total_row > 0) {
                    $output = $data;
                } else {

                    $output = ["No hay registros"];
                }
                $data = array(
                    'table_datos'  => $output,
                    'total_datos'  => $total_row,
                );
                echo json_encode($data);
            }
        }
    }
    public function search()
    {
        return view(
            "search.allSearch",

        );
    }

    public function viewVentas()
    {
        //productos graegados a la venta
        $total_produtos = $this->totalProductos();
        //total venta
        $total = $this->mayoreo();
        return view(
            "ventas.ventas",
            [
                "total_produtos" => $total_produtos,
                "total" => $total,
            ]
        );
    }

    public function mayoreo()
    {
        $total = 0;
        $sql_mayoreos = 0;
        $sql_precio = 0;
        $var = 0;
        foreach ($this->obtenerProductos() as $producto) {
            $id_agregado = $producto->id;
            $cantidad_p = $producto->cantidad;
            $sql_mayoreos = Mayoreo::where("id_prod", "=", $id_agregado)->get();
            $sql_precio = Product::where("id", "=", $id_agregado)->get();
            //si producto no esta dado de alta con mayoreo y regresa vacia consulta
            if ($sql_mayoreos->isEmpty()) {
                $total += $producto->cantidad * $producto->p_venta;
                //para enviar variable indicando que hay un producto no registrado con mayoreo
                $var = 1;
            } else {
                foreach ($sql_mayoreos as  $productos_mayoreo) {
                    $id_prod = $productos_mayoreo->id_prod;
                    $precio_mayoreo = $productos_mayoreo->p_mayoreo;
                    $cantidad_mayoreo = $productos_mayoreo->cantidad;

                    if ($cantidad_p >= $cantidad_mayoreo and $id_agregado == $id_prod) {
                        $producto->p_venta = $precio_mayoreo;
                        $precioMayoreo =  $precio_mayoreo;
                    }
                    foreach ($sql_precio as  $precio) {
                        $producto->p_venta;
                    }
                    if ($cantidad_p < $cantidad_mayoreo) {
                        $producto->p_venta = $precio->p_venta;
                    }
                    $total += $producto->cantidad * $producto->p_venta;
                }
            }
        }
        $total = array($total, $var);
        return ($total);
    }


    public function totalProductos()
    {

        $total_produtos = 0;
        foreach ($this->obtenerProductos() as $reporte) {
            $total_produtos += $reporte->cantidad;
        }
        return $total_produtos;
    }

    //funcion cobrar
    public function cobrar(Request $request)
    {
        if ($request->ajax()) {
            // if ($request->input("accion") == "cobra") {
            $total_produtos = $this->totalProductos();
            $total = $this->mayoreo();
            // }
            $data = array(
                'total_articulos'  => $total_produtos,
                'total_pagar'  => $total[0]
            );
            echo json_encode($data);
        }
    }
}