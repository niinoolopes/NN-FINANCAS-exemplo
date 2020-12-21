<?php

namespace App\Http\Controllers\Investimento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\InvestimentoCarteiraModel;
use App\Model\InvestimentoAtivoRendimentoModel;
use App\Model\InvestimentoIntegranteModel;

use App\Rule\Investimento_Carteira_composicao;
use App\Rule\Investimento_Carteira_analiseTipo;

class Carteira extends Controller
{

  private $result;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->carteira   = new InvestimentoCarteiraModel;
  }
  
  public function get($id = false)
  {
    if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);
    
    $this->result = $this->carteira->get($_GET, $id);
    
    try{
      return response()->json(['STATUS'  => 'success','data' => $this->result]);

    }
    catch (\Exception $e){
      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Model']);

    }
  }

  public function store(Request $request)
  {
    $this->integrante = new InvestimentoIntegranteModel;

    try{
      $carteira = $this->carteira;
      $carteira->INCT_DESCRICAO = $request->get('INCT_DESCRICAO');
      $carteira->INCT_PAINEL    = $request->get('INCT_PAINEL');
      $carteira->INCT_STATUS    = $request->get('INCT_STATUS');
      $carteira->save();
      
      $this->integrante->INCT_ID = $carteira->INCT_ID;
      $this->integrante->USUA_ID = $_GET['usuario'];
      $this->integrante->save();

      return response()->json(['STATUS'  => 'success','data' => $this->carteira]);

    }
    catch (\Exception $e){
      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Model']);

    }
  }
  
  public function update($id, Request $request)
  {
    if( !isset($_GET['usuario'])) return response()->json([ 'STATUS' => 'error', 'msg' => 'o id do usuario é obrigatório']);
    
    try{
      $dados = $request->all();
      
    // inativa todas as carteiras
    if( key_exists('INCT_PAINEL', $dados) && $dados['INCT_PAINEL'] == 1){

      $carteiras = $this->carteira->get($_GET);

      foreach ($carteiras as $carteira) {

        $cart = $this->carteira->find($carteira['INCT_ID']);
        $cart->INCT_PAINEL = 0;
        $cart->save();
      }
    }

      $carteira = $this->carteira->find($id);

      $carteira->INCT_DESCRICAO = $request->get('INCT_DESCRICAO');
      $carteira->INCT_PAINEL    = $request->get('INCT_PAINEL');
      $carteira->INCT_STATUS    = $request->get('INCT_STATUS');
      $carteira->save();

      return response()->json(['STATUS'  => 'success','data' => $carteira]);

    }
    catch (\Exception $e){

      return response()->json(['STATUS'  => 'erro', 'msg' => 'Erro ao executar Investimento_Carteira_Model']);

    }
  }
  
  public function delete($id)
  {
    try{
      return response()->json(['STATUS' => 'success','data' => $this->carteira->find($id)->delete()]);
    
    }
    catch (\Exception $e){
      return response()->json(['STATUS' => 'error', 'msg' => 'Erro ao executar Investimento_Carteira_Model']);

    }
  }

  // --

  public function composicao($id) {
    $rule = new Investimento_Carteira_composicao;

    $rule->get               = $_GET;
    $rule->ITEMS             = $this->carteira->composicao($_GET, $id);
    $rule->M_ATIVORENDIMENTO = new InvestimentoAtivoRendimentoModel;

    $this->result = $rule->exec();

    try{
      return response()->json(['STATUS' => 'success','data' => $this->result]);
    
    }
    catch (\Exception $e){
      return response()->json(['STATUS' => 'error', 'msg' => 'Erro ao executar Investimento_Ativo_Model']);

    }
  }

  public function geral($id){
    $this->result = [];
    $get['usuario'] = $_GET['usuario'];
    $get['mes']     = $_GET['mes'];


    // analise TIPO
    $get['tipo'] = 'tipo';
    $arrItems = $this->carteira->analiseTipo($_GET, $id);
    $rule                  = new Investimento_Carteira_analiseTipo;
    $rule->get             = $get;
    $rule->ITEMS           = $arrItems;
    $rule->M_ATIVORENDIMENTO = new InvestimentoAtivoRendimentoModel;
    $this->result['TIPO'] = $rule->exec();


    // analise TIPO ATIVO
    $get['tipo'] = 'tipoAtivo';
    $rule                  = new Investimento_Carteira_analiseTipo;
    $rule->get             = $get;
    $rule->ITEMS           = $arrItems;
    $rule->M_ATIVORENDIMENTO = new InvestimentoAtivoRendimentoModel;
    $this->result['TIPO_ATIVO'] = $rule->exec();


    // analise ATIVO
    $get['tipo'] = 'ativo';
    $rule                  = new Investimento_Carteira_analiseTipo;
    $rule->get             = $get;
    $rule->ITEMS           = $arrItems;
    $rule->M_ATIVORENDIMENTO = new InvestimentoAtivoRendimentoModel;
    $this->result['ATIVO'] = $rule->exec();

    
    try{
      return response()->json(['STATUS' => 'success','data' => $this->result]);
    
    }
    catch (\Exception $e){
      return response()->json(['STATUS' => 'error', 'msg' => 'Erro ao executar Investimento_Ativo_Model']);

    }
  }

  public function analise($id){
    if(!isset($_GET['tipo'])) return response()->json(['STATUS' => 'error', 'msg' => 'O tipo de analise não foi informado']);

    $rule = new Investimento_Carteira_analiseTipo;

    $rule->get             = $_GET;
    $rule->ITEMS           = $this->carteira->analiseTipo($_GET, $id);
    $rule->M_ATIVORENDIMENTO = new InvestimentoAtivoRendimentoModel;
    $rule->exec();
    
    try{
      return response()->json(['STATUS' => 'success','data' => $rule->exec()]);
    
    }
    catch (\Exception $e){
      return response()->json(['STATUS' => 'error', 'msg' => 'Erro ao executar Investimento_Ativo_Model']);

    }
  }
}