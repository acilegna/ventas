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
    ['total', 'pago','cambio', 'fecha'];

    //uno a muchos
    public function ventasenproductosVendido()
    {
        return $this->hasMany(SellProduct::class, 'id_venta');
    }
}