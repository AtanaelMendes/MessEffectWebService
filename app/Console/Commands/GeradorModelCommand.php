<?php

namespace App\Console\Commands;

use App\User;
use App\DripEmailer;
use Illuminate\Console\Command;
use DB;

use Illuminate\Support\Str;


class GeradorModelCommand extends Command
{
    protected $signature = 'gerador:model {tabela}';
    protected $description = 'Gera o codigo fonte de um model';

    protected $namespace;
    protected $classe;
    protected $tabela;
    protected $conteudo;
    protected $arquivo;

    public function __construct()
    {
        parent::__construct();
    }

    public function inicializarParametros()
    {
        $this->tabela = $this->argument('tabela');

        // carrega arquivo de Indice
        $this->arquivoIndice = base_path('app/Models/indice.json');
        if (file_exists($this->arquivoIndice)) {
            $this->models = json_decode(file_get_contents($this->arquivoIndice), true);
        } else {
            $this->models = [];
        }

        $model = $this->descobrirModel($this->tabela);

        $this->namespace = $model['namespace'];
        $this->classe = $model['classe'];
        $this->arquivo = $model['arquivo'];
    }

    public function salvarArquivo()
    {
        // se diretornio nao existe, cria
        $caminho = dirname($this->arquivo);
        if (!is_dir($caminho)) {
            mkdir($caminho, '0775', true);
        }
        // grava o arquivo
        file_put_contents($this->arquivo, $this->conteudo);
    }

    public function montarCabecalho()
    {
        $this->conteudo = "<?php
/**
 * Created by php artisan gerador:model.
 * Date: " . date('d/M/Y H:i:s') . "
 */

namespace {$this->namespace};

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class {$this->classe} extends BaseModel
{
    use SoftDeletes, RevisionableTrait;

    protected \$table = '{$this->tabela}';
";
    }

    public function montarRodape()
    {
        $this->conteudo .= "
}";
    }

    public function buscarCamposTabela()
    {
        $sql = "
            SELECT
                column_name as nome,
             	  data_type as tipo
            FROM information_schema.columns
            WHERE table_schema = 'public'
            and table_name = '{$this->tabela}'
            order by column_name
        ";
        $this->colunas = DB::select($sql);

    }

