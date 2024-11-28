<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\alert;

class SellProduct extends Model
{
	protected $table = 'productos_vendidos';
	protected $primaryKey = 'id';
	public $timestamps = true;

	//lista blanca atributos que deberían ser asignables en masa
	protected $fillable =
	[
		'id_venta',
		'id_user',
		'id_producto',
		'id_ticket',
		'precio',
		'cantidad'
	];

	//Uno a Muchos (Inverso)
	public function producto()
	{
		return $this->belongsTo(Product::class, 'id_producto');
	}

	/* 	public function usuario()
	{
		return $this->belongsTo(User::class, 'id_user');
	} */

	public function venta()
	{
		return $this->belongsTo(Sell::class, 'id_venta');
	}
	public function ticket()
	{
		return $this->belongsTo(Ticket::class, 'id_ticket');
	}


	public static function sellProductByUser($user, $date1, $date2)
	{
		return self::join('users', 'users.id_employee', '=', 'productos_vendidos.id_user')
			->join('ventas', 'ventas.id', '=', 'productos_vendidos.id_venta')
			->join('productos', 'productos.id', '=', 'productos_vendidos.id_producto')
			->select('ventas.id', 'ventas.fecha', 'users.firstname', 'productos.p_venta', 'productos_vendidos.cantidad', 'ventas.total', 'productos.descripcion')
			->where('users.id_employee', '=', $user)
			->whereBetween('ventas.fecha', [$date1, $date2])
			->get();
	}
	public static function sellProducts($date1, $date2)
	{
		return self::join('users', 'users.id_employee', '=', 'productos_vendidos.id_user')
			->join('ventas', 'ventas.id', '=', 'productos_vendidos.id_venta')
			->join('productos', 'productos.id', '=', 'productos_vendidos.id_producto')
			->select('ventas.id', 'ventas.fecha', 'users.firstname', 'productos.p_venta', 'productos_vendidos.cantidad', 'ventas.total', 'productos.descripcion')
			->whereBetween('ventas.fecha', [$date1, $date2])
			->get();
	}




	public static function updateTicket($id_tickt, $idventa)
	{
		return self::where("id_venta", "=", $idventa)->update([
			"id_ticket" => $id_tickt,

		]);
	}


	public static  function alls()

	{


		/* return self::join('ventas', 'ventas.id', '=', 'productos_vendidos.id_venta')
			->join('ticket', 'ticket.id', '=', 'productos_vendidos.id_ticket')
			->select('productos_vendidos.id_ticket', 'ventas.hora', 'ventas.total', 'ventas.cantProducts')->distinct('productos_vendidos . id_ticket')
			->get(); */



		/* 	return self::select('id_ticket', 'id_venta', 'id_user')->distinct()->with(['venta' => function ($sql) {
			$sql->select('id', 'cantProducts', 'hora', 'total');
		}])->with(['ticket' => function ($sql) {
			$sql->select('id');
		}])->get(); */

		return self::select('id_ticket', 'id_venta', 'id_user', 'id_producto', 'cantidad')->distinct()->with(['venta' => function ($sql) {
			$sql->select('id', 'cantProducts', 'hora', 'total', 'pago');
		}])->with(['ticket' => function ($sql) {
			$sql->select('id');
		}])->with(['producto' => function ($sql) {
			$sql->select('id', 'descripcion', 'p_venta');
		}])->get();
	}



	public static function searchs($query)
	{

		/* 		return self::select('id_ticket', 'id_venta', 'id_user', 'id_producto', 'cantidad')->distinct()->with(['venta' => function ($sql) {
			$sql->select('id', 'cantProducts', 'hora', 'total', 'pago');
		}])->with(['producto' => function ($sql) {
			$sql->select('id', 'descripcion', 'p_venta');
		}])->with(['ticket' => function ($sql) {
			$sql->select('id');
		}])->whereHas('ticket', function ($q) use ($query) {
			$q->where('id', 'like', '%' . $query . '%');
		})->get();
	 */
		/* return self::select('id_ticket', 'id_venta', 'id_user', 'id_producto', 'cantidad')->distinct('id_ticket')
		->with(['venta' => function ($sql) {
			$sql->select('id', 'cantProducts', 'hora', 'total', 'pago');
		}])->with(['ticket' => function ($sql) {
			$sql->select('id');
		}])->whereHas('ticket', function ($q) use ($query) {
			$q->where('id', 'like', '%' . $query . '%');
		})->with(['producto' => function ($sql) {
			$sql->select('id', 'descripcion', 'p_venta');
		}])->get(); */

		return self::select('id_ticket', 'id_venta')->distinct()
			->with(['venta' => function ($sql) {
				$sql->select('id', 'cantProducts', 'hora', 'total', 'pago');
			}])->with(['ticket' => function ($sql) {
				$sql->select('id');
			}])->whereHas('ticket', function ($q) use ($query) {
				$q->where('id', 'like', '%' . $query . '%');
			})->get();
	}
	public static function searchstw($query)
	{
		return self::select('id_ticket', 'id_venta',  'id_producto', 'cantidad')->distinct()
			->with(['venta' => function ($sql) {
				$sql->select('id', 'cantProducts', 'hora', 'total', 'pago', 'fecha');
			}])
			->with(['ticket' => function ($sql) {
				$sql->select('id', 'id_caja', 'id_user')
					->with(['user' => function ($sql) {
						$sql->select('id_employee', 'lastname');
					}])
					->with(['caja' => function ($sql) {
						$sql->select('id');
					}]);
			}])->whereHas('ticket', function ($q) use ($query) {
				$q->where('id', $query);
			})

			->with(['producto' => function ($sql) {
				$sql->select('id', 'descripcion', 'p_venta');
			}])
			->get();

		/* return self::select('id_ticket', 'id_venta',  'id_producto', 'cantidad')->distinct()
			->with(['venta' => function ($sql) {
				$sql->select('id', 'cantProducts', 'hora', 'total', 'pago', 'fecha');
			}])
			->with(['ticket' => function ($sql) {
				$sql->select('id', 'id_caja', 'id_user')
					->with(['user' => function ($sql) {
						$sql->select('id_employee', 'lastname');
					}])
					->with(['caja' => function ($sql) {
						$sql->select('id');
					}]);
			}])->whereHas('ticket', function ($q) use ($query) {
				$q->where('id', $query);
			})

			->with(['producto' => function ($sql) {
				$sql->select('id', 'descripcion', 'p_venta');
			}])
			->get(); */
	}
}

/* return self::select('id_ticket', 'id_venta',  'id_producto', 'cantidad')->distinct()
	->with(['venta' => function ($sql) {
		$sql->select('id', 'cantProducts', 'hora', 'total', 'pago', 'fecha');
	}])
	->with(['ticket' => function ($sql) {
		$sql->select('id', 'id_caja', 'id_user')
		->with(['user' => function ($sql) {
			$sql->select('id_employee', 'lastname');
		}])
			->with(['caja' => function ($sql) {
				$sql->select('id');
			}]);
	}])->whereHas('ticket', function ($q) use ($query) {
		$q->where('id', 'like', '%' . $query . '%');
	})
	->orwhereHas('ticket', function ($q) use ($usua) {
		$q->where('id_user', $usua);
	})
	->with(['producto' => function ($sql) {
		$sql->select('id', 'descripcion', 'p_venta');
	}])
	->get(); */