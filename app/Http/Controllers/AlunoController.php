<?php

namespace App\Http\Controllers;

//require __DIR__.'/vendor/autoload.php';

use App\Models\Aluno;
use App\Models\Escola;
use App\Models\Escrita;
use App\Models\Leituraindividual;
use App\Models\Aritmeticacoletiva;
use App\Models\Aritmeticaindividual;
use App\Models\Psicogenese;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AlunosExport;
use App\Exports\AlunosPorEscolaExport;
use Illuminate\Support\Str;



use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AlunoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        /*
        //método retorna TRUE se o usuário está logado e FALSE se não estiver logado
        if(auth()->check()){
            return 'Página de tarefa. Usuário logado';
        } else {
            return 'Não logado no sistema.';
        }
        */
        //$id = auth()->user()->id;
        //$name = auth()->user()->name;
        //$email = auth()->user()->email;

        //return "Usuário logado: Id: $id  | Nome: $name | E-mail: $email";

        return view('aluno.index');
    }

    public function buscar(Request $request)
    {
        $termoBusca = $request->input('busca');
        // Verifica se o termo de busca é numérico (código do aluno)
        if (is_numeric($termoBusca)) {
            $alunos = Aluno::where('codigo', $termoBusca)->get();
        } else {
            // Caso contrário, busca por nome
            $alunos = Aluno::where('nome', 'like', "%$termoBusca%")->get();
        }

        $resultados = [];
        $alunosForaDoIntervalo = false; // Flag para indicar se há alunos fora do intervalo

        foreach ($alunos as $aluno) {
            $dataNascimento = Carbon::parse($aluno->nascimento);
            $idade = $dataNascimento->age;

            if ($idade < 3 || $idade > 6) {

                $aluno->foraDoIntervalo = true;
                $alunosForaDoIntervalo = true;
            }

            $formularios = $aluno->formularios;

            $resultados[] = [
                'aluno' => $aluno,
                'formularios' => $formularios,
            ];
        }

        // Se houver alunos fora do intervalo, redirecione para a view de erro
        if ($alunosForaDoIntervalo) {
            return view('aluno.erros_resultados_busca', compact('resultados', 'idade'));
        }

        // Se nenhum aluno estiver fora do intervalo, retorne a view normal com os resultados
        return view('aluno.resultados_busca', compact('alunos'));
        //return redirect()->route('aluno.buscar', ['page' => $alunos->currentPage()])->with(compact('alunos'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $escolas = Escola::all();
        return view('aluno.create', compact('escolas'));
    }

    /**
     * Store a newly created resource in storage.
     * Armazene um recurso recém-criado no armazenamento.
     */
    public function store(Request $request)
    {
        //
        //dd($request->all());
        $request->validate(
            [
                'nome' => 'required',
                'nascimento' => 'required',
                'sexo' => 'required',
                'filiacao_1' => 'required',
                'filiacao_2' => 'required',
                'celular_contato' => 'required',
                'cep' => 'required',
                'endereco' => 'required',
                'etapa_aluno' => 'required',
                'escola_aluno' => 'required',
                'codigo' => 'required|unique:alunos,codigo',
            ],
            [
                'required' => 'O campo :attribute precisa ser preenchido!',
                'codigo.unique' => 'O código informado já está em uso.',
            ]
        );
        // Verificar se a escola com o ID fornecido existe
        $escolaId = $request->input('escola_aluno');
        $escola = Escola::find($escolaId);

        if (!$escola) {
            // Lidar com o caso em que a escola não é encontrada (por exemplo, redirecionar de volta ao formulário com uma mensagem de erro)
            return redirect()->back()->withErrors(['escola_aluno' => 'Escola não encontrada.']);
        }

        // Associar a escola ao aluno
        $aluno = new Aluno($request->all());
        $aluno->escola()->associate($escola);
        $aluno->save();

        return redirect()->route('aluno.create');
    }

    /**
     * Display the specified resource.
     * Exiba o recurso especificado.
     */
    public function show()
    {
        $alunos = Aluno::with('escola')->paginate(50);
        //dd($alunos);
        return view('aluno.listar', ['alunos' => $alunos]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aluno $aluno)
    {
        //
        $escolas = Escola::all();
        return view('aluno.update', compact('aluno', 'escolas'));
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aluno $aluno)
    {
        // Valide os dados recebidos do formulário
        $request->validate([
            'nome' => 'required',
            'nascimento' => 'required',
            'sexo' => 'required',
            'filiacao_1' => 'required',
            'filiacao_2' => 'required',
            'celular_contato' => 'required',
            'cep' => 'required',
            'endereco' => 'required',
            'etapa_aluno' => 'required',
            'escola_aluno' => 'required',
        ]);

        // Verifique se a escola com o ID fornecido existe
        $escolaId = $request->input('escola_aluno');
        $escola = Escola::find($escolaId);

        if (!$escola) {
            // Lidar com o caso em que a escola não é encontrada (por exemplo, redirecionar de volta ao formulário com uma mensagem de erro)
            return redirect()->back()->withErrors(['escola_aluno' => 'Escola não encontrada.']);
        }

        // Atualize os dados do aluno
        $aluno->update($request->all());

        // Associe a escola atualizada ao aluno
        $aluno->escola()->associate($escola);
        $aluno->save();

        return redirect()->route('aluno.show', ['aluno' => $aluno->id])->with('success', "Cadastro do aluno $aluno->nome atualizado com sucesso!");
    }

    public function obterDados()
    {
        // Obtenha todas as escolas para exibir no formulário
        $escolas = Escola::all();

        // Verifique se uma escola foi selecionada no formulário
        $escolaId = null;
        $alunosPrimeiroAno = [];
        $alunosSegundoAno = [];
        $alunosTerceiroAno = [];
        $alunosQuartoAno = [];
        $alunosQuintoAno = [];
        $escola = null;
        $nomedaescola = null;

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

            // Consulte os alunos do 1º ano da escola selecionada
            $alunosPrimeiroAno = Aluno::where('escola_id', $escolaId)
                ->where('etapa_aluno', '1º ano')
                ->get();

            // Consulte os alunos do 2º ano da escola selecionada
            $alunosSegundoAno = Aluno::where('escola_id', $escolaId)
                ->where('etapa_aluno', '2º ano')
                ->get();

            // Consulte os alunos do 3º ano da escola selecionada
            $alunosTerceiroAno = Aluno::where('escola_id', $escolaId)
                ->where('etapa_aluno', '3º ano')
                ->get();

            // Consulte os alunos do 4º ano da escola selecionada
            $alunosQuartoAno = Aluno::where('escola_id', $escolaId)
                ->where('etapa_aluno', '4º ano')
                ->get();

            // Consulte os alunos do 5º ano da escola selecionada
            $alunosQuintoAno = Aluno::where('escola_id', $escolaId)
                ->where('etapa_aluno', '5º ano')
                ->get();
        }

        return compact('escolas', 'escolaId', 'alunosPrimeiroAno', 'alunosSegundoAno', 'alunosTerceiroAno', 'alunosQuartoAno', 'alunosQuintoAno', 'escola', 'nomedaescola');
    }

    public function aritmeticaIndividual()
    {
        $dados = $this->obterDados();
        return view('testes.aritmeticaindividual', $dados);
    }
    public function escrita()
    {
        $dados = $this->obterDados();
        return view('testes.escrita', $dados);
    }

    public function aritmetica()
    {
        $dados = $this->obterDados();
        return view('testes.aritmetica', $dados);
    }

    public function leitura()
    {
        $dados = $this->obterDados();
        return view('testes.leitura', $dados);
    }

    public function leituraindividual()
    {
        $dados = $this->obterDados();
        return view('testes.leituraindividual', $dados);
    }

    public function psicogenese()
    {
        $dados = $this->obterDados();
        return view('testes.psicogenese', $dados);
    }
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    private function extrairCamposResposta(Request $request)
    {
        return $request->only(
            collect($request->all())
                ->filter(function ($value, $key) {
                    return strpos($key, 'resposta_') === 0; // Somente campos que começam com 'resposta_'
                })
                ->keys()
                ->toArray()
        );
    }

    private function validarRespostas(Request $request, array $respostas)
    {
        $regras = [];
        $mensagensErro = [
            'numeric' => 'Deve conter apenas números.',
            'required' => 'Precisa ser preenchido.',
        ];

        foreach ($respostas as $fieldName => $fieldValue) {
            $regras[$fieldName] = 'required|numeric'; // Adicionando regras para cada campo de resposta
        }

        return Validator::make($request->all(), $regras, $mensagensErro);
    }

    private function associarRespostas(array $respostas)
    {
        $alunosComRespostas = [];

        foreach ($respostas as $fieldName => $fieldValue) {
            $alunoId = intval(substr($fieldName, 9)); // "resposta_" possui 9 caracteres

            if (!isset($alunosComRespostas[$alunoId])) {
                $aluno = Aluno::find($alunoId);

                if (!$aluno) {
                    continue;
                }

                $alunosComRespostas[$alunoId] = [
                    'id' => $alunoId,
                    'nome' => $aluno->nome,
                    'turma' => $aluno->etapa_aluno,
                    'escola' => $aluno->escola_id,
                    'resposta' => [],
                ];
            }

            $alunosComRespostas[$alunoId]['resposta'][$fieldName] = $fieldValue;
        }
        //dd('alunos: ',$alunosComRespostas);
        return $alunosComRespostas;
    }

    private function interpretarPercentil($percentil)
    {
        if ($percentil <= 1) $interpretacao = 'Déficit muito grave';
        if ($percentil >= 1 && $percentil <= 5) $interpretacao = 'Déficit grave';
        if ($percentil >= 6 && $percentil <= 9) $interpretacao = 'Déficit de leve a moderado';
        if ($percentil >= 10 && $percentil <= 25) $interpretacao = 'Alerta para déficit (abaixo do esperado)';
        if ($percentil >= 26 && $percentil <= 40) $interpretacao = 'Médio-inferior (um pouco abaixo do esperado, mas ainda dentro da média; ou levemente abaixo do esperado)';
        if ($percentil >= 60 && $percentil <= 74) $interpretacao = 'Médio-superior (um pouco acima do esperado, mas ainda dentro da média; ou levemente acima do esperado)';
        if ($percentil >= 75 && $percentil <= 94) $interpretacao = 'Acima do esperado';
        if ($percentil >= 95 && $percentil <= 99) $interpretacao = 'Muito acima do esperado';
        if ($percentil > 99) $interpretacao = 'Desempenho desenvolvido em nível muito superior';
        //dd($percentil);
        return $interpretacao;
    }

    public function psicogenesePost(Request $request)
    {
        // Definindo as regras de validação para cada campo de resposta
        $regras = [];
        foreach ($request->all() as $key => $value) {
            $regras[$key] = 'required'; // Todos os campos são obrigatórios
        }

        // Mensagens de erro personalizadas
        $mensagensErro = [
            'required' => 'O campo é obrigatório.',
        ];

        // Realizando a validação dos dados
        $validacao = Validator::make($request->all(), $regras, $mensagensErro);

        // Verificando se houve erros na validação
        if ($validacao->fails()) {
            //dd('entrou');
            return back()->withErrors($validacao)->withInput();
        }
        $respostas = $this->extrairCamposResposta($request);
        $alunosComRespostas = $this->associarRespostas($respostas);
        $alunosPrimeiroAno = [];
        $alunosSegundoAno = [];
        $alunosTerceiroAno = [];
        foreach ($alunosComRespostas as $alunoId => $alunoData) {
            if ($alunoData['turma'] === '1º ANO') {
                foreach ($alunoData['resposta'] as $psico) {
                    switch ($psico) {
                        case 'Pré-silábico':
                            $psicogenese = 'Pré-silábico';
                            break;
                        case 'Silábico':
                            $psicogenese = 'Silábico';
                            break;
                        case 'Silábico-alfabético':
                            $psicogenese = 'Silábico-alfabético';
                            break;
                        case 'Alfabético':
                            $psicogenese = 'Alfabético';
                            break;
                    }
                }
            } elseif ($alunoData['turma'] === '2º ANO') {
                foreach ($alunoData['resposta'] as $psico) {
                    switch ($psico) {
                        case 'Pré-silábico':
                            $psicogenese = 'Pré-silábico';
                            break;
                        case 'Silábico':
                            $psicogenese = 'Silábico';
                            break;
                        case 'Silábico-alfabético':
                            $psicogenese = 'Silábico-alfabético';
                            break;
                        case 'Alfabético':
                            $psicogenese = 'Alfabético';
                            break;
                    }
                }
            } elseif ($alunoData['turma'] === '3º ANO') {
                foreach ($alunoData['resposta'] as $psico) {
                    switch ($psico) {
                        case 'Pré-silábico':
                            $psicogenese = 'Pré-silábico';
                            break;
                        case 'Silábico':
                            $psicogenese = 'Silábico';
                            break;
                        case 'Silábico-alfabético':
                            $psicogenese = 'Silábico-alfabético';
                            break;
                        case 'Alfabético':
                            $psicogenese = 'Alfabético';
                            break;
                    }
                }
            }
            $escola_id = $alunoData['escola'];
            $escola = Escola::find($escola_id);
            $nomedaescola = $escola->nome;
            //dd($nomedaescola);
            $aluno = [
                'id' => $alunoId,
                'nome' => $alunoData['nome'],
                'turma' => $alunoData['turma'],
                'psicogenese' => $psicogenese
            ];

            $psicodb = new Psicogenese();
            $psicodb->aluno_id = $aluno['id'];
            $psicodb->psicogenese = $aluno['psicogenese'];
            //dd($psicodb);

            try {
                $psicodb->save();
            } catch (\Exception $e) {
                Log::error('Erro ao salvar os dados: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Ocorreu um erro ao salvar os dados. Por favor, tente novamente.');
            }

            if ($alunoData['turma'] === '1º ANO') {
                $alunosPrimeiroAno[] = $aluno;
            } elseif ($alunoData['turma'] === '2º ANO') {
                $alunosSegundoAno[] = $aluno;
            } elseif ($alunoData['turma'] === '3º ANO') {
                $alunosTerceiroAno[] = $aluno;
            }
        }
        //dd($alunosPrimeiroAno, $alunosSegundoAno, $alunosTerceiroAno);
        return view('testes.psicogeneseresultado', [
            'titulo' => 'Resultados Nível Psicogênese 1º, 2º e 3º Anos',
            'alunosPrimeiroAno' => $alunosPrimeiroAno,
            'alunosSegundoAno' => $alunosSegundoAno,
            'alunosTerceiroAno' => $alunosTerceiroAno,
            'nomedaescola' => $nomedaescola,
            'success' => 'Prova realizada com sucesso!',
        ]);
    }

    public function leituraIndividualPost(Request $request)
    {
        //dd($dados);
        // Extrair apenas os campos de resposta do formulário
        $respostas = $this->extrairCamposResposta($request);
        //dd($respostas);
        // Executar a validação das respostas
        $validacao = $this->validarRespostas($request, $respostas);
        //dd($validacao);
        if ($validacao->fails()) {
            //dd($validacao);
            return back()->withErrors($validacao)->withInput();
        }
        $alunosComRespostas = $this->associarRespostas($respostas);

        $alunosPrimeiroAno = [];
        $alunosSegundoAno = [];
        $alunosTerceiroAno = [];

        foreach ($alunosComRespostas as $alunoId => $alunoData) {
            if ($alunoData['turma'] === '1º ANO') {
                foreach ($alunoData['resposta'] as $leitura) {
                    switch ($leitura) {
                        case 1:
                            $percentil = 60;
                            break;
                        case ($leitura >= 2 && $leitura <= 7):
                            $percentil = 70;
                            break;
                        case ($leitura >= 8 && $leitura <= 20):
                            $percentil = 75;
                            break;
                        case ($leitura >= 21 && $leitura <= 27):
                            $percentil = 80;
                            break;
                        case ($leitura >= 28 && $leitura <= 34):
                            $percentil = 90;
                            break;
                        case 35:
                            $percentil = 95;
                            break;
                        case ($leitura >= 36):
                            $percentil = 95;
                            break;
                        default:
                            $percentil = 0; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                    }
                }
            } elseif ($alunoData['turma'] === '2º ANO') {

                foreach ($alunoData['resposta'] as $leitura) {
                    switch ($leitura) {
                        case 1:
                            $percentil = 20;
                            break;
                        case ($leitura >= 2 && $leitura <= 10):
                            $percentil = 25;
                            break;
                        case ($leitura >= 11 && $leitura <= 20):
                            $percentil = 30;
                            break;
                        case ($leitura >= 21 && $leitura <= 25):
                            $percentil = 40;
                            break;
                        case ($leitura >= 26 && $leitura <= 32):
                            $percentil = 50;
                            break;
                        case ($leitura >= 33 && $leitura <= 34):
                            $percentil = 75;
                            break;
                        case 35:
                            $percentil = 80;
                            break;
                        case 36:
                            $percentil = 95;
                            break;
                        case ($leitura >= 37):
                            $percentil = 99;
                            break;
                        default:
                            $percentil = 0; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                    }
                }
            } elseif ($alunoData['turma'] === '3º ANO') {

                foreach ($alunoData['resposta'] as $leitura) {
                    switch ($leitura) {
                        case ($leitura >= 1 && $leitura <= 14):
                            $percentil = 1;
                            break;
                        case ($leitura >= 15 && $leitura <= 18):
                            $percentil = 5;
                            break;
                        case ($leitura >= 19 && $leitura <= 27):
                            $percentil = 10;
                            break;
                        case ($leitura >= 28 && $leitura <= 31):
                            $percentil = 20;
                            break;
                        case 32:
                            $percentil = 25;
                            break;
                        case 33:
                            $percentil = 30;
                            break;
                        case 34:
                            $percentil = 40;
                            break;
                        case 35:
                            $percentil = 70;
                            break;
                        case 36:
                            $percentil = 95;
                            break;
                        case ($leitura >= 37):
                            $percentil = 99;
                            break;
                        default:
                            $percentil = 0; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                    }
                }
            }
            $escola_id = $alunoData['escola'];
            $escola = Escola::find($escola_id);
            $nomedaescola = $escola->nome;
            //dd($nomedaescola);
            $interpretacao = $this->interpretarPercentil($percentil);
            $resposta = reset($alunoData['resposta']);
            $aluno = [
                'id' => $alunoId,
                'nome' => $alunoData['nome'],
                'turma' => $alunoData['turma'],
                'resposta' => $alunoData['resposta'],
                'percentil' => $percentil,
                'interpretação' => $interpretacao
            ];
            //dd($aluno);
            $leituraIndividual = new Leituraindividual();
            $leituraIndividual->aluno_id = $aluno['id'];
            $leituraIndividual->resposta = $resposta;
            $leituraIndividual->percentil = $aluno['percentil'];
            $leituraIndividual->interpretacao = $aluno['interpretação'];
            //dd($leituraIndividual);

            try {
                $leituraIndividual->save();
            } catch (\Exception $e) {
                Log::error('Erro ao salvar os dados: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Ocorreu um erro ao salvar os dados. Por favor, tente novamente.');
            }

            if ($alunoData['turma'] === '1º ANO') {
                $alunosPrimeiroAno[] = $aluno;
            } elseif ($alunoData['turma'] === '2º ANO') {
                $alunosSegundoAno[] = $aluno;
            } elseif ($alunoData['turma'] === '3º ANO') {
                $alunosTerceiroAno[] = $aluno;
            }
        }
        //dd($nomedaescola);
        //dd($alunosPrimeiroAno, $alunosSegundoAno, $alunosTerceiroAno);
        $teste = 'Leitura';
        return view('testes.resultados123', [
            'titulo' => 'Resultados do Teste de Leitura Individual 1º, 2º e 3º Anos',
            'alunosPrimeiroAno' => $alunosPrimeiroAno,
            'alunosSegundoAno' => $alunosSegundoAno,
            'alunosTerceiroAno' => $alunosTerceiroAno,
            'nomedaescola' => $nomedaescola,
            'success' => 'Prova realizada com sucesso!',
            'teste' => $teste
        ]);
    }

    public function aritmeticaIndividualPost(Request $request)
    {
        $alunos = Aluno::has('formularios')->get();
        $dados = $this->obterDados();
        $nomedaescola = $dados['nomedaescola'];
        //dd($nomedaescola);
        // Extrair apenas os campos de resposta do formulário
        $respostas = $this->extrairCamposResposta($request);
        //dd($respostas);
        // Executar a validação das respostas
        $validacao = $this->validarRespostas($request, $respostas);
        //dd($validacao);
        if ($validacao->fails()) {
            return back()->withErrors($validacao)->withInput();
        }
        //dd($validacao);
        // Associar respostas aos alunos
        $alunosComRespostas = $this->associarRespostas($respostas);
        //dd($alunosComRespostas);

        $alunosPrimeiroAno = [];
        $alunosSegundoAno = [];
        $alunosTerceiroAno = [];

        foreach ($alunosComRespostas as $alunoId => $alunoData) {
            if ($alunoData['turma'] === '1º ANO') {
                foreach ($alunoData['resposta'] as $aritmetica) {
                    switch ($aritmetica) {
                        case 1:
                            $percentil = 5;
                            break;
                        case ($aritmetica >= 2 && $aritmetica <= 3):
                            $percentil = 10;
                            break;
                        case ($aritmetica >= 4 && $aritmetica <= 5):
                            $percentil = 25;
                            break;
                        case 6:
                            $percentil = 30;
                            break;
                        case 7:
                            $percentil = 40;
                            break;
                        case 8:
                            $percentil = 50;
                            break;
                        case 9:
                            $percentil = 60;
                            break;
                        case ($aritmetica >= 10 && $aritmetica <= 11):
                            $percentil = 75;
                            break;
                        case 12:
                            $percentil = 80;
                            break;
                        case 13:
                            $percentil = 90;
                            break;
                        case 15:
                            $percentil = 95;
                            break;
                        case ($aritmetica >= 16):
                            $percentil = 99;
                            break;
                        default:
                            $percentil = 1; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                    }
                }
            } elseif ($alunoData['turma'] === '2º ANO') {

                foreach ($alunoData['resposta'] as $aritmetica) {
                    switch ($aritmetica) {
                        case ($aritmetica >= 1 && $aritmetica <= 4):
                            $percentil = 1;
                            break;
                        case ($aritmetica >= 5 && $aritmetica <= 7):
                            $percentil = 5;
                            break;
                        case 8:
                            $percentil = 10;
                            break;
                        case 9:
                            $percentil = 20;
                            break;
                        case 10:
                            $percentil = 30;
                            break;
                        case 11:
                            $percentil = 40;
                            break;
                        case 12:
                            $percentil = 50;
                            break;
                        case ($aritmetica >= 13 && $aritmetica <= 14):
                            $percentil = 60;
                            break;
                        case 15:
                            $percentil = 70;
                            break;
                        case 16:
                            $percentil = 75;
                            break;
                        case 17:
                            $percentil = 80;
                            break;
                        case ($aritmetica >= 18 && $aritmetica <= 19):
                            $percentil = 90;
                            break;
                        case 20:
                            $percentil = 95;
                            break;
                        case ($aritmetica >= 21):
                            $percentil = 99;
                            break;
                        default:
                            $percentil = 0; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                    }
                }
            } elseif ($alunoData['turma'] === '3º ANO') {

                foreach ($alunoData['resposta'] as $aritmetica) {
                    switch ($aritmetica) {
                        case ($aritmetica >= 1 && $aritmetica <= 11):
                            $percentil = 1;
                            break;
                        case 12:
                            $percentil = 5;
                            break;
                        case 13:
                            $percentil = 10;
                            break;
                        case ($aritmetica >= 14 && $aritmetica <= 16):
                            $percentil = 20;
                            break;
                        case 17:
                            $percentil = 25;
                            break;
                        case 18:
                            $percentil = 30;
                            break;
                        case 19:
                            $percentil = 40;
                            break;
                        case 20:
                            $percentil = 60;
                            break;
                        case ($aritmetica >= 21 && $aritmetica <= 22):
                            $percentil = 70;
                            break;
                        case 23:
                            $percentil = 75;
                            break;
                        case 24:
                            $percentil = 80;
                            break;
                        case 25:
                            $percentil = 90;
                            break;
                        case 26:
                            $percentil = 95;
                            break;
                        case ($aritmetica >= 27):
                            $percentil = 99;
                            break;
                        default:
                            $percentil = 0; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                    }
                }
            }
            $escola_id = $alunoData['escola'];
            $escola = Escola::find($escola_id);
            $nomedaescola = $escola->nome;

            $interpretacao = $this->interpretarPercentil($percentil);
            $resposta = reset($alunoData['resposta']);
            $aluno = [
                'id' => $alunoId,
                'nome' => $alunoData['nome'],
                'turma' => $alunoData['turma'],
                'resposta' => $alunoData['resposta'],
                'percentil' => $percentil,
                'interpretação' => $interpretacao
            ];

            $aritmeticaIndividual = new Aritmeticaindividual();
            $aritmeticaIndividual->aluno_id = $aluno['id'];
            $aritmeticaIndividual->resposta = $resposta;
            $aritmeticaIndividual->percentil = $aluno['percentil'];
            $aritmeticaIndividual->interpretacao = $aluno['interpretação'];

            try {
                $aritmeticaIndividual->save();
            } catch (\Exception $e) {
                Log::error('Erro ao salvar os dados: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Ocorreu um erro ao salvar os dados. Por favor, tente novamente.');
            }

            if ($alunoData['turma'] === '1º ANO') {
                $alunosPrimeiroAno[] = $aluno;
            } elseif ($alunoData['turma'] === '2º ANO') {
                $alunosSegundoAno[] = $aluno;
            } elseif ($alunoData['turma'] === '3º ANO') {
                $alunosTerceiroAno[] = $aluno;
            }
        }
        //dd($nomedaescola);
        //dd($alunosPrimeiroAno, $alunosSegundoAno, $alunosTerceiroAno);
        $teste = 'Aritmetica';
        return view('testes.resultados123', [
            'titulo' => 'Resultados do Teste de Aritmetica Individual 1º, 2º e 3º Anos',
            'alunosPrimeiroAno' => $alunosPrimeiroAno,
            'alunosSegundoAno' => $alunosSegundoAno,
            'alunosTerceiroAno' => $alunosTerceiroAno,
            'nomedaescola' => $nomedaescola,
            'success' => 'Prova realizada com sucesso!',
            'teste' => $teste
        ]);
    }

    public function escritapostTeste(Request $request)
    {
        $alunos = Aluno::has('formularios')->get();
        //dd($alunos);
        // Extrair apenas os campos de resposta do formulário
        $respostas = $this->extrairCamposResposta($request);

        // Executar a validação das respostas
        $validacao = $this->validarRespostas($request, $respostas);

        if ($validacao->fails()) {
            return back()->withErrors($validacao)->withInput();
        }

        // Associar respostas aos alunos
        $alunosComRespostas = $this->associarRespostas($respostas);

        // Separar os alunos do 4º e 5º ano com base nas respostas
        $alunosQuartoAno = [];
        $alunosQuintoAno = [];

        foreach ($alunosComRespostas as $alunoId => $alunoData) {
            if ($alunoData['turma'] === '4º ANO') {
                foreach ($alunoData['resposta'] as $respostaValue) {
                    switch ($respostaValue) {
                        case ($respostaValue >= 1 && $respostaValue <= 6):
                            $percentil = 1;
                            break;
                        case ($respostaValue >= 7 && $respostaValue <= 12):
                            $percentil = 5;
                            break;
                        case ($respostaValue >= 13 && $respostaValue <= 16):
                            $percentil = 10;
                            break;
                        case ($respostaValue >= 17 && $respostaValue <= 22):
                            $percentil = 20;
                            break;
                        case 23:
                            $percentil = 25;
                            break;
                        case ($respostaValue >= 24 && $respostaValue <= 25):
                            $percentil = 30;
                            break;
                        case 26:
                            $percentil = 40;
                            break;
                        case ($respostaValue >= 27 && $respostaValue <= 28):
                            $percentil = 50;
                            break;
                        case ($respostaValue >= 29 && $respostaValue <= 30):
                            $percentil = 60;
                            break;
                        case ($respostaValue >= 31 && $respostaValue <= 32):
                            $percentil = 70;
                            break;
                        case 33:
                            $percentil = 75;
                            break;
                        case 34:
                            $percentil = 80;
                            break;
                        case 35:
                            $percentil = 90;
                            break;
                        case 36:
                            $percentil = 95;
                            break;
                        case ($respostaValue >= 37):
                            $percentil = 99;
                            break;
                        default:
                            $percentil = 0; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                    }
                }
            } elseif ($alunoData['turma'] === '5º ANO') {

                foreach ($alunoData['resposta'] as $respostaValue) {
                    switch ($respostaValue) {
                        case 1:
                            $percentil = 1;
                            break;
                        case 2:
                            $percentil = 5;
                            break;
                        case 3:
                            $percentil = 10;
                            break;
                        case ($respostaValue >= 4 && $respostaValue <= 6):
                            $percentil = 20;
                            break;
                        case 7:
                            $percentil = 25;
                            break;
                        case 8:
                            $percentil = 30;
                            break;
                        case ($respostaValue >= 9 && $respostaValue <= 11):
                            $percentil = 40;
                            break;
                        case ($respostaValue >= 12 && $respostaValue <= 13):
                            $percentil = 50;
                            break;
                        case ($respostaValue >= 14 && $respostaValue <= 15):
                            $percentil = 60;
                            break;
                        case ($respostaValue >= 16 && $respostaValue <= 18):
                            $percentil = 70;
                            break;
                        case 19:
                            $percentil = 75;
                            break;
                        case 20:
                            $percentil = 80;
                            break;
                        case ($respostaValue >= 21 && $respostaValue <= 24):
                            $percentil = 90;
                            break;
                        case ($respostaValue >= 25 && $respostaValue <= 27):
                            $percentil = 95;
                            break;
                        case ($respostaValue >= 28):
                            $percentil = 99;
                            break;
                        default:
                            $percentil = 0; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                    }
                }
            }

            $escola_id = $alunoData['escola'];
            $escola = Escola::find($escola_id);
            $nomedaescola = $escola->nome;

            $interpretacao = $this->interpretarPercentil($percentil);
            $resposta = reset($alunoData['resposta']);
            $aluno = [
                'id' => $alunoId,
                'nome' => $alunoData['nome'],
                'turma' => $alunoData['turma'],
                'resposta' => $alunoData['resposta'],
                'percentil' => $percentil,
                'interpretação' => $interpretacao
            ];
            //dd($aluno);
            $escrita = new Escrita();
            $escrita->aluno_id = $aluno['id']; // Atribuir o ID do aluno
            $escrita->resposta = $resposta;
            $escrita->percentil = $aluno['percentil']; // Atribuir o percentil
            $escrita->interpretacao = $aluno['interpretação']; // Atribuir a interpretação
            //dd($escrita);

            $escrita->save();
            //dd($escrita);

            if ($alunoData['turma'] === '4º ANO') {
                $alunosQuartoAno[] = $aluno;
            } elseif ($alunoData['turma'] === '5º ANO') {
                $alunosQuintoAno[] = $aluno;
            }
        }

        // Agora $alunosQuartoAno contém os alunos do 4º ano e suas respostas associadas, e o mesmo para $alunosQuintoAno com os alunos do 5º ano
        //dd($alunosQuartoAno, $alunosQuintoAno);
        $teste = 'Escrita';
        return view('testes.resultados', [
            'titulo' => 'Resultados do Teste de Escrita',
            'alunosQuintoAno' => $alunosQuintoAno,
            'alunosQuartoAno' => $alunosQuartoAno,
            'nomedaescola' => $nomedaescola,
            'success' => 'Prova realizada com sucesso!',
            'teste' => $teste
        ]);
    }

    public function aritmeticapostTeste(Request $request)
    {
        // Extrair apenas os campos de resposta do formulário
        $respostas = $this->extrairCamposResposta($request);

        // Executar a validação das respostas
        $validacao = $this->validarRespostas($request, $respostas);

        if ($validacao->fails()) {
            return back()->withErrors($validacao)->withInput();
        }

        // Associar respostas aos alunos
        $alunosComRespostas = $this->associarRespostas($respostas);

        // Separar os alunos do 4º e 5º ano com base nas respostas
        $alunosQuartoAno = [];
        $alunosQuintoAno = [];
        foreach ($alunosComRespostas as $alunoId => $alunoData) {
            foreach ($alunoData['resposta'] as $respostaValue) {
                switch ($respostaValue) {
                    case ($respostaValue >= 1 && $respostaValue <= 16):
                        $percentil = 1;
                        break;
                    case ($respostaValue >= 17 && $respostaValue <= 18):
                        $percentil = 5;
                        break;
                    case ($respostaValue >= 19 && $respostaValue <= 20):
                        $percentil = 10;
                        break;
                    case ($respostaValue >= 21 && $respostaValue <= 22):
                        $percentil = 25;
                        break;
                    case 23:
                        $percentil = 30;
                        break;
                    case 24:
                        $percentil = 40;
                        break;
                    case 25:
                        $percentil = 50;
                        break;
                    case 26:
                        $percentil = 60;
                        break;
                    case ($respostaValue >= 27 && $respostaValue <= 28):
                        $percentil = 75;
                        break;
                    case 29:
                        $percentil = 80;
                        break;
                    case ($respostaValue >= 30 && $respostaValue <= 32):
                        $percentil = 90;
                        break;
                    case 33:
                        $percentil = 95;
                        break;
                    case ($respostaValue >= 34):
                        $percentil = 99;
                        break;
                    default:
                        $percentil = 0; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                }
            }
            $interpretacao = $this->interpretarPercentil($percentil);
            $resposta = reset($alunoData['resposta']);
            $aluno = [
                'id' => $alunoId,
                'nome' => $alunoData['nome'],
                'turma' => $alunoData['turma'],
                'resposta' => $alunoData['resposta'],
                'percentil' => $percentil,
                'interpretação' => $interpretacao
            ];
            $escola_id = $alunoData['escola'];
            $escola = Escola::find($escola_id);
            $nomedaescola = $escola->nome;

            $aritmeticaColetiva = new Aritmeticacoletiva();
            $aritmeticaColetiva->aluno_id = $aluno['id'];
            $aritmeticaColetiva->resposta = $resposta;
            $aritmeticaColetiva->percentil = $aluno['percentil'];
            $aritmeticaColetiva->interpretacao = $aluno['interpretação'];

            try {
                $aritmeticaColetiva->save();
            } catch (\Exception $e) {
                Log::error('Erro ao salvar os dados: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Ocorreu um erro ao salvar os dados. Por favor, tente novamente.');
            }

            if ($alunoData['turma'] === '4º ANO') {
                $alunosQuartoAno[] = $aluno;
            } elseif ($alunoData['turma'] === '5º ANO') {
                $alunosQuintoAno[] = $aluno;
            }
        }
        //dd($alunosQuartoAno, $alunosQuintoAno);
        //dd(session('success'));
        $teste = 'Aritmética';
        return view('testes.resultados', [
            'titulo' => 'Resultados do Teste de Aritmética Coletiva 4º e 5º Anos',
            'alunosQuintoAno' => $alunosQuintoAno,
            'alunosQuartoAno' => $alunosQuartoAno,
            'nomedaescola' => $nomedaescola,
            'success' => 'Prova realizada com sucesso!',
            'teste' => $teste
        ]) //->with('success', 'Prova realizada com sucesso!');
        ;
    }

    public function leiturapostTeste(Request $request)
    {
        // Extrair apenas os campos de resposta do formulário
        $respostas = $this->extrairCamposResposta($request);

        // Executar a validação das respostas
        $validacao = $this->validarRespostas($request, $respostas);

        if ($validacao->fails()) {
            return back()->withErrors($validacao)->withInput();
        }

        // Associar respostas aos alunos
        $alunosComRespostas = $this->associarRespostas($respostas);

        // Separar os alunos do 4º e 5º ano com base nas respostas
        $alunosQuartoAno = [];
        $alunosQuintoAno = [];

        foreach ($alunosComRespostas as $alunoId => $alunoData) {
            if ($alunoData['turma'] === '4º ANO') {
                foreach ($alunoData['resposta'] as $respostaValue) {
                    switch ($respostaValue) {
                        case 0:
                            $percentil = 1;
                            break;
                        case ($respostaValue >= 1 && $respostaValue <= 31):
                            $percentil = 5;
                            break;
                        case 32:
                            $percentil = 10;
                            break;
                        case ($respostaValue >= 33 && $respostaValue <= 34):
                            $percentil = 25;
                            break;
                        case 35:
                            $percentil = 50;
                            break;
                        case 36:
                            $percentil = 95;
                            break;
                        case ($respostaValue > 36):
                            $percentil = 99;
                            break;
                    }
                }
            } elseif ($alunoData['turma'] === '5º ANO') {
                foreach ($alunoData['resposta'] as $respostaValue) {
                    switch ($respostaValue) {
                        case ($respostaValue >= 1 && $respostaValue <= 15):
                            $percentil = 1;
                            break;
                        case ($respostaValue >= 16 && $respostaValue <= 23):
                            $percentil = 10;
                            break;
                        case ($respostaValue >= 24 && $respostaValue <= 25):
                            $percentil = 20;
                            break;
                        case ($respostaValue >= 26 && $respostaValue <= 28):
                            $percentil = 40;
                            break;
                        case 29:
                            $percentil = 60;
                            break;
                        case ($respostaValue >= 30 && $respostaValue <= 31):
                            $percentil = 80;
                            break;
                        case 32:
                            $percentil = 95;
                            break;
                        case ($respostaValue > 32):
                            $percentil = 99;
                            break;
                        default:
                            $percentil = 0; // Define um valor padrão caso 'escrita' não corresponda a nenhum dos casos acima
                    }
                }
            }
            $escola_id = $alunoData['escola'];
            $escola = Escola::find($escola_id);
            $nomedaescola = $escola->nome;

            $interpretacao = $this->interpretarPercentil($percentil);
            $resposta = reset($alunoData['resposta']);
            $aluno = [
                'id' => $alunoId,
                'nome' => $alunoData['nome'],
                'turma' => $alunoData['turma'],
                'resposta' => $alunoData['resposta'],
                'percentil' => $percentil,
                'interpretação' => $interpretacao
            ];

            $leituraIndividual = new Leituraindividual();
            $leituraIndividual->aluno_id = $aluno['id'];
            $leituraIndividual->resposta = $resposta;
            $leituraIndividual->percentil = $aluno['percentil'];
            $leituraIndividual->interpretacao = $aluno['interpretação'];
            //dd($leituraIndividual);

            try {
                $leituraIndividual->save();
            } catch (\Exception $e) {
                Log::error('Erro ao salvar os dados: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Ocorreu um erro ao salvar os dados. Por favor, tente novamente.');
            }

            if ($alunoData['turma'] === '4º ANO') {
                $alunosQuartoAno[] = $aluno;
            } elseif ($alunoData['turma'] === '5º ANO') {
                $alunosQuintoAno[] = $aluno;
            }
        }
        $teste = 'Leitura';
        return view(
            'testes.resultados',
            [
                'titulo' => 'Resultados do Teste de Leitura Individual 4º e 5º Anos',
                'alunosQuintoAno' => $alunosQuintoAno,
                'alunosQuartoAno' => $alunosQuartoAno,
                'nomedaescola' => $nomedaescola,
                'success' => 'Prova realizada com sucesso!',
                'teste' => $teste
            ]
        );
    }


    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aluno $aluno)
    {
        //
    }

    /**
     * ============================================================================================================
     *  EXPORTAÇÃO DOS RESULTADOS
     * ============================================================================================================
     */
    public function exportacao()
    {
        // Obtenha todos os alunos do banco de dados com pelo menos um resultado em uma das tabelas relacionadas
        $alunos = Aluno::whereHas('formularios')
            ->orWhereHas('escritas')
            ->orWhereHas('aritmeticacoletivas')
            ->orWhereHas('aritmeticaindividuals')
            ->orWhereHas('leituraindividuals')
            ->orWhereHas('psicogeneses')
            ->with(['formularios', 'escritas', 'aritmeticacoletivas', 'aritmeticaindividuals', 'leituraindividuals', 'psicogeneses'])
            ->get();

        // Se nenhum aluno tiver resultados em nenhuma das tabelas relacionadas, encerre a execução
        if ($alunos->isEmpty()) {
            return redirect()->back()->withErrors(['Não há alunos com resultados para exportar.']);
        }


        // Crie uma nova planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adicione cabeçalhos
        $headers = [
            'Código',
            'Nome',
            'Data de Nascimento',
            'Idade',
            'Sexo',
            'Etapa Aluno',
            'Escola Aluno',
            'Data do Teste',
            'Escore PCFO',
            'Pontuação Padrão PCFO',
            'Resultado do Teste PCFO',
            'Data do Teste',
            'Resposta Escrita Coletiva',
            'Percentil Escrita Coletiva',
            'Interpretação Escrita Coletiva',
            'Data do Teste',
            'Resposta Aritmética Coletiva',
            'Percentil Aritmética Coletiva',
            'Interpretação Aritmética Coletiva',
            'Data do Teste',
            'Resposta Aritmética Individual ',
            'Percentil Aritmética Individual ',
            'Interpretação Aritmética Individual',
            'Data do Teste',
            'Resposta Leitura Individual ',
            'Percentil Leitura Individual ',
            'Interpretação Leitura Individual',
            'Data do Teste',
            'Nível Psicogênese da Língua Escrita'

        ];
        $sheet->fromArray([$headers], null, 'A1');

        // Define a largura das colunas
        foreach (range('A', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Adicione os dados dos alunos
        $row = 2; // Início dos dados na segunda linha
        foreach ($alunos as $aluno) {
            // Calcula a idade do aluno
            $dataNascimento = Carbon::parse($aluno->nascimento);
            $idade = $dataNascimento->age;


            // Prepara os dados básicos do aluno
            $alunoData = [
                $aluno->codigo,
                $aluno->nome,
                $dataNascimento->format('d/m/Y'),
                $idade,
                $aluno->sexo,
                $aluno->etapa_aluno,
                $aluno->escola->nome,
            ];

            // Adicione os resultados do formulário, se existirem
            $formulario = $aluno->formularios->first();
            if ($formulario) {
                $formData = [
                    $formulario->created_at->format('d/m/Y'),
                    $formulario->escore,
                    $formulario->pontuacao_padrao,
                    $formulario->resultado_teste,
                ];
            } else {
                // Se não houver resultados de formulário, preencha com valores vazios
                $formData = array_fill(0, 4, '');
            }

            // Adicione os resultados de escrita, se existirem
            $escrita = $aluno->escritas->first();
            if ($escrita) {
                $escritaData = [
                    $escrita->created_at->format('d/m/Y'),
                    $escrita->resposta,
                    $escrita->percentil,
                    $escrita->interpretacao,
                ];
            } else {
                // Se não houver resultados de escrita, preencha com valores vazios
                $escritaData = array_fill(0, 4, '');
            }

            // Adicione os resultados de aritmética coletiva, se existirem
            $aritmeticaColetiva = $aluno->aritmeticacoletivas->first();
            if ($aritmeticaColetiva) {
                $aritmeticaColetivaData = [
                    $aritmeticaColetiva->created_at->format('d/m/Y'),
                    $aritmeticaColetiva->resposta,
                    $aritmeticaColetiva->percentil,
                    $aritmeticaColetiva->interpretacao,
                ];
            } else {
                // Se não houver resultados de aritmética coletiva, preencha com valores vazios
                $aritmeticaColetivaData = array_fill(0, 4, '');
            }

            // Adicione os resultados de aritmética individual, se existirem
            $aritmeticaIndividual = $aluno->aritmeticaindividuals->first();
            if ($aritmeticaIndividual) {
                $aritmeticaIndividualData = [
                    $aritmeticaIndividual->created_at->format('d/m/Y'),
                    $aritmeticaIndividual->resposta,
                    $aritmeticaIndividual->percentil,
                    $aritmeticaIndividual->interpretacao,
                ];
            } else {
                // Se não houver resultados de aritmética individual, preencha com valores vazios
                $aritmeticaIndividualData = array_fill(0, 4, '');
            }

            // Adicione os resultados de leitura individual, se existirem
            $leituraIndividual = $aluno->leituraindividuals->first();
            if ($leituraIndividual) {
                $leituraIndividualData = [
                    $leituraIndividual->created_at->format('d/m/Y'),
                    $leituraIndividual->resposta,
                    $leituraIndividual->percentil,
                    $leituraIndividual->interpretacao,
                ];
            } else {
                // Se não houver resultados de leitura individual, preencha com valores vazios
                $leituraIndividualData = array_fill(0, 4, '');
            }

            // Adicione os resultados de psicogênese, se existirem
            $psicogenese = $aluno->psicogeneses->first();
            if ($psicogenese) {
                $psicogeneseData = [
                    $psicogenese->created_at->format('d/m/Y'),
                    $psicogenese->psicogenese,
                ];
            } else {
                // Se não houver resultados de psicogênese, preencha com valores vazios
                $psicogeneseData = [''];
            }

            // Combine todos os dados do aluno
            $rowData = array_merge($alunoData, $formData, $escritaData, $aritmeticaColetivaData, $aritmeticaIndividualData, $leituraIndividualData, $psicogeneseData);

            // Adicione os dados do aluno à planilha
            $sheet->fromArray([$rowData], null, "A$row");

            $row++;
        }

        // Configurar o cabeçalho de resposta para forçar o download do arquivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="alunos.xlsx"');
        header('Cache-Control: max-age=0');

        // Salvar a planilha em um arquivo temporário
        $writer = new Xlsx($spreadsheet);
        $tempFilePath = tempnam(sys_get_temp_dir(), 'export_');
        $writer->save($tempFilePath);

        // Ler e enviar o arquivo para o navegador
        readfile($tempFilePath);

        // Excluir o arquivo temporário após o download
        unlink($tempFilePath);

        exit(); // Termina a execução do script
    }

    public function exportacaoescola()
    {
        $escolas = Escola::all();
        //dd($escolas);
        return view('aluno.exportacaoescola', compact('escolas'));
    }



    public function exportacaoescolapost(Request $request)
    {
        $escolaId = $request->input('escola_id');
        $escola = Escola::find($escolaId);

        if (!$escola) {
            return redirect()->back()->withErrors(['escola_id' => 'Escola não encontrada.']);
        }

        // Obtenha todos os alunos da escola com pelo menos um resultado em uma das tabelas relacionadas
        $alunos = Aluno::where('escola_id', $escolaId)
            ->where(function ($query) {
                $query->has('formularios')
                    ->orWhereHas('escritas')
                    ->orWhereHas('aritmeticacoletivas')
                    ->orWhereHas('aritmeticaindividuals')
                    ->orWhereHas('leituraindividuals')
                    ->orWhereHas('psicogeneses');
            })
            ->with(['formularios', 'escritas', 'aritmeticacoletivas', 'aritmeticaindividuals', 'leituraindividuals', 'psicogeneses'])
            ->get();

        // Se nenhum aluno tiver resultados em nenhuma das tabelas relacionadas, encerre a execução
        if ($alunos->isEmpty()) {
            return redirect()->back()->withErrors(['escola_id' => 'Não há alunos com resultados para exportar.']);
        }

        // Crie uma nova planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adicione cabeçalhos
        $headers = [
            'Código',
            'Nome',
            'Data de Nascimento',
            'Idade',
            'Sexo',
            'Etapa Aluno',
            'Escola Aluno',
            'Data do Teste',
            'Escore PCFO',
            'Pontuação Padrão PCFO',
            'Resultado do Teste PCFO',
            'Data do Teste',
            'Resposta Escrita Coletiva',
            'Percentil Escrita Coletiva',
            'Interpretação Escrita Coletiva',
            'Data do Teste',
            'Resposta Aritmética Coletiva',
            'Percentil Aritmética Coletiva',
            'Interpretação Aritmética Coletiva',
            'Data do Teste',
            'Resposta Aritmética Individual ',
            'Percentil Aritmética Individual ',
            'Interpretação Aritmética Individual',
            'Data do Teste',
            'Resposta Leitura Individual ',
            'Percentil Leitura Individual ',
            'Interpretação Leitura Individual',
            'Data do Teste',
            'Nível Psicogênese da Língua Escrita'

        ];
        $sheet->fromArray([$headers], null, 'A1');

        // Define a largura das colunas
        foreach (range('A', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Adicione os dados dos alunos
        $row = 2; // Início dos dados na segunda linha
        foreach ($alunos as $aluno) {
            // Calcula a idade do aluno
            $dataNascimento = Carbon::parse($aluno->nascimento);
            $idade = $dataNascimento->age;


            // Prepara os dados básicos do aluno
            $alunoData = [
                $aluno->codigo,
                $aluno->nome,
                $dataNascimento->format('d/m/Y'),
                $idade,
                $aluno->sexo,
                $aluno->etapa_aluno,
                $aluno->escola->nome,
            ];

            // Adicione os resultados do formulário, se existirem
            $formulario = $aluno->formularios->first();
            if ($formulario) {
                $formData = [
                    $formulario->created_at->format('d/m/Y'),
                    $formulario->escore,
                    $formulario->pontuacao_padrao,
                    $formulario->resultado_teste,
                ];
            } else {
                // Se não houver resultados de formulário, preencha com valores vazios
                $formData = array_fill(0, 4, '');
            }

            // Adicione os resultados de escrita, se existirem
            $escrita = $aluno->escritas->first();
            if ($escrita) {
                $escritaData = [
                    $escrita->created_at->format('d/m/Y'),
                    $escrita->resposta,
                    $escrita->percentil,
                    $escrita->interpretacao,
                ];
            } else {
                // Se não houver resultados de escrita, preencha com valores vazios
                $escritaData = array_fill(0, 4, '');
            }

            // Adicione os resultados de aritmética coletiva, se existirem
            $aritmeticaColetiva = $aluno->aritmeticacoletivas->first();
            if ($aritmeticaColetiva) {
                $aritmeticaColetivaData = [
                    $aritmeticaColetiva->created_at->format('d/m/Y'),
                    $aritmeticaColetiva->resposta,
                    $aritmeticaColetiva->percentil,
                    $aritmeticaColetiva->interpretacao,
                ];
            } else {
                // Se não houver resultados de aritmética coletiva, preencha com valores vazios
                $aritmeticaColetivaData = array_fill(0, 4, '');
            }

            // Adicione os resultados de aritmética individual, se existirem
            $aritmeticaIndividual = $aluno->aritmeticaindividuals->first();
            if ($aritmeticaIndividual) {
                $aritmeticaIndividualData = [
                    $aritmeticaIndividual->created_at->format('d/m/Y'),
                    $aritmeticaIndividual->resposta,
                    $aritmeticaIndividual->percentil,
                    $aritmeticaIndividual->interpretacao,
                ];
            } else {
                // Se não houver resultados de aritmética individual, preencha com valores vazios
                $aritmeticaIndividualData = array_fill(0, 4, '');
            }

            // Adicione os resultados de leitura individual, se existirem
            $leituraIndividual = $aluno->leituraindividuals->first();
            if ($leituraIndividual) {
                $leituraIndividualData = [
                    $leituraIndividual->created_at->format('d/m/Y'),
                    $leituraIndividual->resposta,
                    $leituraIndividual->percentil,
                    $leituraIndividual->interpretacao,
                ];
            } else {
                // Se não houver resultados de leitura individual, preencha com valores vazios
                $leituraIndividualData = array_fill(0, 4, '');
            }

            // Adicione os resultados de psicogênese, se existirem
            $psicogenese = $aluno->psicogeneses->first();
            if ($psicogenese) {
                $psicogeneseData = [
                    $psicogenese->created_at->format('d/m/Y'),
                    $psicogenese->psicogenese,
                ];
            } else {
                // Se não houver resultados de psicogênese, preencha com valores vazios
                $psicogeneseData = [''];
            }

            // Combine todos os dados do aluno
            $rowData = array_merge($alunoData, $formData, $escritaData, $aritmeticaColetivaData, $aritmeticaIndividualData, $leituraIndividualData, $psicogeneseData);

            // Adicione os dados do aluno à planilha
            $sheet->fromArray([$rowData], null, "A$row");

            $row++;
        }

        // Configurar o cabeçalho de resposta para forçar o download do arquivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="alunos_' . $escola->nome . '.xlsx"');
        header('Cache-Control: max-age=0');

        // Salvar a planilha em um arquivo temporário
        $writer = new Xlsx($spreadsheet);
        $tempFilePath = tempnam(sys_get_temp_dir(), 'export_');
        $writer->save($tempFilePath);

        // Ler e enviar o arquivo para o navegador
        readfile($tempFilePath);

        // Excluir o arquivo temporário após o download
        unlink($tempFilePath);

        exit(); // Termina a execução do script
    }




    /*
    public function exportacao()
    {
        return Excel::download(new AlunosExport, 'Resultado PCFO Geral.xlsx');
    }

    public function exportacaoescola()
    {
        $escolas = Escola::all();
        //dd($escolas);
        return view('aluno.exportacaoescola', compact('escolas'));
    }

    public function exportacaoescolapost(Request $request)
    {
        // Validar os dados recebidos do formulário
        $request->validate([
            'escola_aluno' => 'required|exists:escolas,id',
        ]);

        // Obter o ID da escola do formulário
        $escolaId = $request->input('escola_aluno');

        // Encontrar a escola com base no ID fornecido
        $escola = Escola::find($escolaId);

        // Verificar se a escola foi encontrada
        if (!$escola) {
            // Se a escola não for encontrada, redirecionar de volta com uma mensagem de erro
            return back()->withErrors(['escola_aluno' => 'Escola não encontrada.']);
        }

        // Iniciar a exportação passando o nome da escola como argumento
        $export = new AlunosPorEscolaExport($escola->nome);

        // Realizar o download do arquivo Excel com os resultados
        return Excel::download($export, 'Resultado PCFO ' . Str::slug($escola->nome) . '.xlsx');
    }
    */
}
