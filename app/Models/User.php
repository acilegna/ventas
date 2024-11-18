<?php



namespace App\Models;


use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    public $timestamps = true;
    protected $primaryKey = 'id_employee';

    protected $fillable = [

        'lastname',
        'firstname',
        'email',
        'password',
    ];




    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];



    //uno a muchos
   /*  public function userenproductosVendido()
    {
        return $this->hasMany(SellProduct::class, 'id_user');
    } */

    //uno a muchos
    public function usereninventario()
    {
        return $this->hasMany(Inventario::class, 'id_usuario');
    }


    //uno a muchos
    public function userenmovimientocaja()
    {
        return $this->hasMany(MovePayment::class, 'id_usu');
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }
    public static function getUsers($id)
    {
        return self::select('users.id_employee', 'users.lastname', 'users.firstname', 'users.email', 'users.active', 'users.password')
            ->where('users.id_employee', '=',  $id)
            ->get();
    }
    public static function allUsers()
    {
        return self::select('users.id_employee', 'users.lastname', 'users.firstname', 'users.email', 'users.active')
            ->get();
    }

    public static function allUsersId($query)
    {
        return self::select('users.id_employee', 'users.lastname', 'users.firstname', 'users.email', 'users.active')
            ->where('users.lastname', 'like', '%' . $query . '%')
            ->get();
    }

    public static function saveChangesUser($datos)
    {


        $password = $datos['inputContraseña'];
        $verifypass = $datos['inputContraseña2'];

        if ($verifypass != $password) {
        } else {
            return self::where('id_employee', $datos['inputId'])->update(
                [
                    'firstname' => $datos['inputUser'],
                    'lastname' => $datos['inputApe'],
                    'email' => $datos['inputEmail'],
                    'active' => $datos['status'],
                    'password' => $datos['inputContraseña'] = Hash::make($password),

                ]

            );
        }

        User::getEmail($datos['inputId']);
        //if($datos['inputEmail']!=)
    }
    public static function deleteUsers($id)
    {
        //var_dump($id);
        return self::where('id_employee', $id)->delete();
    }



    public static function getEmail($id)
    {
        var_dump($id);
        /*   return self::select('users.email')->where('id_employee', $id);*/
    }
}