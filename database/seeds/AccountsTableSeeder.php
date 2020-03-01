<?php
use App\User;
use App\Models\Acao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountsTableSeeder extends Seeder
{
    private $accounts = [
        ["superadmin@mail.com", "111111", "Super Admin","ROLE_ADMIN"]
//        ["superadmin@agroproject.com", "111111", "Super Admin","ROLE_ADMIN"]
    ];

    public function run(){
        foreach ($this->accounts as $account){
            $this->insertOrUpdate($account);
        }
    }

    private function insertOrUpdate($newAccount)
    {

        $usuario = User::firstOrNew(array('email' => $newAccount[0]));
        $usuario->password = Hash::make($newAccount[1]);
        $usuario->name = $newAccount[2];
        $usuario->save();

        $usuario->roles()->sync(Acao::where('codigo', $newAccount[3])->first()->id);
    }

}
