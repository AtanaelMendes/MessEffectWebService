<?php
namespace App\Repository;


use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountRepository extends BaseRepository
{
    protected $model;

    /**
     * AccountRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function searchByEmailAndType($email, $type)
    {
        Log::info("Filtrando contas com: email=" . $email . ($type ? ", type=" . $type : ""));

        $type = $type == "" ? null : $type;
        $email = $email == "" ? null : $email;

        if($type == "trashed"){
            //onlyTrashed
            if($email){
                return DB::table("users")
                    ->where('email', 'ilike', '%' . $email . '%')
                    ->orderBy('created_at', 'desc')
                    ->whereNotNull('deleted_at')
                    ->get();
            }else{
                return DB::table("users")
                    ->whereNotNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }else if($type == "non-trashed"){
            //passar false
            if($email){
                return DB::table("users")
                    ->where('email', 'ilike', '%' . $email . '%')
                    ->orderBy('created_at', 'desc')
                    ->whereNull('deleted_at')
                    ->get();
            }else{
                return DB::table("users")
                    ->whereNull('deleted_at')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        if($email){
            return DB::table("users")
                ->where('email', 'ilike', '%' . $email . '%')
                ->orderBy('created_at', 'desc')
                ->get();
        }else{
            return DB::table("users")->orderBy('created_at', 'desc')->get();
        }
    }
}
