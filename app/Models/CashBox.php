<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBox extends Model
{
    protected $table = 'cajas';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $dateFormat = 'Y-m-d'; //determina el formato de como se almacenan los atributos de fecha en bd
    //en createdat aqui solo se guardaria fecha

    //lista blanca atributos que deberían ser asignables en masa
    protected $fillable = ['id', 'descripcion', 'nameclient', 'status'];

    //uno a muchos
    public function cajaenmovimientocaja()
    {
        return $this->hasMany(MovePayment::class, 'id_caja');
    }

    //actualizar status al cerrar caja
    public static function updateStatusActive($sesionId_caja)
    {
        $valor = self::where("id", $sesionId_caja)->update(["status" => "1"]);
        return $valor;
    }

    public static function getQuery($query)
    {
        return self::where('id', 'like', '%' . $query . '%')
            ->orWhere('status', 'like', '%' . $query . '%')
            ->orWhere('descripcion', 'like', '%' . $query . '%')
            ->orderBy('id', 'desc')
            ->get();
    }
    public static function getAll()
    {
        return self::orderBy('id', 'desc')->get();
    }
    public static function getBox($idCaja)
    {
        return self::where('id', $idCaja)->get();
    }
    public static function updateBoxActive($id)
    {
        return self::where("id", $id)->update(["status" => "1"]);
    }
    public static function updateBoxInactive($id_caja)
    {
        return self::where("id", $id_caja)->update(["status" => "0"]);
    }
    public static function  BoxActive()    {

        return self::where('status', 1)->get();
    }


    public static function getnameclient($nameclient)
    {
        return self::where('nameclient', $nameclient)->get();
    }
}