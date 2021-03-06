<?php

namespace App\Http\Controllers;

use App\DTOs\AccountInfoDTO;
use App\Http\Requests\AccountChangePasswordRequest;
use App\Http\Requests\AccountInfoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Http\Requests\AccountRequest;
use App\Repository\RoleRepository;
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
        unset($createdAccount->user_id);
        if(count($roleIds) > 0) {
            $createdAccount->load('roles');
        }
        return response($createdAccount, Response::HTTP_CREATED);
    }

    public function info(){
//        $this->authorize('show_account_info');
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
//        $this->authorize('show_account');
        Log::info("Exibindo as informações da conta com id = " . $id);

        $account = $this->accountRepository->findOneOrFail($id, true);
        $account->load('roles');
        $account->load('user');

        return response($account, Response::HTTP_OK);
    }

    public function updateInfo(AccountInfoRequest $request){
//        $this->authorize('update_account');
        Log::info("Atualizando as informações da conta");

        $existedAccount = Auth::user();

        $account = $request->only('name', 'email');

        $this->accountRepository->update($account, $existedAccount->id, true);

        $updatedAccount = $this->accountRepository->find($existedAccount->id, true);

        return response($updatedAccount, Response::HTTP_OK);
    }

    public function changePassword(AccountChangePasswordRequest $request){
//        $this->authorize('update_account');
        Log::info("Alterando a senha da conta");

        $existedAccount = Auth::user();

        $account = ['password' => Hash::make($request->input('new_password'))];

        $this->accountRepository->update($account, $existedAccount->id, true);

        $updatedAccount = $this->accountRepository->find($existedAccount->id, true);

        return response($updatedAccount, Response::HTTP_OK);
    }

    public function update(AccountRequest $request, $id){
//        $this->authorize('update_account');
        Log::info("Atualizando as informações da conta com id = " . $id);

        $existedAccount = $this->accountRepository->findOneOrFail($id, true);

        $roleIds = $this->getRolesIds($request);

        $account = $request->only('nome', 'email', 'password', 'produtor_id');

        if(array_key_exists('password', $account)){
            $account['password'] = Hash::make($request->input('password'));
        }

        $this->accountRepository->update($account, $existedAccount->id, true);
        $updatedAccount = $this->accountRepository->find($existedAccount->id, true);

        if(count($roleIds) > 0) {
            $updatedAccount->roles()->sync($roleIds);
            $updatedAccount->load('roles');
        }

        return response($updatedAccount, Response::HTTP_OK);
    }

    public function restore($id){
//        $this->authorize('restore_account');
        Log::info("Restaurando a conta com id = " . $id);

        $this->accountRepository->restore($id);

        return response(null, Response::HTTP_OK);
    }

    public function destroy($id){
//        $this->authorize('delete_account');
        Log::info("Desativando a conta com id = " . $id);

        $this->accountRepository->forceDelete($id, true);

        return response(null, Response::HTTP_OK);
    }

    public function archive($id){
//        $this->authorize('archive_account');
        Log::info("Arquivando a conta com id = " . $id);

        $this->accountRepository->delete($id, true);

        return response(null, Response::HTTP_OK);
    }

    private function getRolesIds(AccountRequest $request){
        $roleIds = [];
        if($request->input('roles')) {
            $roleIds = array_map('intval', explode(',', $request->input('roles')));

            foreach($roleIds as $roleId){
                $this->roleRepository->findOneOrFail($roleId);
            }
        }

        return $roleIds;
    }

    public function uploadImage(ImageRequest $request){
//        $this->authorize('upload_account_image');
        Log::info("Alterando a imagem da conta");

        $existedAccount = Auth::user();

        $createdImage = $this->imageService->save($request, $existedAccount->user_id);

        $existedAccount->image()->associate($createdImage);

        $existedAccount->save();

        return response(null, Response::HTTP_OK);
    }

}
