<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\AccountRequest;
use App\Repositories\RoleRepository;
use App\Repository\AccountRepository;
use App\Services\ImageService;
use App\User;

class AccountController extends Controller
{
    private $accountRepository;
    private $roleRepository;
    private $imageService;

    /**
     * AccountController constructor.
     * @param AccountRepository $accountRepository
     * @param RoleRepository $roleRepository
     * @param ImageService $imageService
     */
    public function __construct(AccountRepository $accountRepository,
                                RoleRepository $roleRepository,
                                ImageService $imageService)
    {
        $this->accountRepository = $accountRepository;
        $this->roleRepository = $roleRepository;
        $this->imageService = $imageService;
    }

    public function index(Request $request){
        $this->authorize('list_accounts');

        $type = $request->query('type');
        $email = $request->query('email');

        $items = $this->accountRepository->searchByEmailAndType($email, $type);

        //TODO: Colocar essa parte em uma função
        /*$currentAccount = Auth::user();
        foreach ($items as $key => $item) {
            if ($item->email == $currentAccount->email) {
                $items->pull($key);
            }
        }*/

        return response($items->values(), Response::HTTP_OK);
    }
    public function store(AccountRequest $request){
        $this->authorize('create_account');
        Log::info("Criando conta para: " . $request->input('email'));

        $roleIds = $this->getRolesIds($request);

        $account = $request->all();
        $account['password'] = Hash::make($account['password']);

        $createdAccount = DB::transaction(function () use ($account, $roleIds) {
            $createdAccount = $this->accountRepository->create($account);

            if (count($roleIds) > 0) {
                $createdAccount->roles()->sync($roleIds);
            }

            return $createdAccount;
        });

        $createdAccount = $this->accountRepository->findOneOrFail($createdAccount->id);
        $createdAccount->load('user');
        unset($createdAccount->user_id);
        if(count($roleIds) > 0) {
            $createdAccount->load('roles');
        }
        return response($createdAccount, Response::HTTP_CREATED);
    }

    public function info(){
        $this->authorize('show_account_info');
        Log::info("Pegando as informações da própria conta");

        $account = Auth::user();

        return response(new AccountInfoDTO($account), Response::HTTP_OK);
    }

    public function logout(){
        $user = Auth::user()->token();
        $user->revoke();
        return response('', Response::HTTP_OK);
    }

    public function show($id){
        $this->authorize('show_account');
        Log::info("Exibindo as informações da conta com id = " . $id);

        $account = $this->accountRepository->findOneOrFail($id, true);
        $account->load('roles');
        $account->load('user');

        return response($account, Response::HTTP_OK);
    }




}
