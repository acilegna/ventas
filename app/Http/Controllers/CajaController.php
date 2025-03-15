<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use App\Models\MoveClosing;
use App\Models\MovePayment;
use App\Models\Sell;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //comentario
    }
    public function funcion()
    {
        $alert = '';
        //guardar id de caja en una sesion
        session(['alert' => $alert]);
        $alert = session('alert');
    }

    //funcion mostrar para registro de dinero
    public function registroIni()
    {
        return view('cajas.registroInicial');
    }

    //funcion para dar de alta cajas
    public function vistaCaja()
    {
        return view('cajas.cajas');
    }
    //funcion para mostrar todas las cajas
    public function allCajas()
    {
        return view('cajas.allCajas');
    }
    //eliminar cajas
    public function deleteCaja($parameters)
    {
        $cajas = CashBox::find($parameters);
        if ($cajas->id != null) {
            $cajas->delete();
            return redirect()->route('allcaja')->with([
                'mensaje' => 'Caja eliminada correctamente!!',
                'tipo' => 'info',
            ]);
        } else {
            return redirect()->route("allcaja")->with([
                'mensaje' => 'No se encontro registro',
                'tipo' => 'danger',
            ]);
        }
    }

    //guardar cambios de modificacion de cajas
    public function saveCangecajas(Request $request)
    {
        $id = $request->inputId;
        $cajas = CashBox::find($id);
        if ($cajas->id != null) {
            $cajas->descripcion = $request->inputDescripcion;
            $cajas->status = $request->stcaja;
            $cajas->save();
            return redirect()->route("allcaja")->with([
                'mensaje' => 'Datos actualizados correctamente!!',
                'tipo' => 'info',
            ]);
        } else {
            return redirect()->route("allcaja")->with([
                'mensaje' => 'Los datos no se han actualizado!!',
                'tipo' => 'danger',
            ]);
        }
    }

    public function editCajas($param)
    {
        $res = CashBox::getBox($param);
        return view('cajas.editCajas', ['res' => $res]);
    }

    public function cajasAjax(Request $request)
    {
        if ($request->ajax()) {
            // $output = '';
            $query = $request->get('query');

            if ($query != '') {
                //hace el filtro
                $data = CashBox::getQuery($query);
            } else {
                //muestra todos los datos
                $data = CashBox::getAll();
            }
            $total_row = $data->count();

            $output = $data;
            $data = array(
                'table_data' => $output,
                'total_data' => $total_row,
            );
            echo json_encode($data);
        }
    }

    public function newCaja(Request $request)
    {
        $validate = $this->validate($request, [
            'inputCaja' => 'required|unique:cajas,descripcion',
            'inputStatus' => 'required|nullable',
        ]);
        $nameCaja = $request->inputCaja;
        $host  = getenv("COMPUTERNAME");

        //recoger valor seleccionado  
        $status = $request->get('inputStatus');
        $caja = new CashBox;
        $caja->descripcion = $nameCaja;
        $caja->nameclient = $host;
        $caja->status = $status;
        $caja->save();
        return redirect('allcaja');
    }



    public function turnoOpen()
    {
        return view('cajas.turnoOpen');
    }

    //sacar el total de las ventas del ultimo registro
    public function totalVenta()
    {
        $venta = Sell::latest('id')->first();
        $total_venta = $venta['total'];
        return $total_venta;
    }

    // Obtener usuario logueado y fechaHora
    public function obtenerDatos()
    {
        //id usuario logueado
        $id_user = Auth()->user()->id_employee;
        $fechaHora = now();
        //almacenar en arreglo los datos a enviar
        $datos = [$id_user, $fechaHora];
        return $datos;
    }


    public function altaRegistroFin(Request $request)
    {
        $totalCaja = $this->sumaTotalTurnoOpen();
        $efectivoCaja = $request->efectivoFinal;
        $id_user = session('id_usuTurno');
        $datos = $this->obtenerDatos();

        //sacar datos del arreglo
        $fechaHora = $datos[1];
        //consulta para comparar total en caja con efectivo al cierre
        $efectivo = MovePayment::getTurnoOpen($id_user);

        $totalSuma = $efectivo[0]->total_caja;
        $id_mov = $efectivo[0]->id;
        //contar total de id, si hay mas de 3 bloquear pantalla y solicitar contraseña al admin
        $ids = MoveClosing::getTotalId($id_mov);

        //compara efectivo fisico en caja con el total en la bd
        if ($totalSuma == $efectivoCaja) {
            //obtener id de caja con turno abierto
            $consultaMovcaja = MovePayment::getTurnoOpen($id_user);
            if ($consultaMovcaja != null) {
                $idCaja = $consultaMovcaja[0]->id_caja;
                $caja = CashBox::updateBoxActive($idCaja);
                //obtiene registro del  movimiento con usuario logueado y status abierto y modifica
                //pendiente
                $Movcaja = MovePayment::updateStatus($id_user,  $fechaHora,  $totalCaja, $efectivoCaja);
                //destruir sesion
                session()->forget('id_usuTurno');
                Auth::logout();
                return redirect('/');
            }
        } elseif ($ids >= 3) {
            // si hay 3 registros con el mismo id regresar ventana para solicitar al admin el desbloqueo
            return view('cajas.registroFinal')->with('alert', 'Mensaje no Vacio');
        } elseif ($ids <= 3) {
            $data = [
                'id_user' => $id_user,
                'id_mov' => $id_mov,
                // 'fechaHora' => $fechaHora,
            ];

            MoveClosing::create($data);
            return view('cajas.registroFinal')->with('alert');
        }
    }



    public function envia(Request $request)
    {
        //recibir id del usuario que continuaremos turno
        $id_usuTurno = $request->Opcioncaja;
        $sesionId_usuTurno = session('id_usuTurno');
        //guardar id de usuario  usuario que continuaremos turno en una sesion
        session(['id_usuTurno' => $id_usuTurno]);
        if ($request->input('cerrarTurno') == 'closeTurno') {

            //usar funcion para enviar variable alert
            $this->funcion();
            //se envia vacia
            return view('cajas.registroFinal')->with('alert');
        } else {

            return redirect('/welcome');
        }
    }

    //cerrar caja  despues de abrir turno
    public function cerrarCaja($fechaHora, $sesionId_caja, $id_usu)
    {
        //obtiene registro del  movimiento con usuario logueado y status abierto
        $Movcaja = MovePayment::updateStatus($id_usu, $fechaHora);
        //actualizar caja a disponible despues de cerrar
        $caja = CashBox::updateStatusActive($sesionId_caja);
    }
    //funcion Cerrar turno que estuvo abierto
    public function cerrarTurno($fechaHora, $sesionId_usuTurno)
    {
        //obtener datos con sttaus abierto
        $consultaMovcaja = MovePayment::getTurnoOpen($sesionId_usuTurno);
        foreach ($consultaMovcaja as $key => $value) {
            $idCaja = $value->id_caja;
            $caja = CashBox::updateBoxActive($idCaja);
            //pendeintes
            //$Movcaja = MovePayment::updateTurnoOpen($sesionId_usuTurno, $efectivoCaja = 0, $fechaHora);
        }
    }


    public function sumaTotalTurnoOpen()
    {
        $datos = $this->obtenerDatos();
        //sacar datos del arreglo
        $id_usu = $datos[0];
        $regis = MovePayment::getTurnoOpen($id_usu);
        $dinero_ini = $regis[0]->dinero_inicial;
        $acomulado_v = $regis[0]->acomulado_ventas;
        $acomulado_entra = $regis[0]->acomulado_entradas;
        $acomulado_sa = $regis[0]->acomulado_salidas;
        $totalCaja = ($dinero_ini +  $acomulado_entra + $acomulado_v) - ($acomulado_sa);
        return $totalCaja;
    }


    public function saveMovimientoscaja($id_caja, $inicial, $id_usu)
    {
        //Crear registro.
        $status = 'abierto';
        $data = [
            'id_caja' => $id_caja,
            'id_usu' => $id_usu,
            'dinero_inicial' => $inicial,
            'status' => $status,
        ];
        MovePayment::create($data);
    }


    public function operacionCaja(Request $request)
    {
        // Obtener usuario logueado y fechaHora
        $datos = $this->obtenerDatos();
        //sacar datos del arreglo
        $id_usu = $datos[0];
        $fechaHora = $datos[1];
        //recibir dinero inicial
        $inicial = $request->inicial;
        //recibir id de la caja que inicia sesion
        $id_caja = $request->caja;
        //modificar status de caja  ponerla  inactiva
        $caja = CashBox::updateBoxInactive($id_caja);

        if ($request->input('registrar') == 'regCaja') {
            $this->saveMovimientoscaja($id_caja, $inicial, $id_usu);
            return redirect('/welcome');
        }

        if ($request->input('cerrar') == 'close') {

            $id = $this->getIdcaja();
            $this->sumarTotal($value = 1);
            $this->cerrarCaja($fechaHora, $id, $id_usu);
            $caja = CashBox::updateBoxActive($id);

            Auth::logout();
            return redirect('/');
        }
        if ($request->input('mantener') == 'open') {
            $id = $this->getIdcaja();
            $this->sumarTotal($value = 2);
            $caja = CashBox::updateBoxActive($id);

            Auth::logout();
            return redirect('/');
        }
    }



    public function getIdcaja()
    {
        $datos = $this->obtenerDatos();
        $id_usu = $datos[0];
        $idcaja = MovePayment::getTurnoOpen($id_usu);
        $id = $idcaja[0]->id_caja;
        return $id;
    }

    public function sumarTotal($value)
    {
        $datos = $this->obtenerDatos();
        //sacar datos del arreglo
        $id_usu = $datos[0];
        $fechaHora = $datos[1];
        $totalCaja = $this->sumaTotalTurnoOpen();
        if ($value == 1) {
            MovePayment::updateStatus($id_usu, $fechaHora, $totalCaja);
        } else {
            MovePayment::updateCaja($id_usu, $fechaHora, $totalCaja);
        }
    }
}