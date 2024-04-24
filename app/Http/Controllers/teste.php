<?php
foreach ($alunoData['leitura'] as $respostaValue) {
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
    $alunosLeitura[] = [
        'id' => $alunoId,
        'leitura' => $respostaValue,
        'percentil' => $percentil,
        'interpretação' => $interpretacao
    ];
}

public function exportacao()
    {
        // Obtenha todos os alunos do banco de dados
        $alunos = Aluno::with('formularios')->get();

        // Crie uma nova planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Adicione cabeçalhos
        $headers = [
            'Código',
            'Nome',
            'Data de Nascimento',
            'Idade',
            'Data do Teste',
            'Escore',
            'Pontuação Padrão',
            'Resultado do Teste',
            'Sexo',
            'Raça',
            'Filiação 1',
            'Filiação 2',
            'Celular Contato',
            'CEP',
            'Endereço',
            'Etapa Aluno',
            'Escola Aluno'
        ];
        $sheet->fromArray([$headers], null, 'A1');

        // Adicione os dados dos alunos
        $row = 2; // Início dos dados na segunda linha
        foreach ($alunos as $aluno) {
            foreach ($aluno->formularios as $formulario) {
                // Calcula a idade do aluno
                $dataNascimento = Carbon::parse($aluno->nascimento);
                $idade = $dataNascimento->age;

                $data = [
                    $aluno->codigo,
                    $aluno->nome,
                    $dataNascimento->format('d/m/Y'),
                    $idade,
                    $formulario->created_at->format('d/m/Y'),
                    $formulario->escore,
                    $formulario->pontuacao_padrao,
                    $formulario->resultado_teste,
                    $aluno->sexo,
                    $aluno->raca,
                    $aluno->filiacao_1,
                    $aluno->filiacao_2,
                    $aluno->celular_contato,
                    $aluno->cep,
                    $aluno->endereco,
                    $aluno->etapa_aluno,
                    $aluno->escola->nome
                ];

                $sheet->fromArray([$data], null, "A$row");

                $row++;
            }
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
        ($escolas);
        return view('aluno.exportacaoescola', compact('escolas'));
    }
    public function exportacaoescolapost(Request $request)
    {
        $escolaId = $request->input('escola_id');
        //dd($escolaId);
        $escola = Escola::find($escolaId);
        //dd($escola);
        if (!$escola) {
            return redirect()->back()->withErrors(['escola_id' => 'Escola não encontrada.']);
        }

        $alunos = Aluno::with('formularios')->where('escola_id', $escolaId)->get();

        // Verificar se não há alunos com testes realizados
        if ($alunos->isEmpty()) {
            return redirect()->back()->withErrors(['nenhum_aluno' => 'Não há alunos com testes realizados para esta escola.']);
        }

        // Crie uma nova planilha
        $spreadsheet = new Spreadsheet();

        // Adicione cabeçalhos
        $headers = [
            'Código',
            'Nome',
            'Data de Nascimento',
            'Idade',
            'Data do Teste',
            'Escore',
            'Pontuação Padrão',
            'Resultado do Teste',
            'Sexo',
            'Raça',
            'Filiação 1',
            'Filiação 2',
            'Celular Contato',
            'CEP',
            'Endereço',
            'Etapa Aluno',
            'Escola Aluno'
        ];
        $spreadsheet->getActiveSheet()->fromArray([$headers], null, 'A1');

        // Adicione os dados dos alunos
        $row = 2; // Início dos dados na segunda linha
        foreach ($alunos as $aluno) {
            foreach ($aluno->formularios as $formulario) {
                // Calcula a idade do aluno
                $dataNascimento = Carbon::parse($aluno->nascimento);
                $idade = $dataNascimento->age;

                $data = [
                    $aluno->codigo,
                    $aluno->nome,
                    $dataNascimento->format('d/m/Y'),
                    $idade,
                    $formulario->created_at->format('d/m/Y'),
                    $formulario->escore,
                    $formulario->pontuacao_padrao,
                    $formulario->resultado_teste,
                    $aluno->sexo,
                    $aluno->raca,
                    $aluno->filiacao_1,
                    $aluno->filiacao_2,
                    $aluno->celular_contato,
                    $aluno->cep,
                    $aluno->endereco,
                    $aluno->etapa_aluno,
                    $aluno->escola->nome
                ];

                $spreadsheet->getActiveSheet()->fromArray([$data], null, "A$row");

                $row++;
            }
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
