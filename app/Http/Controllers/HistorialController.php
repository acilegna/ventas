<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\SellProduct;

use Illuminate\Http\Request;

use function Laravel\Prompts\alert;

class HistorialController extends Controller
{

    public function actione(Request $request)
    {

        if ($request->ajax()) {
            $output = '';
            $query = $request->get('query');

            if ($query != '') {

                $data = SellProduct::searchs($query);
            } else {
                //muestra todos los datos
                // $data = SellProduct::alls();
            }
            //si existe 
            if (isset($data)) {
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
    }
    public function action(Request $request)
    {

        if ($request->ajax()) {
            $output = '';
            $query = $request->get('query');
            $user =  $request->get('user');

            if ($query != '' and $user != '') {

                $data = SellProduct::searchstw($query, $user);
            } /* else {
                //muestra todos los datos
                // $data = SellProduct::alls();
            } */
            //si existe 

            if (isset($data)) {
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
    }
    public function gets()
    {
        $data = SellProduct::with('venta')->get();
        var_dump($data);
    }
}