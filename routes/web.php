<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/db', 'Db@index');


Route::get('/', function () {
    die('Adiciona Modulo Investimento');
});


Route::post('/login', 'Login\Login@store');


Route::middleware('NNToken')->group(function () {
    

    Route::get('/login-remake', 'Login\Login@remake');
    
    Route::get('/busca/{tipo}',         'BuscaController@get');

    Route::prefix('usuario')->group(function() {
        Route::post('/{USUA_ID}',   'Usuario\Usuario@update');
    });

    Route::prefix('cofre')->group(function() {
        // DEFAULTs
        Route::get('/tipo',                        'Cofre\Tipo@get');

        // CRUDs
        Route::get('/carteira',                    'Cofre\Carteira@get');
        Route::post('/carteira',                   'Cofre\Carteira@store');
        Route::post('/carteira/{COCT_ID}',         'Cofre\Carteira@update');
        Route::get('/carteira/delete/{COCT_ID}',   'Cofre\Carteira@delete');

        Route::get('/item',                        'Cofre\Item@get');
        Route::post('/item',                       'Cofre\Item@store');
        Route::post('/item/{COIT_ID}',             'Cofre\Item@update');
        Route::get('/item/delete/{COIT_ID}',       'Cofre\Item@delete');
        
        // --

        Route::get('/busca/{tipo}',                 'Cofre\Busca@get');

    });

    Route::prefix('financa')->group(function() {
        
        // DEFAULTs
        Route::get('/tipo',                        'Financa\Tipo@get');
        Route::get('/situacao',                    'Financa\Situacao@get');

        // CRUDs
        Route::get('/carteira',                    'Financa\Carteira@get');
        Route::post('/carteira',                   'Financa\Carteira@store');
        Route::post('/carteira/{FINC_ID}',         'Financa\Carteira@update');
        Route::get('/carteira/delete/{FINC_ID}',   'Financa\Carteira@delete');

        Route::get('/grupo',                       'Financa\Grupo@get');
        Route::post('/grupo',                      'Financa\Grupo@store');
        Route::post('/grupo/{FIGP_ID}',            'Financa\Grupo@update');
        Route::get('/grupo/delete/{FIGP_ID}',      'Financa\Grupo@delete');
        
        Route::get('/categoria',                   'Financa\Categoria@get');
        Route::post('/categoria',                  'Financa\Categoria@store');
        Route::post('/categoria/{FICT_ID}',        'Financa\Categoria@update');
        Route::get('/categoria/delete/{FICT_ID}',  'Financa\Categoria@delete');
        
        Route::get('/item',                        'Financa\Item@get');
        Route::post('/item',                       'Financa\Item@store');
        Route::post('/item/{FNIT_ID}',             'Financa\Item@update');
        Route::get('/item/delete/{FNIT_ID}',       'Financa\Item@delete');

        Route::get('/lista-fixa',                  'Financa\ListaFixa@get');
        Route::post('/lista-fixa',                 'Financa\ListaFixa@store');
        Route::post('/lista-fixa/{FNLF_ID}',       'Financa\ListaFixa@update');
        Route::get('/lista-fixa/delete/{FNLF_ID}', 'Financa\ListaFixa@delete');

        // --
        
        Route::get('/busca/{tipo}',                 'Financa\Busca@get');

        // CONTINUAR REFATORANDO
        
        Route::get('/lista-fixa/processar-id/{id}',        'Financa\ListaFixa@processaId');
        Route::get('/lista-fixa/processar-lista/{FINC_ID}','Financa\ListaFixa@processaLista');
    });

    Route::prefix('investimento')->group(function() {
        
        // DEFAULTs
        Route::get('/tipo',                 'Investimento\Tipo@get');
        Route::get('/ordem-tipo',           'Investimento\OrdemTipo@get');

        // CRUDs
        Route::get('/carteira',               'Investimento\CarteiraController@get');
        Route::post('/carteira',              'Investimento\CarteiraController@store');
        Route::post('/carteira/{id}',         'Investimento\CarteiraController@update');
        Route::get('/carteira/delete/{id}',   'Investimento\CarteiraController@delete');
        Route::get('/carteira-consolida',     'Investimento\CarteiraController@consolida');
        
        // --
        Route::get('/corretora',              'Investimento\CorretoraController@get');
        Route::post('/corretora',             'Investimento\CorretoraController@store');
        Route::post('/corretora/{id}',        'Investimento\CorretoraController@update');
        Route::get('/corretora/delete/{id}',  'Investimento\CorretoraController@delete');
        // --        
        Route::get('/ativo-tipo',             'Investimento\AtivoTipoController@get');
        Route::post('/ativo-tipo',            'Investimento\AtivoTipoController@store');
        Route::post('/ativo-tipo/{id}',       'Investimento\AtivoTipoController@update');
        Route::get('/ativo-tipo/delete/{id}', 'Investimento\AtivoTipoController@delete');

        Route::get('/rendimento',             'Investimento\AtivoRendimentoController@get');
        Route::post('/rendimento',            'Investimento\AtivoRendimentoController@store');
        Route::post('/rendimento/{id}',       'Investimento\AtivoRendimentoController@update');
        Route::get('/rendimento/delete/{id}', 'Investimento\AtivoRendimentoController@delete');

        Route::get('/cotacao',                'Investimento\AtivoCotacaoController@get');
        Route::post('/cotacao',               'Investimento\AtivoCotacaoController@store');
        Route::post('/cotacao/{id}',          'Investimento\AtivoCotacaoController@update');
        Route::get('/cotacao/delete/{id}',    'Investimento\AtivoCotacaoController@delete');

        Route::get('/ativo-split',            'Investimento\AtivoSplitController@get');
        Route::post('/ativo-split',           'Investimento\AtivoSplitController@store');
        Route::post('/ativo-split/{id}',      'Investimento\AtivoSplitController@update');
        Route::get('/ativo-split/delete/{id}','Investimento\AtivoSplitController@delete');
        // --        
        Route::get('/ativo',                  'Investimento\AtivoController@get');
        Route::post('/ativo',                 'Investimento\AtivoController@store');
        Route::post('/ativo/{id}',            'Investimento\AtivoController@update');
        Route::get('/ativo/delete/{id}',      'Investimento\AtivoController@delete');
        // --        
        Route::get('/ordem',                  'Investimento\OrdemController@get');
        Route::post('/ordem',                 'Investimento\OrdemController@store');
        Route::post('/ordem/{id}',            'Investimento\OrdemController@update');
        Route::get('/ordem/delete/{id}',      'Investimento\OrdemController@delete');
        
        Route::get('/item',                   'Investimento\ItemController@get');
        Route::get('/item/{id}',              'Investimento\ItemController@get');
        Route::post('/item',                  'Investimento\ItemController@store');
        Route::post('/item/{id}',             'Investimento\ItemController@update');
        Route::get('/item/delete/{id}',       'Investimento\ItemController@delete');

        // --
        
        Route::get('/busca/{tipo}',            'Investimento\BuscaController@get');





        
        // Route::get('/ativo/{id}',          'Investimento\Ativo@get');
        // Route::get('/ativo-carteira/{id}', 'Investimento\Ativo@ativoCarteira');
        
        // Route::get('/carteira-geral/{id}',      'Investimento\Carteira@geral');
        // Route::get('/carteira/{id}',        'Investimento\Carteira@get');
        // Route::get('/carteira-composicao/{id}', 'Investimento\Carteira@composicao');
        // Route::get('/carteira-analise/{id}',    'Investimento\Carteira@analise');
        
        
        // Route::get('/ordem',             'Investimento\Ordem@get');
        // Route::get('/ordem/{id}',        'Investimento\Ordem@get');
        // Route::post('/ordem',            'Investimento\Ordem@store');
        // Route::post('/ordem/{id}',       'Investimento\Ordem@update');
        // Route::get('/ordem/delete/{id}', 'Investimento\Ordem@delete');



        // Route::get('/nota',         'Financa\Carteira@get');
        // Route::get('/nota/{id}',    'Financa\Carteira@get');
        // Route::post('/carteira',        'Financa\Carteira@store');
        // Route::put('/carteira/{id}',   'Financa\Carteira@update');
        // Route::post('/carteira/{id}',   'Financa\Carteira@update');
        // Route::delete('/carteira/{id}', 'Financa\Carteira@delete');
        // Route::get('/carteira/delete/{id}', 'Financa\Carteira@delete');
    });

    Route::prefix('relatorio')->group(function() {
        Route::get('/busca/{tipo}', 'Relatorio\BuscaController@get');
    });


});
