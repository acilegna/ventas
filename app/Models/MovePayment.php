<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovePayment extends Model
{
    protected $table = 'movimiento_caja';
    protected $primaryKey = 'id';
    public $timestamps = false;


    public function setIdCajaAttribute($id_caja)
    {
        $this->attributes['id_caja'] = $id_caja;
    }
    public function setIdUsuAttribute($id_usu)
    {
        $this->attributes['id_usu'] = $id_usu;
    }

    public function setDineroInicialAttribute($inicial)
    {
        $this->attributes['dinero_inicial'] = $inicial;
    }
    public function setInicioEnAttribute($fechaHora)
    {
        $this->attributes['inicio_en'] = $fechaHora;
    }
    public function setStatusAttribute($status)
    {
        $this->attributes['status'] = ucfirst($status);
    }
    public function setTotalCajaAttribute($totalcaja)
    {
        $this->attributes['total_caja'] = ucfirst($totalcaja);
    }

    //lista blanca atributos que deberían ser asignables en masa
    protected $fillable =
    [
        'id_caja',
        'id_usu',
        'dinero_inicial',
        'acomulado_ventas',
        'acomulado_entradas',
        'acomulado_salidas',
        'efectivo_cierre',
        'total_caja',
        'numero_ventas',
        'status',
        'inicio_en',
        'termino_en'
    ];



    //Uno a Muchos (Inverso)
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_employee');
    }

    public static function updateStatus($id_user, $fechaHora, $opcion = 9, $cierre = 0)
    {
        return self::where("id_usu", "=", $id_user)->where("status", "=", "Abierto")->update([
            "efectivo_cierre" => $cierre,
            "total_caja" => $opcion,
            "status" => "cerrado",
            "termino_en" => $fechaHora
        ]);
    }
    //obtener datos de la sesion abierta
    public static function getTurnoOpen($id_user)
    {

        return self::where("id_usu", "=", $id_user)->where("status", "=", "Abierto")->get();
    }

    public static function updateCaja($sesionUserTurno, $fechaHora, $efectivoCaja)
    {
        return self::where("id_usu", "=", $sesionUserTurno)->where("status", "=", "Abierto")->update([

            "total_caja" => $efectivoCaja,
            "termino_en" => $fechaHora
        ]);
    }


    public static function updatVentas($id_usu_openturno, $numero)
    {
        return self::where("id_usu", "=", $id_usu_openturno)->where("status", "=", "Abierto")->update([
            "numero_ventas" => $numero
        ]);
    }

    public static function turnOpen()
    {
        return self::join('users', 'users.id_employee', '=', 'movimiento_caja.id_usu')
            ->join('cajas', 'cajas.id', '=', 'movimiento_caja.id_caja')
            ->select(
                'cajas.descripcion',
                'users.firstname',
                'movimiento_caja.id',
                'movimiento_caja.id_usu',
                'movimiento_caja.id_caja',
                'movimiento_caja.acomulado_entradas',
                'movimiento_caja.acomulado_salidas',
                'movimiento_caja.acomulado_ventas'
            )
            ->where('movimiento_caja.status', '=', 'Abierto')
            ->get();
    }
}