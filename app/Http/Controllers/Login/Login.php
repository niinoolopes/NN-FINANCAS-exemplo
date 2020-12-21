<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\UsuarioModel;
use App\Model\Financa\Carteira       as F_CarteiraModel;
use App\Model\Financa\Grupo          as F_GrupoModel;
use App\Model\Financa\Categoria      as F_CategoriaModel;
use App\Model\Financa\Situacao       as F_SituacaoModel;
use App\Model\Financa\Tipo           as F_TipoModel;
use App\Model\Financa\Item           as F_ItemModel;

use App\Model\Investimento\Carteira  as I_CarteiraModel;
use App\Model\Investimento\Corretora as I_CorretoraModel;
use App\Model\Investimento\Tipo      as I_TipoModel;
use App\Model\Investimento\Ativo     as I_AtivoModel;
use App\Model\Investimento\AtivoTipo as I_AtivoTipoModel;

use App\Model\Cofre\Carteira         as C_CarteiraModel;
use App\Model\Cofre\Item             as C_ItemModel;
use App\Model\Cofre\Tipo             as C_TipoModel;

use App\Http\Controllers\Auth\NNToken;

class Login extends Controller
{
  private $usuario;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->usuario     = new UsuarioModel;
    $this->F_carteira  = new F_CarteiraModel;
    $this->F_grupo     = new F_GrupoModel;
    $this->F_categoria = new F_CategoriaModel;
    $this->F_situacao  = new F_SituacaoModel;
    $this->F_tipo      = new F_TipoModel;
    $this->F_item      = new F_ItemModel;

    $this->I_carteira  = new I_CarteiraModel;
    $this->I_corretora = new I_CorretoraModel;
    $this->I_tipo      = new I_TipoModel;
    $this->I_ativo     = new I_AtivoModel;
    $this->I_ativoTipo = new I_AtivoTipoModel;

    
    $this->C_carteira  = new C_CarteiraModel;
    $this->C_item      = new C_ItemModel;
    $this->C_tipo      = new C_TipoModel;

    // $this->helpers     = new Helpers;
    $this->token     = new NNToken;

  }

  public function store(Request $request)
  {
    $user = $this->usuario->where( 'USUA_EMAIL', $request->get('USUA_EMAIL') )->where( 'USUA_SENHA', base64_encode($request->get('USUA_SENHA')) )->first();
    
    if($user){

      return response()->json(
        $this->buscarDados($user)
      );

    }else{
      return response()->json([
        'STATUS' => 'error',
        'msg'    => 'Email ou Senha não localizados'
      ]);
    }
    
  }

  public function remake() {

    $h = apache_request_headers();

    $str = $h['Authorization'];
    $str = explode('.', $str);

    $payload = $str[1];

    $payload = base64_decode($payload);

    $payload = json_decode($payload);

    // --

    $user = $this->usuario->where('USUA_ID', $payload->USUA_ID )->first();

    return response()->json(
      $this->buscarDados($user)
    );
  }

  private function buscarDados($user) {

    $token = $this->token->Make($user);

    $PERIODO          = isset($_GET['mes']) ? $_GET['mes'] : date('Y-m');
    
    $FINC_ID          = 0;
    $F_arrCarteira    = [];
    $F_arrGrupo       = [];
    $F_arrCategoria   = [];
    $F_arrSituacao    = [];
    $F_arrTipo        = [];

    $INCT_ID          = 0;
    $I_arrCarteira    = [];
    $I_arrCorretora   = [];
    $I_arrTipo        = [];
    $I_arrAtivo       = [];
    $I_arrAtivoTipo   = [];
    $I_arrOrdem       = [];

    // USUARIO
    $USUARIO = (object)[
      'USUA_ID'    => $user->USUA_ID,
      'USUA_NOME'  => $user->USUA_NOME,
      'USUA_EMAIL' => $user->USUA_EMAIL,
    ];
    
    $get['usuario'] = $user->USUA_ID;
    $get['status'] = 1;


    // FINANCAS
    $F_arrCarteira  = $this->F_carteira->get($get);
    $F_arrGrupo     = $this->F_grupo->get($get);
    $F_arrCategoria = $this->F_categoria->get($get);
    $F_arrSituacao  = $this->F_situacao->all();
    $F_arrTipo      = $this->F_tipo->all();

    $FINC_ID        = array_filter($F_arrCarteira, function($c){ return $c->FINC_PAINEL; });
    $FINC_ID        = count($FINC_ID) > 0 ? reset($FINC_ID)->FINC_ID : 0;


    // Investimento
    $I_arrCarteira    = $this->I_carteira->get($get);
    $I_arrCorretora   = $this->I_corretora->get($get);
    $I_arrTipo        = $this->I_tipo->all()->toArray();
    $I_arrAtivo       = $this->I_ativo->get($get);
    $I_arrAtivoTipo   = $this->I_ativoTipo->get($get);

    $INCT_ID          = array_filter($I_arrCarteira, function($c) { return $c->INCT_PAINEL; });
    $INCT_ID          = count($INCT_ID) > 0 ? reset($INCT_ID)->INCT_ID : 0;


    // Investimento
    $C_arrCarteira    = $this->C_carteira->get($get);
    $C_arrtipo        = $this->C_tipo->all()->toArray();

    $COCT_ID          = array_filter($C_arrCarteira, function($c) { return $c->COCT_PAINEL; });
    $COCT_ID          = count($COCT_ID) > 0 ? reset($COCT_ID)->COCT_ID : 0;


    // --

    return [
      'STATUS'  => 'success',
      'data'    => (object)[
        'TOKEN'        => $token,
        'USUARIO'      => $USUARIO,
        'PERIODO'      => $PERIODO,
        
        'FINANCA'   => (object)[
          'FINC_ID'         => $FINC_ID,
          'CARTEIRA'        => $F_arrCarteira,
          'GRUPO'           => $F_arrGrupo,
          'CATEGORIA'       => $F_arrCategoria,
          'SITUACAO'        => $F_arrSituacao,
          'TIPO'            => $F_arrTipo,
        ],
        
        'INVESTIMENTO' => (object)[
          'INCT_ID'    => $INCT_ID,
          'CARTEIRA'   => $I_arrCarteira,
          'CORRETORA'  => $I_arrCorretora,
          'TIPO'       => $I_arrTipo,
          'ATIVO'      => $I_arrAtivo,
          'ATIVO_TIPO' => $I_arrAtivoTipo,
        ],
        
        'COFRE'  => (object)[
          'COCT_ID'    => $COCT_ID,
          'CARTEIRA'   => $C_arrCarteira,
          'TIPO'       => $C_arrtipo,
        ],
      ]
    ];
  }
}

