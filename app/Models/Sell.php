<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sell extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id';
    public $timestamps = true;

    //lista blanca atributos que deberían ser asignables en masa
    protected $fillable =
    [ 'pago','cambio', 'total', 'cantProducts', 'fecha','hora'];

    //uno a muchos
    public function ventasenproductosVendido()
    {
        return $this->hasMany(SellProduct::class, 'id_venta');
    }
}