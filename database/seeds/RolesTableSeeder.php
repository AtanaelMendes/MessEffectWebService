<?php

use App\Models\Acao;
use App\Models\Permissao;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class RolesTableSeeder extends Seeder
{
    private $permissions;

    public function run(){
        $this->permissions = new Collection();

        $this->createRole('ROLE_ADMIN', 'Administrador', [
            $this->getAccountPermissions(),
            $this->getRolePermissions(),
            $this->getPermissionPermissions(),
            $this->getPessoaPermissions(),
            $this->getContatosPermissions(),
            $this->getLocalizacoesPermissions(),
            $this->getProdutosPermissions(),
            $this->getMarcasPermissions(),
            $this->getImagePermissions(),
            $this->getResourcePermissions(),
        ]);

    }

    private function insertOrUpdate($codigo, $nome, $descricao)
    {
        $permission = Permissao::firstOrNew(array('codigo' => $codigo));
        $permission->nome = $nome;
        $permission->descricao = $descricao;
        $permission->save();

        return $permission->id;
    }

    private function savePermissions($permissions){
        $ids = [];
        foreach($permissions as $permission){
            foreach ($permission as $key => $value){
                $ids[] = $this->insertOrUpdate($key, $value, null);
            }
        }

        return $ids;
    }

    private function createRole($codigo, $nome, $permissions){
        $role = Acao::firstOrNew(array('codigo' => $codigo));
        $role->nome = $nome;
        $role->save();

        $permissionIds = $this->savePermissions($permissions);

        $role->permissions()->sync($permissionIds);
    }

    private function getAccountPermissions(){
        return [
            'create_account' => 'Criar conta',
            'update_account' => 'Atualizar conta',
            'list_accounts' => 'Listar contas',
            'show_account_info' => 'Exibir informações da conta',
            'show_account' => 'Exibir conta',
            'restore_account' => 'Restaurar conta',
            'delete_account' => 'Deletar conta',
            'archive_account' => 'Arquivar conta',
            'upload_account_image' => 'Atualizar imagem da conta',
        ];
    }

    private function getRolePermissions(){
        return [
            'list_roles' => 'Listar roles',
            'show_role' => 'Exibir role',

        ];
    }

    private function getPermissionPermissions(){
        return [
            'list_permissions' => 'Listar permissões',
            'show_permission' => 'Exibir permissão',

        ];
    }

    public function getPessoaPermissions()
    {
        return [
            'create_pessoa' => 'Criar pessoa',
            'update_pessoa' => 'Atualizar pessoa',
            'list_pessoas' => 'Listar pessoas',
            'show_pessoa' => 'Exibir pessoa',
            'restore_pessoa' => 'Restaurar pessoa',
            'delete_pessoa' => 'Deletar pessoa',
            'archive_pessoa' => 'Arquivar pessoa',
        ];
    }

    public function getContatosPermissions()
    {
        return [
            'create_contato' => 'Criar contato',
            'update_contato' => 'Atualizar contato',
            'list_contatos' => 'Listar contatos',
            'show_contato' => 'Exibir contato',
            'restore_contato' => 'Restaurar contato',
            'delete_contato' => 'Deletar contato',
            'archive_contato' => 'Arquivar contato',
        ];
    }

    public function getLocalizacoesPermissions()
    {
        return [
            'create_localizacao' => 'Criar localização',
            'update_localizacao' => 'Atualizar localização',
            'list_localizacoes' => 'Listar localizações',
            'show_localizacao' => 'Exibir localização',
            'restore_localizacao' => 'Restaurar localização',
            'delete_localizacao' => 'Deletar localização',
            'archive_localizacao' => 'Arquivar localização',
        ];
    }

    public function getProdutosPermissions(){
        return [
            'create_produto' => 'Criar produto',
            //'update_safra' => 'Atualizar safra',
            'list_produtos' => 'Listar produtos',
            /*'show_safra' => 'Exibir safra',
            'restore_safra' => 'Restaurar safra',
            'delete_safra' => 'Deletar safra',*/
        ];
    }

    public function getMarcasPermissions()
    {
        return [
            'list_marcas' => 'Listar marcas',
            'create_marca' => 'Criar marca',
            'update_marca' => 'Atualizar marca',
            'delete_marca' => 'Deletar marca',
            'archive_marca' => 'Arquivar marca',
            'restore_marca' => 'Restaurar marca',
            'upload_marca_image' => 'Atualizar imagem da marca',
        ];
    }

    public function getImagePermissions()
    {
        return [
            'list_images' => 'Listar Imagens',
        ];
    }

    public function getResourcePermissions()
    {
        return [
            'get_resources' => 'Baixar recursos para sincronização',
        ];
    }

}
