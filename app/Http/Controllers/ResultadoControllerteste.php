<?php

namespace App\Http\Controllers;

use App\Models\Aritmeticacoletiva;
use App\Models\Aritmeticaindividual;
use App\Models\Escrita;
use App\Models\Leituraindividual;
use App\Models\Psicogenese;
use App\Models\Aluno;
use App\Models\Formulario;
use App\Models\Escola;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AlunoController;
use ConsoleTVs\Charts\Charts;
use ConsoleTVs\Charts\Builder\Chart;

use Illuminate\Http\Request;

class ResultadoControllerteste extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function escolherEscola()
    {
        $alunoController = new AlunoController();
        $dados = $alunoController->obterDados();
        $escolas = Escola::all();
        // Verifica se há uma escola selecionada
        $escolaId = request()->input('escola_id');
        if ($escolaId) {
            // Encontra a escola pelo ID e atribui o nome à variável $nomedaescola
            $escola = Escola::findOrFail($escolaId);
            $nomedaescola = $escola->nome;
        } else {
            // Define $nomedaescola como null se nenhuma escola foi selecionada
            $nomedaescola = null;
        }
        // Retorna a view com os dados
        return view('testes.percentual', [
            'dados' => $dados,
            'escolas' => $escolas,
            'nomedaescola' => $nomedaescola
        ]);
    }

    private function ultimaDataInterpretacao($escolaId)
    {
        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoAritmeticacoletiva = Aritmeticacoletiva::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoAritmeticaindividual = Aritmeticaindividual::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoEscrita = Escrita::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoLeituraindividual = Leituraindividual::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        $ultimaDataInterpretacaoPsicogenese = Psicogenese::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoFormulario = Formulario::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        return compact( 'ultimaDataInterpretacaoAritmeticacoletiva',
                        'ultimaDataInterpretacaoAritmeticaindividual',
                        'ultimaDataInterpretacaoEscrita',
                        'ultimaDataInterpretacaoLeituraindividual',
                        'ultimaDataInterpretacaoPsicogenese',
                        'ultimaDataInterpretacaoFormulario'
                    );
    }

    public function percentual(Request $request)
    {
        $escolaId = $request->input('escola_id');
        $escola = Escola::findOrFail($escolaId); // Encontra a escola pelo ID
        $nomedaescola = $escola->nome;
        $escolas = Escola::all();
        $ultimaDataInterpretacao = $this->ultimaDataInterpretacao($escolaId);
        $ultimaDataInterpretacaoAritmeticacoletiva = $ultimaDataInterpretacao['ultimaDataInterpretacaoAritmeticacoletiva'];
        $ultimaDataInterpretacaoAritmeticaindividual = $ultimaDataInterpretacao['ultimaDataInterpretacaoAritmeticaindividual'];
        $ultimaDataInterpretacaoEscrita = $ultimaDataInterpretacao['ultimaDataInterpretacaoEscrita'];
        $ultimaDataInterpretacaoLeituraindividual = $ultimaDataInterpretacao['ultimaDataInterpretacaoLeituraindividual'];
        $ultimaDataInterpretacaoPsicogenese = $ultimaDataInterpretacao['ultimaDataInterpretacaoPsicogenese'];
        $ultimaDataInterpretacaoFormulario = $ultimaDataInterpretacao['ultimaDataInterpretacaoFormulario'];
        dd($ultimaDataInterpretacao);
        $contadorInterpretacoes = [];

    }



    /*
    public function buscarescola()
    {
        // Obtenha todas as escolas para exibir no formulário
        $escolas = Escola::all();

        // Variáveis iniciais
        $escolaId = null;
        $escola = null;
        $nomedaescola = null;
        $alunosComResultados = null;

        // Verifique se uma escola foi selecionada no formulário
        if (request()->has('escola_id')) {
            // Valide o ID da escola recebido
            request()->validate([
                'escola_id' => 'required|exists:escolas,id',
            ]);

            // Recupere o ID da escola selecionada
            $escolaId = request('escola_id');

            // Consulte a escola selecionada
            $escola = Escola::find($escolaId);
            $nomedaescola = $escola->nome;

            // Consulte os alunos com resultados
            $alunosComResultados = Aluno::where('escola_id', $escolaId)
                ->with(['escritas' => function ($query) {
                    $query->select('id', 'escrita', 'percentil', 'interpretacao', 'aluno_id');
                }])
                ->get();
        }

        return view('testes.buscar_resultados', [
            'escolaId' => $escolaId,
            'escola' => $escola,
            'nomedaescola' => $nomedaescola,
            'escolas' => $escolas,
            'alunosComResultados' => $alunosComResultados,
        ]);
    }
    public function escrita(Request $request)
    {
        // Inicialize um array para armazenar os tipos de teste selecionados
        $tiposTeste = [];

        // Verifique quais checkboxes foram selecionados e adicione os tipos de teste ao array
        if ($request->has('escrita_coletiva_4_5')) {
            $tiposTeste[] = 'escritas';

        }
        if ($request->has('aritmetica_coletiva_4_5')) {
            $tiposTeste[] = 'aritmetica_coletiva_4_5';
        }
        if ($request->has('leitura_individual_4_5')) {
            $tiposTeste[] = 'leitura_individual_4_5';
        }
        // Se nenhum teste for selecionado, retorne um erro
        if (empty($tiposTeste)) {
            return redirect()->back()->with('error', 'É necessário selecionar pelo menos um teste.');
        }
        // Obtenha o ID da escola selecionada
        $escolaId = $request->input('escola_id');

        // Inicialize a variável $alunosComResultados como uma coleção vazia
        $alunosComResultados = collect();

        // Verifique se o ID da escola está definido
        if ($escolaId) {
            // Inicialize a consulta para buscar os alunos de acordo com o tipo de teste
            $consultaAlunos = Aluno::where('escola_id', $escolaId);

            // Adicione a cláusula de verificação do tipo de teste
            foreach ($tiposTeste as $tipoTeste) {
                $consultaAlunos->whereExists(function ($query) use ($tipoTeste) {
                    $query->select(DB::raw(1))
                        ->from($tipoTeste)
                        ->whereRaw('alunos.id = ' . $tipoTeste . '.aluno_id');
                    // Adicione outras condições conforme necessário para filtrar por tipo de teste
                });
            }

            // Execute a consulta e obtenha os alunos com resultados
            $alunosComResultados = $consultaAlunos->with(['escritas' => function ($query) {
                $query->select('id', 'escrita', 'percentil', 'interpretacao', 'aluno_id');
            }])->get();
        }

        // Consulte os alunos do 4º ano da escola selecionada
        $alunosQuartoAno = Aluno::where('escola_id', $escolaId)
            ->where('etapa_aluno', '4º ANO')
            ->get();

        // Consulte os alunos do 5º ano da escola selecionada
        $alunosQuintoAno = Aluno::where('escola_id', $escolaId)
            ->where('etapa_aluno', '5º ANO')
            ->get();
        //dd($alunosComResultados);
        // Retorne os alunos com resultados de acordo com os tipos de teste selecionados
        return view('testes.buscar_resultados', [
            'alunosComResultados' => $alunosComResultados,
            'alunosQuartoAno' => $alunosQuartoAno,
            'alunosQuintoAno' => $alunosQuintoAno,
            'escolas' => Escola::all(),
            'escolaId' => $escolaId,

        ]);
    }
    */
}
