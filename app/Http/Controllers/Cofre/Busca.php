<?php

namespace App\Http\Controllers\Cofre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Cofre\Busca    as BuscaModel;
use App\Model\Cofre\Item     as ItemModel;
use App\Model\Cofre\Tipo     as TipoModel;
// use App\Model\Cofre\Situacao as SituacaoModel;

use App\Helpers;

class Busca extends Controller
{
  private $busca;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->busca = new BuscaModel;
    $this->item  = new ItemModel;
    $this->tipo  = new TipoModel;
  }


  public function get($tipo) {

    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é obrigatório']);

    // tipo original
    $tipoOriginal = $tipo;

    // tratamento para chamada do metodo
    $tipo   = str_replace('-', ' ',$tipo);
    $tipo   = ucwords($tipo);
    $tipo   = str_replace(' ', '',$tipo);
    $method = "get{$tipo}";


    if( !method_exists($this, $method) ){
      return response()->json(
        [
        'STATUS' => 'error', 
        'msg' => "a busca informada '{$tipoOriginal}' não existe."
        ]
      );
    }
    
    $result = $this->$method();
    try{
      $STATUS = 'success';
    }
    catch (\Exception $e){

      $STATUS = 'erro';
      $result   = (object) [
        'msg' => "Erro ao executar Controller/Cofre/Busca/{$tipoOriginal}"
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  // --

  public function getProposito() {
    if( !isset($_GET['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o COCT_ID é obrigatório']) );
    return $this->busca->proposito($_GET);
  }

  // --

  /*
  public function getInativo() {
    if( !isset($_GET['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o COCT_ID é obrigatório']) );
    $_GET['status'] = 0;
    
    $items = $this->busca->inativo($_GET);

    return [
      'items'       => $items,
      'itemsFiltro' => 'fazer',
    ];
  }

  public function getMesExtrato() {
    if( !isset($_GET['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o COCT_ID é obrigatório']) );
    $_GET['status'] = 1;
    
    $items = $this->busca->mesExtrato($_GET);

    return [
      'items'       => $items,
      'itemsFiltro' => 'fazer',
    ];
  }

  public function getHistorico() {
    if( !isset($_GET['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o COCT_ID é obrigatório']) );
    
    $_GET['limit']   = isset($_GET['limit'])   ? $_GET['limit']   : 30;
    $_GET['dataAte'] = isset($_GET['dataAte']) ? $_GET['dataAte'] : date('Y-m-d');
    $_GET['dataDe']  = isset($_GET['dataDe'])  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'] . '-60 day' ));

    $items = $this->busca->historico($_GET);

    return [
      'items'       => $items,
      'itemsFiltro' => 'fazer',
      'dataDe'      => $_GET['dataDe'],
      'dataAte'     => $_GET['dataAte'],
      'limit'       => $_GET['limit'],
    ];
  }

  public function getMovimentacao() {
    if( !isset($_GET['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o COCT_ID é obrigatório']) );

    $_GET['limit']   = isset($_GET['limit'])   ? $_GET['limit']   : 30;
    $_GET['dataAte'] = isset($_GET['dataAte']) ? $_GET['dataAte'] : date('Y-m-d');
    $_GET['dataDe']  = isset($_GET['dataDe'])  ? $_GET['dataDe']  : date('Y-m-d', strtotime( $_GET['dataAte'] . '-30 day' ));
    
    $_GET['limit'] = isset($_GET['limit']) ? $_GET['limit'] : 60;

    $items = $this->busca->movimentacao($_GET);

    return [
      'items' => $items,
      'itemsFiltro' => 'fazer',
      'dataDe'      => $_GET['dataDe'],
      'dataAte'     => $_GET['dataAte'],
      'limit'       => $_GET['limit'],
    ];
  }
  */
  
  // --

  public function getComposicaoCarteira() {
    if( !isset($_GET['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o COCT_ID é obrigatório']) );
    $_GET['status'] = 1;

    $consolidado = $this->busca->consolidado($_GET);

    $consolidadoGrafico = (object)[
      'labels'     => [],
      'valores'    => [],
      'percentual' => [],
    ];
    $arrResultado = [];


    // APURA TOTAL DA CARTEIRA PARA CALCULAR PERCENTUAL POR PROPOSITO
    $TOTAL         = 0;
    $totalEntrada  = 0;
    $totalRetirada = 0;
    foreach($consolidado as $item) { 
      if($item->COTP_ID == 1) $totalEntrada  += number_format($item->COIT_SOMA, 3, '.', '' ); 
      if($item->COTP_ID == 2) $totalRetirada += number_format($item->COIT_SOMA, 3, '.', '' ); 
      $TOTAL = number_format($totalEntrada - $totalRetirada, 3, '.', '' );
    }
    unset($totalEntrada);
    unset($totalRetirada);


    foreach ($consolidado as $key => $item) {
      if( !key_exists($item->COIT_PROPOSITO, $arrResultado) ) {
        $arrResultado[$item->COIT_PROPOSITO] = (object)[
          'COIT_PROPOSITO'  => $item->COIT_PROPOSITO,
          'COIT_ENTRADA'    => 0,
          'COIT_RETIRADA'   => 0,
          'COIT_SALDO'      => 0,
          'COIT_PERCENTUAL' => 0,
        ];
      }

      // SOMA 'ENTRADA' e 'RETIRADA'
      if($item->COTP_ID == 1) $arrResultado[$item->COIT_PROPOSITO]->COIT_ENTRADA  += $item->COIT_SOMA;
      if($item->COTP_ID == 2) $arrResultado[$item->COIT_PROPOSITO]->COIT_RETIRADA += $item->COIT_SOMA;
      
      // CALCULA 'SALDO'
      $COIT_ENTRADA  = $arrResultado[$item->COIT_PROPOSITO]->COIT_ENTRADA;
      $COIT_RETIRADA = $arrResultado[$item->COIT_PROPOSITO]->COIT_RETIRADA;
      $COIT_SALDO    = number_format(($COIT_ENTRADA-$COIT_RETIRADA), 2, '.', '');
      $arrResultado[$item->COIT_PROPOSITO]->COIT_SALDO = $COIT_SALDO;

      // CALCULA 'PERCENTUAL'
      $COIT_PERCENTUAL = ( $COIT_SALDO > 0 && $TOTAL > 0) ? $COIT_SALDO / $TOTAL : 0;
      $COIT_PERCENTUAL = number_format($COIT_PERCENTUAL, 2, '.', '' );
      $arrResultado[$item->COIT_PROPOSITO]->COIT_PERCENTUAL = "{$COIT_PERCENTUAL}%";

      // REMOVE PROPOSITO QUANDO TIVER VALOR IGUAL A 'ZERADO'
      if($COIT_SALDO == 0) unset($arrResultado[$item->COIT_PROPOSITO]);
    }

    
    // ATRIBUI VALORES AO ARRAY GRAFICO
    foreach($arrResultado as $item) {
      $consolidadoGrafico->labels[]     = $item->COIT_PROPOSITO;
      $consolidadoGrafico->valores[]    = $item->COIT_SALDO;
      $consolidadoGrafico->percentual[] = $item->COIT_PERCENTUAL;
    }

    return [
      'consolidado'        => array_values($arrResultado),
      'consolidadoGrafico' => $consolidadoGrafico,
    ];
  }

  // -- 

  public function getConsolidado() {
    if( !isset($_GET['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o COCT_ID é obrigatório']) );
    $_GET['status'] = 1;

    $items = $this->item->get($_GET);

    // --

    $arrTotal = [
      'ENTRADA' => 0,
      'RETIRADA' => 0
    ];

    foreach ($items as $item) {
      if($item->COTP_ID == 1) $arrTotal['ENTRADA']  += $item->COIT_VALOR;
      if($item->COTP_ID == 2) $arrTotal['RETIRADA'] += $item->COIT_VALOR;
      unset($item);
    }

    $arrTotal['SALDO'] = number_format( $arrTotal['ENTRADA'] - $arrTotal['RETIRADA'] , 2, '.', '');

    return (object) $arrTotal;
  }

  // --

  public function getCarteiraFluxoAno() {
    if( !isset($_GET['COCT_ID'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o COCT_ID é obrigatório']) );
    if( !isset($_GET['periodo'])) die( json_encode(['STATUS' => 'error', 'msg' => 'o periodo é obrigatório']) );
    

    $items = $this->item->get([
      'usuario' => $_GET['usuario'],
      'COCT_ID' => $_GET['COCT_ID'],
      'status'  => 1,
      'dataAno' =>  substr($_GET['periodo'], 0, 4),
    ]);

    // monta Array retornos 'SALDO', 'ENTRADA' e 'Retirada'
    $keys = [];
    foreach (['Saldo', 'Entrada', 'Retirada'] as $key => $text) {
      for ($i=1; $i <= 12; $i++) { 
        $keys[$text][$i] = number_format(0, 2, '.', '');
      }
    }
    
    // apura os valores para cada KEY de acordo com o TIPO
    foreach ($items as $key => $item) {
      $mes = date('n', strtotime($item->COIT_DATA));

      $soma = $keys[$item->COTP_DESCRICAO][$mes] + $item->COIT_VALOR;
      $keys[$item->COTP_DESCRICAO][$mes] = number_format($soma, 2, '.', '');
      
      unset($items[$key]);
    }


    // apura o Saldo de cada Mês
    for ($i=1; $i <= 12; $i++) { 

      $Saldo    = $keys['Saldo'][$i];
      $Entrada  = $keys['Entrada'][$i];
      $Retirada = $keys['Retirada'][$i];

      
      // resgata saldo do mes anterior para o mes atual do loop
      if( $i > 1 ) { 
        $meAnterior = $i - 1;
        $Saldo += $keys['Saldo'][$meAnterior];
      } 

      $Saldo += $Entrada;
      $Saldo -= $Retirada;
      $keys['Saldo'][$i] = number_format($Saldo, 2, '.', '');
    }

    $keys['Retirada'] = array_map(function($val){ return $val != 0 ? "-{$val}" : "0";}, $keys['Retirada']);

    return [
      'SALDO'    => array_values($keys['Saldo']),
      'ENTRADA'  => array_values($keys['Entrada']),
      'RETIRADA' => array_values($keys['Retirada']),
    ];
  }
}