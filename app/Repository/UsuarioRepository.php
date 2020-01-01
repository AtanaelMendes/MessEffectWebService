<?php
namespace App\Repository;


use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UsuarioRepository extends BaseRepository
{
    protected $model;

    /**
     * AccountRepository constructor.
     * @param Usuario $usuario
     */
    public function __construct(Usuario $usuario)
    {
        $this->model = $usuario;
    }

    public function searchByEmailAndType($email, $type)
    {
        Log::info("Filtrando contas com: email=" . $email . ($type ? ", type=" . $type : ""));

        $type = $type == "" ? null : $type;
        $email = $email == "" ? null : $email;

        if($type == "trashed"){
            //onlyTrashed
            if($email){
                return DB::table("usuario")
                    ->where('email', 'ilike', '%' . $email . '%')
                    ->orderBy('created_at', 'desc')
                    ->whereNotNull('deleted_at')
                    ->get();
            }else{
                return DB::table("usuario")
                    ->whereNotNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }else if($type == "non-trashed"){
            //passar false
            if($email){
                return DB::table("usuario")
                    ->where('email', 'ilike', '%' . $email . '%')
                    ->orderBy('created_at', 'desc')
                    ->whereNull('deleted_at')
                    ->get();
            }else{
                return DB::table("usuario")
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        if($email){
            return DB::table("usuario")
                ->where('email', 'ilike', '%' . $email . '%')
                ->orderBy('created_at', 'desc')
                ->get();
        }else{
            return DB::table("usuario")->orderBy('created_at', 'desc')->get();
        }
    }
}