    public function montarFillable()
    {
        $ignorar = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $campos = [];
        foreach ($this->colunas as $coluna) {
            if (in_array($coluna->nome, $ignorar)) {
                continue;
            }
            $campos[] = "'{$coluna->nome}'";
        }
        $campos = implode(',
        ', $campos);

        $this->conteudo .= "
    protected \$fillable = [
        $campos
    ];
";
    }

    public function montarDates()
    {
        $considerar = [
            'date',
            'timestamp without time zone',
        ];
        $campos = [];
        foreach ($this->colunas as $coluna) {
            if (!in_array($coluna->tipo, $considerar)) {
                continue;
            }
            $campos[] = "'{$coluna->nome}'";
        }
        $campos = implode(',
        ', $campos);

        $this->conteudo .= "
    protected \$dates = [
        $campos
    ];
";
    }

    public function montarCasts()
    {
        $tipos = [

            'bigint' => 'integer',
            'integer' => 'integer',
            'smallint' => 'integer',

            'boolean' => 'boolean',

            'double precision' => 'float',
            'numeric' => 'float',

        ];
        $campos = [];
        foreach ($this->colunas as $coluna) {
            if (!isset($tipos[$coluna->tipo])) {
                continue;
            }
            $campos[] = "'{$coluna->nome}' => '{$tipos[$coluna->tipo]}'";
        }
        $campos = implode(',
        ', $campos);

        $this->conteudo .= "
    protected \$casts = [
        $campos
    ];
";
    }

    public function testar()
    {
        $cmd = "{$this->namespace}\\{$this->classe}";

        // TESTE CONTAGEM REGISTROS
        $this->line('');
        $this->info('Teste contagem Registros na tabela...');
        $count = $cmd::count();
        $this->line("$count Registros na tabela");

        // TESTE INSTANCIAR
        $this->line('');
        $this->info('Teste Instanciar Classe...');
        if ($count > 0) {
            $obj = $cmd::first();
        } else {
            $obj = new $cmd();
        }
        print_r($obj->getAttributes());
        $this->line('OK');

        // TESTE RELACIONAMENTOS BELONGS TO
        $this->line('');
        $this->info('Teste de Relacionamentos belongsTo...');
        foreach ($this->models[$this->tabela]['belongsTo'] as $relacao) {
            $metodo = $relacao['metodo'];
            $this->info("{$this->classe}::$metodo()...");
            $count = $obj->$metodo()->count();
            $this->line("$count registros - OK");
        }

        // TESTES RELACIONAMENTOS HAS MANY
        $this->line('');
        $this->info('Teste de Relacionamentos hasMany...');
        foreach ($this->models[$this->tabela]['hasMany'] as $relacao) {
            $metodo = $relacao['metodo'];
            $this->info("{$this->classe}::$metodo()...");
            $count = $obj->$metodo()->count();
            $this->line("$count registros");
        }

    }

    public function montarChavesExtrangeiras()
    {
        $sql = '
          SELECT
              kcu.column_name as coluna,
              ccu.table_name AS tabela,
              ccu.column_name AS pk
          FROM
              information_schema.table_constraints AS tc
              JOIN information_schema.key_column_usage
                  AS kcu ON tc.constraint_name = kcu.constraint_name
              JOIN information_schema.constraint_column_usage
                  AS ccu ON ccu.constraint_name = tc.constraint_name
          WHERE constraint_type = \'FOREIGN KEY\'
          and tc.table_name = :tabela
          ORDER BY kcu.column_name
        ';
        $chaves = DB::select($sql, [
            'tabela' => $this->tabela
        ]);
        foreach ($chaves as $chave) {
            $this->montarBelongsTo($chave->coluna, $chave->tabela, $chave->pk);
        }

        $sql = '
          SELECT
              tc.table_name as tabela,
              kcu.column_name as coluna,
              ccu.column_name AS pk
          FROM
              information_schema.table_constraints AS tc
              JOIN information_schema.key_column_usage
                  AS kcu ON tc.constraint_name = kcu.constraint_name
              JOIN information_schema.constraint_column_usage
                  AS ccu ON ccu.constraint_name = tc.constraint_name
          WHERE constraint_type = \'FOREIGN KEY\'
          and ccu.table_name = :tabela
          ORDER BY tc.table_name, kcu.column_name
        ';
        $chaves = DB::select($sql, [
            'tabela' => $this->tabela
        ]);
        foreach ($chaves as $chave) {
            $this->montarHasMany($chave->coluna, $chave->tabela, $chave->pk);
        }
    }

    public function descobrirModel($tabela)
    {
        if (@$model = $this->models[$tabela]) {
            return $model;
        }
        $namespace = "App\Models";
        $palavras = explode('_', $tabela);
        $class = [];
        foreach ($palavras as $palavra) {
            $classe[] = Str::singular($palavra);
        }
        $classe = Str::studly(implode(' ', $classe));
        do {
            $this->line('');
            $this->line("A tabela {$tabela} não possui model listado no Indice. Por favor confirme o nome da classe e o namespace.");
            $classe = $this->ask("Qual a Classe para a tabela '{$tabela}'?", $classe);
            $namespace = $this->ask("Qual a Namespace para a tabela '{$tabela}'?", $namespace);
            $arquivo = $this->montarCaminhoArquivoModel($namespace, $classe);
        } while (!$this->confirm("Confirmar {$arquivo}?", true));
        $model = [
            'tabela' => $tabela,
            'namespace' => $namespace,
            'classe' => $classe,
            'arquivo' => $arquivo,
            'belongsTo' => [],
            'hasMany' => [],
        ];
        $this->models[$tabela] = $model;
        return $model;
    }

    public function montarCaminhoArquivoModel ($namespace, $classe = null)
    {
        // monta caminho diretorio
        $caminho = explode('\\', $namespace);
        $caminho[0] = 'app';
        $caminho = base_path(implode('/', $caminho));
        if (empty($classe)) {
            return $caminho;
        }
        return "{$caminho}/{$classe}.php";
    }

    public function descobrirMetodoBelongsTo ($coluna)
    {
        if (!@$metodo = $this->models[$this->tabela]['belongsTo'][$coluna]['metodo']) {
            $metodo = preg_replace('/(_id(?!.*_id))/', '', $coluna);
            $metodo = Str::camel($metodo);
        }
        $metodo = $this->ask("Metodo belongsTo: '{$coluna}'", $metodo);
        $this->models[$this->tabela]['belongsTo'][$coluna] = [
            'coluna' => $coluna,
            'metodo' => $metodo
        ];
        return $metodo;
    }

    public function montarBelongsTo($coluna, $tabela, $pk)
    {
        $model = $this->descobrirModel($tabela);
        $metodo = $this->descobrirMetodoBelongsTo($coluna);
        $this->conteudo .= "
    public function {$metodo}()
    {
        return \$this->belongsTo('{$model['namespace']}\\{$model['classe']}', '{$coluna}', '{$pk}')->withTrashed();
    }
";
    }

    public function descobrirMetodoHasMany($coluna, $tabela)
    {
        $chave = "{$tabela}.{$coluna}";
        if (!@$metodo = $this->models[$this->tabela]['hasMany'][$chave]['metodo']) {
            $metodo = Str::camel($tabela);
        }
        $metodo = $this->ask("Metodo HasMany: '{$chave}'", $metodo);
        $this->models[$this->tabela]['hasMany'][$chave] = [
            'tabela' => $tabela,
            'coluna' => $coluna,
            'metodo' => $metodo
        ];
        return $metodo;
    }

    public function montarHasMany($coluna, $tabela, $pk)
    {
        $model = $this->descobrirModel($tabela);
        $metodo = $this->descobrirMetodoHasMany($coluna, $tabela);
        $this->conteudo .= "
    public function {$metodo}()
    {
        return \$this->hasMany('{$model['namespace']}\\{$model['classe']}', '{$coluna}', '{$pk}');
    }
";
    }

    public function salvarIndiceModels()
    {
        $conteudo = json_encode($this->models, JSON_PRETTY_PRINT);
        file_put_contents($this->arquivoIndice, $conteudo);
    }

    public function handle()
    {
        $this->inicializarParametros();
        $this->buscarCamposTabela();
        $this->montarCabecalho();
        $this->montarFillable();
        $this->montarDates();
        $this->montarCasts();
        $this->montarChavesExtrangeiras();
        $this->montarRodape();
        $this->salvarArquivo();
        $this->salvarIndiceModels();
        $this->testar();
    }
}
