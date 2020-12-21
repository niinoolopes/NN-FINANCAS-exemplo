<?php

namespace App\Http\Controllers\Relatorio;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Financa\Item    as F_ItemModel;
use App\Model\Cofre\Item      as C_ItemModel;
use App\Model\Relatorio\Busca as R_Busca;


class BuscaController extends Controller
{
  private $path;
  private $pathRelatorio;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');
    
    $this->F_item  = new F_ItemModel;
    $this->C_item  = new C_ItemModel;
    
    // url
    $url = explode('/', $_SERVER['SCRIPT_FILENAME']);
    unset($url[count($url) - 1]);
    $url = implode('/',$url);

    $this->path          = $url;
    $this->pathRelatorio = $url . '/storage/app/public/relatorios';
    
    $this->url           = url('/');
    $this->urlRelarotio  = $this->url . '/storage/app/public/relatorios';
    // --

    $this->relatorio    = new R_Busca;
  }

  public function get($tipo) {
    if( !isset($_GET['usuario'])) return response()->json(['STATUS' => 'error', 'msg' => 'o USUARIO é uma parametro obrigatório']);

    // cria pasta relatorio quando não existe
    $this->folderRelatorio();


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
        'msg' => "Erro ao executar Controller/Investimento/Busca/{$tipoOriginal}"
      ];
    }

    return response()->json(['STATUS'  => $STATUS, 'data' => $result]);
  }

  private function getLista() {
    if( !isset($_GET['modulo'])) die(json_encode(['STATUS' => 'error', 'msg' => "o 'modulo' é uma parametro obrigatório"]));

    $USUA_ID     = $_GET['usuario'];
    $strModulo   = $_GET['modulo'];
    $carteira    = $_GET['carteira'];
    $_GET['ano'] = isset($_GET['ano']) ? $_GET['ano'] : date('Y');

    // --

    // set Metodo distinct
    $arrModulo['financa']      = 'distinctFinanca';
    $arrModulo['cofre']        = 'distinctCofre';
    $arrModulo['investimento'] = 'distinctInvestimento';
    $modulotMetodo = $arrModulo[$strModulo];
    
    // get distinctItems
    $options['usuario']  = $USUA_ID;
    $options['carteira'] = $carteira;
    $distinctItems = $this->relatorio->$modulotMetodo($options);


    // itens filtro
    $itemsFiltro = [];
    foreach ($distinctItems as $item) {
      $ano = substr($item->DATA,0 ,4);
      if(!in_array($ano, $itemsFiltro)) $itemsFiltro[] = $ano;
    }
    
    
    // itens do ano em GET
    $distinctItems =  array_filter($distinctItems, function($item){
      $ano = substr($item->DATA,0 ,4);
      return $ano == $_GET['ano'];
    });


    // ordena por ordem crescente
    $distinctItems = array_reverse($distinctItems);


    // VALIDA CADA MES DO ANO
    foreach ($distinctItems as $key => $item) {
      $item_data = $item->DATA;

      $tmp = $this->help_LabelPeriodoFile($item_data);
      $LABEL        = $tmp['LABEL'];
      $PERIODO      = $tmp['PERIODO'];
      $PERIODO_FILE = $tmp['PERIODO_FILE'];


      $relatorioExist = $this->relatorioExist($strModulo, $carteira, $USUA_ID, $PERIODO_FILE) ? true : false;
      $url            = $relatorioExist ? $this->urlRelatorio($strModulo, $carteira, $USUA_ID, $PERIODO_FILE) : '';

      $distinctItems[$key] = [
        'LABEL'           => $LABEL,
        'URL'             => $url,
        'PERIODO'         => $PERIODO,
        'RELATORIO'       => $relatorioExist,
      ];
    }

    // ADICIONA RELATORIO ANO
    $LABEL        = $_GET['ano'];
    $PERIODO      = $_GET['ano'];
    $PERIODO_FILE = $_GET['ano'];
    
    $relatorioExist = $this->relatorioExist($strModulo, $carteira, $USUA_ID, $PERIODO_FILE) ? true : false;
    $url            = $relatorioExist ? $this->urlRelatorio($strModulo, $carteira, $USUA_ID, $PERIODO_FILE) : '';
    
    if(count($distinctItems)){
      array_unshift($distinctItems, [
        'LABEL'           => $LABEL,
        'URL'             => $relatorioExist ? $this->urlRelatorio($strModulo, $carteira, $USUA_ID, $PERIODO_FILE) : '',
        'PERIODO'         => $PERIODO,
        'RELATORIO'       => $relatorioExist,
      ]);
      // ADICIONA RELATORIO ANO - FIM
    }


    return [
      'ano'         => $_GET['ano'],
      'modulo'      => $_GET['modulo'],
      'items'       => $distinctItems,
      'itemsFiltro' => $itemsFiltro,
    ];
  }
  
  public function getGerarRelatorio() {
    if( !isset($_GET['modulo'])) die(json_encode(['STATUS' => 'error', 'msg' => "o 'modulo' é uma parametro obrigatório"]));

    $PERIODO   = $_GET['data'];
    $strModulo = $_GET['modulo'];
    $USUA_ID   = $_GET['usuario'];
    $carteira  = $_GET['carteira'];

    // --

    $options['usuario'] = $_GET['usuario'];
    $options['mes']     = $_GET['data'];
    $options['status']  = 1;
    if( $strModulo == 'financa') $items = $this->F_item->get($options);
    if( $strModulo == 'cofre')   $items = $this->C_item->get($options);

 
    // retorna quando não existir registros para o PERIODO informado
    if( count($items) == 0) die( json_encode(['STATUS' => 'error', 'msg' => 'Não existe registos para esse mês.']) );
    
    
    try{
      $tmp = $this->help_LabelPeriodoFile($PERIODO);
      $LABEL        = $tmp['LABEL'];
      $PERIODO      = $tmp['PERIODO'];
      $PERIODO_FILE = $tmp['PERIODO_FILE'];

      // FILE
      $filaName = $this->pathRelatorio($strModulo, $carteira, $USUA_ID, $PERIODO_FILE);
  
      $output = fopen($filaName, 'w');

      fputcsv(
        $output, 
        $this->utf8_map( array_keys(( array)reset($items) ) ),
        ";"
      );
  
      foreach ($items as $dados) {
        $dados = (array)$dados;
        fputcsv(
          $output, 
          $this->utf8_map($dados),
          ";"
        );
      }
      
      fclose($output);
      // FILE - FIM

      
      $relatorioExist = $this->relatorioExist($strModulo, $carteira, $USUA_ID, $PERIODO_FILE) ? true : false;
      $url            = $relatorioExist ? $this->urlRelatorio($strModulo, $carteira, $USUA_ID, $PERIODO_FILE) : '';

      return [
        'LABEL'           => $LABEL,
        'URL'             => $url,
        'PERIODO'         => $PERIODO,
        'RELATORIO'       => $relatorioExist,
      ];
    }
    catch (\Exception $e){
      die( json_encode(['STATUS' => 'error', 'msg' => 'Erro ao gerar Relatório.']) );
    }
  }

  // aux

  private function urlRelatorio($MODULO, $carteira, $USUA, $DATA) {
    return "{$this->urlRelarotio}/usuario_{$USUA}--carteira_{$carteira}--relatorio_{$MODULO}--{$DATA}.csv";
  }
  
  private function pathRelatorio($MODULO, $carteira, $USUA, $DATA) {
    return "{$this->pathRelatorio}/usuario_{$USUA}--carteira_{$carteira}--relatorio_{$MODULO}--{$DATA}.csv";
  }

  private function relatorioExist($MODULO, $carteira, $USUA, $DATA) {
    $pathFile = $this->pathRelatorio($MODULO, $carteira, $USUA, $DATA);
    return file_exists($pathFile);
  }

  private function folderRelatorio() {
    // valida se pasta existe
    if( !is_dir($this->pathRelatorio) ){

      // se não existir, cria pasta uploads
      chmod($this->path, 0777);
      mkdir($this->pathRelatorio, 0777, true);
    }
  }

  private function utf8_map($dados) {
    return array_map( function($value){ return utf8_decode($value); }, $dados);
  }

  private function help_LabelPeriodoFile($PERIODO) {
    $result = [];

    if(strlen($PERIODO) == 7) {
      $ano       = substr($PERIODO,0 ,4);
      $mes       = substr($PERIODO,5 ,2);

      $result['LABEL']        = "{$mes}-{$ano}";
      $result['PERIODO']      = "{$ano}-{$mes}";
      $result['PERIODO_FILE'] = "{$ano}_{$mes}";
    }

    if(strlen($PERIODO) == 4){

      $result['LABEL']        = $PERIODO;
      $result['PERIODO']      = $PERIODO;
      $result['PERIODO_FILE'] = $PERIODO;
    }
    return $result;
  }
}

