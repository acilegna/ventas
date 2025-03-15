<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entradasalida extends Model
{
    protected $table = 'entradasalidas';
    protected $primaryKey = 'id_es';
    public $timestamps = false;
   
    
    protected $fillable =
    [
        'id_user', 'id_caja', 'cantidad', 'tipo', 'comentario', 'hora_fecha'
    ];


    //relacion uno a muchos
    public function cajas()
    {
        return $this->hasMany(CashBox::class, 'id');
    }
}