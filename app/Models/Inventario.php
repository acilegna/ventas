<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventarios';
    protected $primaryKey = 'id';
    public $timestamps = true;
    //lista blanca atributos que deberían ser asignables en masa
    protected $fillable =
    [
        'descripcion',
        'cantidad_inicial',
        'cantidad',
        'costo_unitario',
        'costo_despues'
    ];


    //Uno a Muchos (Inverso)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_employee');
    }
    //Uno a Muchos (Inverso)
    public function producto()
    {
        return $this->belongsTo(Product::class, 'id');
    }
}