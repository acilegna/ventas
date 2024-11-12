<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'ticket';
    protected $primaryKey = 'id';
    public $timestamps = true;
    //lista blanca atributos que deberían ser asignables en masa
    protected $fillable = ['id_caja' ];


    //uno a muchos
    public function ticketenproductosVendido()
    {
        return $this->hasMany(SellProduct::class, 'id_ticket');
    }


}