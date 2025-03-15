<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 
class Cliente extends Model
{
     protected $table = 'clientes';
     protected $primaryKey = 'id';
     public $timestamps = true;
     protected $fillable =
     [
          'nombre', 'apellidos', 'telefono',
          'direccion'
     ];

 
}