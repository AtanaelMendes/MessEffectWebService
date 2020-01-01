<?php
namespace App\Repositories;

use App\Models\Pessoa;
use App\Resources\SynchronizableResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class PessoaRepository extends BaseRepository implements SynchronizableResource
{
    protected $model;

    public function __construct(Pessoa $pessoa)
    {
        $this->model = $pessoa;
    }

    public function listAllWithFilter($request)
    {
        $type = $request->type;
        $search = $request->search;

        $qry = Pessoa::select('*')->where(function($query) use ($search) {
            $query->orWhere('nome', 'ilike', '%' . $search . '%')
                ->orWhere('cpf', 'ilike', '%' . $search . '%')
                ->orWhere('cnpj', 'ilike', '%' . $search . '%');
        });

        if($type == 'all'){
            $qry->withTrashed();
        }
        if($type == 'trashed'){
            $qry->onlyTrashed();
        }
        $qry->orderBy('nome', 'asc');

//        dd($qry->toSql());

        return $qry->get();
    }

    public function search($words)
    {
        $query = "with consulta as (select p.*, ge.nome as grupo_nome,concat(p.nome, ' ', p.cpf, ' ', p.cnpj, ' ', 
        p.inscricao_estadual, ' ', p.inscricao_municipal, ' ', p.razao_social, ' ', ge.nome) as campao from pessoas p 
        inner join grupos_economicos ge on p.grupo_economico_id = ge.id) select * from consulta WHERE ";

        $filtro = [];
        $i_palavra = 0;
        foreach ($words as $palavra) {
            if($i_palavra > 0){
                $query .= "AND ";
            }
            $query .= "campao ilike :palavra{$i_palavra} ";
            $filtro["palavra{$i_palavra}"] = '%'.$palavra.'%';
            $i_palavra++;
        }

        $results = collect(DB::select($query, $filtro));

        $results = $results->groupBy('grupo_nome')->toArray();

        $results2 = [];

        foreach ($results as $index => $result){
            $results2[] = [
                'nome' => $index,
                'pessoas' => $result
            ];
        }

        return $results2;
    }

    public function getResourceAfter(Carbon $after, int $pessoaId)
    {
        return Pessoa::Select('*')->where('id', $pessoaId, 'updated_at', '>', $after)->get();
    }

    public function hasResourceAfter(Carbon $after, int $pessoaId)
    {
        return Pessoa::Select('*')->where('id', $pessoaId, 'updated_at', '>', $after)->get()->isNotEmpty();
    }
}
