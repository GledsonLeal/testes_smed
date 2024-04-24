<?php

use Illuminate\Support\Facades\Route;
use App\Mail\MensagemTesteMail;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->middleware('verified');

Auth::routes(['verify' => true]);

Route::get('/pagina_inicial', [App\Http\Controllers\HomeController::class, 'pagina_inicial'])
    ->name('nome')
    ->middleware('verified');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('pagina_inicial')
    ->middleware('verified');

Route::middleware('verified')->group(function () {
    Route::resource('aluno', 'App\Http\Controllers\AlunoController');
    Route::resource('formulario', 'App\Http\Controllers\FormularioController');
    //Route::resource('resultado', 'App\Http\Controllers\ResultadoController');
});

Route::get('resultado/exportacao', 'App\Http\Controllers\AlunoController@exportacao')->middleware('verified');
Route::get('resultado/exportacaoescola', 'App\Http\Controllers\AlunoController@exportacaoescola')->middleware('verified');
Route::post('resultado/exportacaoescolapost', 'App\Http\Controllers\AlunoController@exportacaoescolapost')
    ->middleware('verified')
    ->name('aluno.exportacaoescolapost');

Route::get('teste/aritmetica', [App\Http\Controllers\AlunoController::class, 'aritmetica'])
    ->middleware('verified')
    ->name('aluno.aritmetica');

Route::post('aluno/aritmeticapostTeste', [App\Http\Controllers\AlunoController::class, 'aritmeticapostTeste'])
    ->middleware('verified')
    ->name('aluno.aritmeticapostTeste');

Route::get('teste/escrita', 'App\Http\Controllers\AlunoController@escrita')
    ->middleware('verified')
    ->name('aluno.escrita');

Route::post('aluno/escritapostTeste', 'App\Http\Controllers\AlunoController@escritapostTeste')
    ->middleware('verified')
    ->name('aluno.escritapostTeste');

Route::get('teste/leitura', 'App\Http\Controllers\AlunoController@leitura')
    ->middleware('verified')
    ->name('aluno.leitura');

Route::post('aluno/leiturapostTeste', 'App\Http\Controllers\AlunoController@leiturapostTeste')
    ->middleware('verified')
    ->name('aluno.leiturapostTeste');

Route::get('formulario/{aluno}', 'FormularioController@show')
    ->middleware('verified')
    ->name('formulario.show');

Route::post('aluno/buscar', [App\Http\Controllers\AlunoController::class, 'buscar'])
    ->middleware('verified')
    ->name('aluno.buscar');

Route::get('resultado/escrita', 'App\Http\Controllers\ResultadoController@escrita')
    ->middleware('verified')
    ->name('teste.escrita');

Route::get('teste/aritmeticaindividual', 'App\Http\Controllers\AlunoController@aritmeticaindividual')
    ->middleware('verified')
    ->name('teste.aritmeticaindividual');

Route::post('aluno/aritmeticaindividualpost', 'App\Http\Controllers\AlunoController@aritmeticaIndividualPost')
    ->middleware('verified')
    ->name('aluno.aritmeticaindividualpost');

Route::get('teste/leituraindividual', 'App\Http\Controllers\AlunoController@leituraindividual')
    ->middleware('verified')
    ->name('teste.leituraindividual');

Route::post('aluno/leituraindividualpost', 'App\Http\Controllers\AlunoController@leituraIndividualPost')
    ->middleware('verified')
    ->name('aluno.leituraindividualpost');

Route::get('teste/psicogenese', 'App\Http\Controllers\AlunoController@psicogenese')
    ->middleware('verified')
    ->name('teste.psicogenese');

Route::post('aluno/psicogenesepost', 'App\Http\Controllers\AlunoController@psicogenesePost')
    ->middleware('verified')
    ->name('aluno.psicogenesepost');

Route::get('resultado/escolherescola', 'App\Http\Controllers\ResultadoController@escolherEscola')
    ->middleware('verified')
    ->name('resultado.escolherescola');
    
Route::post('resultado/percentual', 'App\Http\Controllers\ResultadoController@percentual')
    ->middleware('verified')
    ->name('resultado.percentual');
/*
Route::get('resultado/escolherescola', 'App\Http\Controllers\ResultadoControllerteste@escolherEscola')
    ->middleware('verified')
    ->name('resultado.escolherescola');
    
Route::post('resultado/percentual', 'App\Http\Controllers\ResultadoControllerteste@percentual')
    ->middleware('verified')
    ->name('resultado.percentual');
*/

// Exemplo de rota com Middleware de verificação
// Route::get('/mensagem-teste', function(){
//     return new MensagemTesteMail();
//     //Mail::to('leitelealgledson@gmail.com')->send(new MensagemTesteMail());
//     //return 'E-mail enviado com sucesso!';
// })->middleware('verified');
