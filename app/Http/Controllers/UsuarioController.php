<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 17/09/18
 * Time: 08:39
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use App\Repository\UsuarioRepository;
use App\Repository\PessoaRepository;
use App\Repository\AcaoRepository;
use App\DTOs\UsuarioInfoDTO;
use App\Services\ImageService;
//use App\Http\Requests\UsuarioRequest;
//use App\Http\Requests\UsuarioChangePasswordRequest;
//use App\Http\Requests\UsuarioInfoRequest;
//use App\Http\Requests\ImageRequest;


class UsuarioController extends Controller {

    private $usuarioRepository;
    private $acaoRepository;
    private $pessoaRepository;
    private $imageService;

    public function __construct(UsuarioRepository $usuarioRepository,
                                AcaoRepository $acaoRepository,
                                PessoaRepository $pessoaRepository,
                                ImageService $imageService)
    {
        $this->usuarioRepository = $usuarioRepository;
        $this->acaoRepository = $acaoRepository;
        $this->pessoaRepository = $pessoaRepository;
        $this->imageService = $imageService;
    }

    public function index(Request $request){
        $this->authorize('list_usuarios');

        $type = $request->query('type');
        $email = $request->query('email');

        $items = $this->usuarioRepository->searchByEmailAndType($email, $type);

        //TODO: Colocar essa parte em uma função
        /*$currentAccount = Auth::user();
        foreach ($items as $key => $item) {
            if ($item->email == $currentAccount->email) {
                $items->pull($key);
            }
        }*/

        return response($items->values(), Response::HTTP_OK);
    }

    public function store(Request $request){
        $this->authorize('create_usuario');
        Log::info("Criando conta para: " . $request->input('email'));

        $acoesId = $this->getRolesIds($request);

        $usuario = $request->all();
        $usuario['password'] = Hash::make($usuario['password']);

        $createdUsuario = DB::transaction(function () use ($usuario, $acoesId) {
            $createdUsuario = $this->UsuarioRepository->create($usuario);

            if (count($acoesId) > 0) {
                $createdUsuario->roles()->sync($acoesId);
            }

            return $createdUsuario;
        });

        $createdUsuario = $this->UsuarioRepository->findOneOrFail($createdUsuario->id);
        $createdUsuario->load('pessoa');
        unset($createdUsuario->pessoa_id);
        if(count($acoesId) > 0) {
            $createdUsuario->load('acoes');
        }
        return response($createdUsuario, Response::HTTP_CREATED);
    }

    public function info(){
        $this->authorize('show_usuario_info');
        Log::info("Pegando as informações da própria conta");

        $usuario = Auth::user();

        return response(new UsuarioInfoDTO($usuario), Response::HTTP_OK);
    }

    public function logout(){
        $usuario = Auth::user()->token();
        $usuario->revoke();
        return response('', Response::HTTP_OK);
    }

    public function show($id){
        $this->authorize('show_usuario');
        Log::info("Exibindo as informações da conta com id = " . $id);

        $usuario = $this->UsuarioRepository->findOneOrFail($id, true);
        $usuario->load('acoes');
        $usuario->load('pessoa');

        return response($usuario, Response::HTTP_OK);
    }

    public function updateInfo(Request $request)
    {
        $this->authorize('update_account');
        Log::info("Atualizando as informações da conta");

        $existedAccount = Auth::user();

        $usuario = $request->only('nome', 'email');

        $this->UsuarioRepository->update($usuario, $existedAccount->id, true);

        $updatedAccount = $this->UsuarioRepository->find($existedAccount->id, true);

        return response($updatedAccount, Response::HTTP_OK);
    }

    public function changePassword(Request $request)
    {
        $this->authorize('update_usuario');
        Log::info("Alterando a senha da conta");

        $existedAccount = Auth::user();

        $usuario = ['password' => Hash::make($request->input('new_password'))];

        $this->UsuarioRepository->update($usuario, $existedAccount->id, true);

        $updatedAccount = $this->UsuarioRepository->find($existedAccount->id, true);

        return response($updatedAccount, Response::HTTP_OK);
    }

    public function update(Request $request, $id){
        $this->authorize('update_usuario');
        Log::info("Atualizando as informações da conta com id = " . $id);

        $existedAccount = $this->UsuarioRepository->findOneOrFail($id, true);

        $acoesId = $this->getAcoesId($request);

        $usuario = $request->only('nome', 'email', 'password', 'pessoa_id');

        if(array_key_exists('password', $usuario)){
            $usuario['password'] = Hash::make($request->input('password'));
        }

        $this->UsuarioRepository->update($usuario, $existedAccount->id, true);
        $updatedAccount = $this->UsuarioRepository->find($existedAccount->id, true);

        if(count($acoesId) > 0) {
            $updatedAccount->roles()->sync($acoesId);
            $updatedAccount->load('roles');
        }

        return response($updatedAccount, Response::HTTP_OK);
    }

    public function restore($id){
        $this->authorize('restore_usuario');
        Log::info("Restaurando a conta com id = " . $id);

        $this->UsuarioRepository->restore($id);

        return response(null, Response::HTTP_OK);
    }

    public function destroy($id){
        $this->authorize('delete_usuario');
        Log::info("Deletando a conta com id = " . $id);

        $this->UsuarioRepository->forceDelete($id, true);

        return response(null, Response::HTTP_OK);
    }

    public function archive($id){
        $this->authorize('archive_usuario');
        Log::info("Arquivando a conta com id = " . $id);

        $this->UsuarioRepository->delete($id, true);

        return response(null, Response::HTTP_OK);
    }

    private function getAcoesId(Request $request){
        $acoesId = [];
        if($request->input('roles')) {
            $acoesId = array_map('intval', explode(',', $request->input('roles')));

            foreach($acoesId as $acaoId){
                $this->acaoRepository->findOneOrFail($acaoId);
            }
        }

        return $acoesId;
    }

    public function uploadImage(Request $request)
    {
        $this->authorize('upload_usuario_image');
        Log::info("Alterando a imagem da conta");

        $existedAccount = Auth::user();

        $createdImage = $this->imageService->save($request, $existedAccount->pessoa_id);

        $existedAccount->image()->associate($createdImage);

        $existedAccount->save();

        return response(null, Response::HTTP_OK);
    }
}
