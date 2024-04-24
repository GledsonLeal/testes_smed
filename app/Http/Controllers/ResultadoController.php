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
use Khill\Lavacharts\Lavacharts;

use Illuminate\Http\Request;

class ResultadoController extends Controller
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


    public function percentual(Request $request)
    {
        $escolaId = $request->input('escola_id');
        $escola = Escola::findOrFail($escolaId); // Encontra a escola pelo ID
        $nomedaescola = $escola->nome;
        $escolas = Escola::all();
        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacao = Aritmeticacoletiva::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();

        //dd($ultimaDataInterpretacao);
        // Inicializa um array para armazenar o contador de interpretações
        $contadorInterpretacoes = [];


        // Inicializa todos os valores de interpretação com zero
        $todasInterpretacoes = [
            'Déficit muito grave',
            'Déficit grave',
            'Déficit de leve a moderado',
            'Alerta para déficit (abaixo do esperado)',
            'Médio-inferior (um pouco abaixo do esperado, mas ainda dentro da média; ou levemente abaixo do esperado)',
            'Médio-superior (um pouco acima do esperado, mas ainda dentro da média; ou levemente acima do esperado)',
            'Acima do esperado',
            'Muito acima do esperado',
            'Desempenho desenvolvido em nível muito superior',
        ];

        // Inicializa o contador de todas as interpretações com zero
        foreach ($todasInterpretacoes as $interpretacao) {
            $contadorInterpretacoes[$interpretacao] = 0;
        }

        // Busca todas as respostas da última data
        if ($ultimaDataInterpretacao !== null) {
            // Se $ultimaDataInterpretacao não for nulo, execute o código relacionado
            // ...
            // Aqui você pode colocar o código relacionado à manipulação de $ultimaDataInterpretacao

            // Busca todas as respostas da última data
            $respostasUltimaData = Aritmeticacoletiva::whereDate('created_at', $ultimaDataInterpretacao->created_at)
                ->whereHas('aluno', function ($query) use ($escolaId) {
                    $query->where('escola_id', $escolaId);
                })->get();



            // Conta as respostas de cada interpretação
            foreach ($respostasUltimaData as $resposta) {
                $interpretacaoString = $resposta->interpretacao;
                $contadorInterpretacoes[$interpretacaoString]++;
            }

            // Calcula a porcentagem de cada interpretação
            $percentuaisAritmeticacoletiva = [];
            foreach ($contadorInterpretacoes as $interpretacao => $quantidade) {
                // Calcula o percentual da interpretação em relação ao total de respostas da última data
                $percentual = $respostasUltimaData->count() > 0 ? number_format(($quantidade / $respostasUltimaData->count()) * 100, 2) : 0;
                $percentuaisAritmeticacoletiva[$interpretacao] = $percentual;
            }
        }
        //dd('Resultados do Teste de Aritmética Coletiva 4º e 5º Anos:', $percentuaisAritmeticacoletiva);
        //################################################################



        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoAritmeticaindividual = Aritmeticaindividual::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        //dd($ultimaDataInterpretacao);
        // Inicializa um array para armazenar o contador de interpretações
        $contadorInterpretacoes = [];
        // Inicializa todos os valores de interpretação com zero
        $todasInterpretacoes = [
            'Déficit muito grave',
            'Déficit grave',
            'Déficit de leve a moderado',
            'Alerta para déficit (abaixo do esperado)',
            'Médio-inferior (um pouco abaixo do esperado, mas ainda dentro da média; ou levemente abaixo do esperado)',
            'Médio-superior (um pouco acima do esperado, mas ainda dentro da média; ou levemente acima do esperado)',
            'Acima do esperado',
            'Muito acima do esperado',
            'Desempenho desenvolvido em nível muito superior',
        ];

        // Inicializa o contador de todas as interpretações com zero
        foreach ($todasInterpretacoes as $interpretacao) {
            $contadorInterpretacoes[$interpretacao] = 0;
        }
        if ($ultimaDataInterpretacaoAritmeticaindividual !== null) {
            // Busca todas as respostas da última data
            $respostasUltimaData = Aritmeticaindividual::whereDate('created_at', $ultimaDataInterpretacaoAritmeticaindividual->created_at)
                ->whereHas('aluno', function ($query) use ($escolaId) {
                    $query->where('escola_id', $escolaId);
                })->get();

            // Conta as respostas de cada interpretação
            foreach ($respostasUltimaData as $resposta) {
                $interpretacaoString = $resposta->interpretacao;
                $contadorInterpretacoes[$interpretacaoString]++;
            }

            // Calcula a porcentagem de cada interpretação
            $percentuaisAritmeticaindividual = [];
            foreach ($contadorInterpretacoes as $interpretacao => $quantidade) {
                // Calcula o percentual da interpretação em relação ao total de respostas da última data
                $percentual = $respostasUltimaData->count() > 0 ? number_format(($quantidade / $respostasUltimaData->count()) * 100, 2) : 0;
                $percentuaisAritmeticaindividual[$interpretacao] = $percentual;
            }
        }
        //####################################################################


        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoEscrita = Escrita::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        //dd($ultimaDataInterpretacao);
        // Inicializa um array para armazenar o contador de interpretações
        $contadorInterpretacoes = [];
        // Inicializa todos os valores de interpretação com zero
        $todasInterpretacoes = [
            'Déficit muito grave',
            'Déficit grave',
            'Déficit de leve a moderado',
            'Alerta para déficit (abaixo do esperado)',
            'Médio-inferior (um pouco abaixo do esperado, mas ainda dentro da média; ou levemente abaixo do esperado)',
            'Médio-superior (um pouco acima do esperado, mas ainda dentro da média; ou levemente acima do esperado)',
            'Acima do esperado',
            'Muito acima do esperado',
            'Desempenho desenvolvido em nível muito superior',
        ];

        // Inicializa o contador de todas as interpretações com zero
        foreach ($todasInterpretacoes as $interpretacao) {
            $contadorInterpretacoes[$interpretacao] = 0;
        }
        if ($ultimaDataInterpretacaoEscrita !== null) {
            // Busca todas as respostas da última data
            $respostasUltimaData = Escrita::whereDate('created_at', $ultimaDataInterpretacaoEscrita->created_at)
                ->whereHas('aluno', function ($query) use ($escolaId) {
                    $query->where('escola_id', $escolaId);
                })->get();

            // Conta as respostas de cada interpretação
            foreach ($respostasUltimaData as $resposta) {
                $interpretacaoString = $resposta->interpretacao;
                $contadorInterpretacoes[$interpretacaoString]++;
            }

            // Calcula a porcentagem de cada interpretação
            $percentuaisEscrita = [];
            foreach ($contadorInterpretacoes as $interpretacao => $quantidade) {
                // Calcula o percentual da interpretação em relação ao total de respostas da última data
                $percentual = $respostasUltimaData->count() > 0 ? number_format(($quantidade / $respostasUltimaData->count()) * 100, 2) : 0;
                $percentuaisEscrita[$interpretacao] = $percentual;
            }

            /*
            $chartEscrita = Charts::create('pie', 'highcharts')
            ->title('Percentuais de Interpretações - Escrita')
            ->labels(array_keys($percentuaisEscrita))
            ->values(array_values($percentuaisEscrita));
            */
        }
        //dd($interpretacao, $percentual);
        //####################################################################


        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoLeituraindividual = Leituraindividual::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        //dd($ultimaDataInterpretacao);
        // Inicializa um array para armazenar o contador de interpretações
        $contadorInterpretacoes = [];

        // Inicializa todos os valores de interpretação com zero
        $todasInterpretacoes = [
            'Déficit muito grave',
            'Déficit grave',
            'Déficit de leve a moderado',
            'Alerta para déficit (abaixo do esperado)',
            'Médio-inferior (um pouco abaixo do esperado, mas ainda dentro da média; ou levemente abaixo do esperado)',
            'Médio-superior (um pouco acima do esperado, mas ainda dentro da média; ou levemente acima do esperado)',
            'Acima do esperado',
            'Muito acima do esperado',
            'Desempenho desenvolvido em nível muito superior',
        ];

        // Inicializa o contador de todas as interpretações com zero
        foreach ($todasInterpretacoes as $interpretacao) {
            $contadorInterpretacoes[$interpretacao] = 0;
        }

        // Busca todas as respostas da última data
        if ($ultimaDataInterpretacaoLeituraindividual !== null) {
            $respostasUltimaData = Leituraindividual::whereDate('created_at', $ultimaDataInterpretacaoLeituraindividual->created_at)
                ->whereHas('aluno', function ($query) use ($escolaId) {
                    $query->where('escola_id', $escolaId);
                })->get();

            // Conta as respostas de cada interpretação
            foreach ($respostasUltimaData as $resposta) {
                $interpretacaoString = $resposta->interpretacao;
                $contadorInterpretacoes[$interpretacaoString]++;
            }

            // Calcula a porcentagem de cada interpretação
            $percentuaisLeituraindividual = [];
            foreach ($contadorInterpretacoes as $interpretacao => $quantidade) {
                // Calcula o percentual da interpretação em relação ao total de respostas da última data
                $percentual = $respostasUltimaData->count() > 0 ? number_format(($quantidade / $respostasUltimaData->count()) * 100, 2) : 0;
                $percentuaisLeituraindividual[$interpretacao] = $percentual;
            }
        }
        ####################################################################################################
        ####################################################################################################
        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoPsicogenese = Psicogenese::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        // Inicializa um array para armazenar o contador de interpretações
        $contadorInterpretacoesPsicogenese = [];

        // Inicializa todos os valores de interpretação com zero
        $todasInterpretacoesPsicogenese = [
            'Pré-silábico',
            'Silábico',
            'Silábico-alfabético',
            'Alfabético',
        ];

        // Inicializa o contador de todas as interpretações com zero
        foreach ($todasInterpretacoesPsicogenese as $interpretacao) {
            $contadorInterpretacoesPsicogenese[$interpretacao] = 0;
        }

        // Busca todas as respostas da última data
        if ($ultimaDataInterpretacaoPsicogenese !== null) {
            $respostasUltimaData = Psicogenese::whereDate('created_at', $ultimaDataInterpretacaoPsicogenese->created_at)
                ->whereHas('aluno', function ($query) use ($escolaId) {
                    $query->where('escola_id', $escolaId);
                })->get();

            // Conta as respostas de cada interpretação
            foreach ($respostasUltimaData as $resposta) {
                $interpretacaoString = $resposta->psicogenese; // Aqui usamos a coluna correta
                $contadorInterpretacoesPsicogenese[$interpretacaoString]++;
            }

            // Calcula a porcentagem de cada interpretação
            $percentuaisPsicogenese = [];
            foreach ($contadorInterpretacoesPsicogenese as $interpretacao => $quantidade) {
                // Calcula o percentual da interpretação em relação ao total de respostas da última data
                $percentual = $respostasUltimaData->count() > 0 ? number_format(($quantidade / $respostasUltimaData->count()) * 100, 2) : 0;
                $percentuaisPsicogenese[$interpretacao] = $percentual;
            }
        }
        ####################################################################################
        ####################################################################################

        // Busca as interpretações mais recentes da escola escolhida
        $ultimaDataInterpretacaoFormulario = Formulario::whereHas('aluno', function ($query) use ($escolaId) {
            $query->where('escola_id', $escolaId);
        })->latest('created_at')->first();
        //dd($ultimaDataInterpretacao);
        // Inicializa um array para armazenar o contador de interpretações
        $contadorInterpretacoesPCFO = [];

        // Inicializa todos os valores de interpretação com zero
        $todasInterpretacoesPCFO = [
            'muito baixa',
            'baixa',
            'média',
            'alta',
            'muito alta'
        ];

        // Inicializa o contador de todas as interpretações com zero
        foreach ($todasInterpretacoesPCFO as $interpretacao) {
            $contadorInterpretacoesPCFO[$interpretacao] = 0;
        }
        //dd($interpretacao);
        // Busca todas as respostas da última data
        // Verifica se $ultimaDataInterpretacao é nulo antes de usá-lo
        if ($ultimaDataInterpretacaoFormulario !== null) {
            // Busca todas as respostas da última data
            $respostasUltimaData = Formulario::whereDate('created_at', $ultimaDataInterpretacaoFormulario->created_at)
                ->whereHas('aluno', function ($query) use ($escolaId) {
                    $query->where('escola_id', $escolaId);
                })->get();

            //dd($respostasUltimaData);
            // Conta as respostas de cada interpretação
            //$interpretacaoString = $resposta->psicogenese; // Aqui usamos a coluna correta
            //$contadorInterpretacoesPsicogenese[$interpretacaoString]++;
            foreach ($respostasUltimaData as $resposta) {
                $interpretacaoString = $resposta->resultado_teste;
                $contadorInterpretacoesPCFO[$interpretacaoString]++;
            }

            // Calcula a porcentagem de cada interpretação
            $percentuaisPCFO = [];
            foreach ($contadorInterpretacoesPCFO as $interpretacao => $quantidade) {
                // Calcula o percentual da interpretação em relação ao total de respostas da última data
                $percentual = count($respostasUltimaData) > 0 ? number_format(($quantidade / count($respostasUltimaData)) * 100, 2) : 0;
                $percentuaisPCFO[$interpretacao] = $percentual;
            }
            //dd($percentuaisPCFO);
        }
        /*
        // Cria um novo objeto Lavacharts
        $lava = new Lavacharts;

        // Define os dados para o gráfico
        $data = $lava->DataTable();

        // Adiciona as colunas do gráfico
        $data->addStringColumn('Interpretação')
            ->addNumberColumn('Percentual');

        // Adiciona os dados do gráfico
        foreach ($percentuaisAritmeticacoletiva as $interpretacao => $percentual) {
            $data->addRow([$interpretacao, $percentual]);
        }

        // Configurações do gráfico
        $chart = $lava->PieChart('PercentualAritmetica', $data, [
            'title' => 'Percentuais de Interpretações - Aritmética Coletiva',
            'titleTextStyle' => [
                'color'    => '#eb6b2c',
                'fontSize' => 14
            ],
            'legend' => [
                'position' => 'bottom',
                'textStyle' => [
                    'color' => '#555'
                ]
            ],
            'colors' => ['#4285F4', '#DB4437', '#F4B400', '#0F9D58', '#AB47BC', '#FF7043', '#9E9D24', '#5C6BC0', '#78909C']
        ]);
        */
        return view('testes.percentual', [
            'nomedaescola' => $nomedaescola,
            'percentuaisEscrita' => $percentuaisEscrita ?? [],
            'percentuaisAritmeticacoletiva' => $percentuaisAritmeticacoletiva ?? [],
            'percentuaisAritmeticaindividual' => $percentuaisAritmeticaindividual ?? [],
            'percentuaisLeituraindividual' => $percentuaisLeituraindividual ?? [],
            'percentuaisPsicogenese' => $percentuaisPsicogenese ?? [],
            'percentuaisPCFO' => $percentuaisPCFO ?? [],
            'escolas' => $escolas,
            'error',
            //'chart' => $chart
            //'chartEscrita' => $chartEscrita,
        ]);
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
