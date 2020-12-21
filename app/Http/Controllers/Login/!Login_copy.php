<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\FinancaCarteiraModel;
use App\Model\FinancaGrupoModel;
use App\Model\FinancaCategoriaModel;
use App\Model\FinancaItemModel;
use App\Model\FinancaSituacaoModel;
use App\Model\FinancaTipoModel;
use App\Model\UsuarioModel;

use App\Rule\Financa_Item__getMes;
use App\Rule\Financa_Item__getAno;

use App\Helpers;

class Login_copy extends Controller
{
  private $usuario;

  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->usuario   = new UsuarioModel;
    $this->carteira  = new FinancaCarteiraModel;
    $this->grupo     = new FinancaGrupoModel;
    $this->categoria = new FinancaCategoriaModel;
    $this->situacao  = new FinancaSituacaoModel;
    $this->tipo      = new FinancaTipoModel;
    $this->item      = new FinancaItemModel;
    $this->helpers   = new Helpers;
  }

  public function store(Request $request)
  {
    $FINC_ID        = 0;
    $PERIODO        = date('Y-m');
    $carteiras      = [];
    $situacao       = [];
    $tipo           = [];
    $consolidadoMes = $this->consolidadoMes();
    $consolidadoAno = $this->consolidadoAno();

    $MES          = date('Y-m');
    $ANO          = date('Y');

    $result = (object)[
      'STATUS' => 'error',
    ];

    $user = $this->usuario->where(
                            'USUA_EMAIL', $request->get('USUA_EMAIL')
                          )->where(
                            'USUA_SENHA', base64_encode($request->get('USUA_SENHA'))
                          )->first();
    if($user){
      $USUARIO = (object)[
        'USUA_ID'    => $user->USUA_ID,
        'USUA_NOME'  => $user->USUA_NOME,
        'USUA_EMAIL' => $user->USUA_EMAIL,
      ];

      dd($USUARIO);

      $get['usuario'] = $user->USUA_ID;
      $get['status'] = 1;

      $arrCarteira = $this->carteira->get($get);

      if(count($arrCarteira) > 0){

        foreach ($arrCarteira as $carteira) {
          $carteira = (object)$carteira;

          if($carteira->FINC_PAINEL == 1) $FINC_ID = $carteira->FINC_ID;

          $crt = (object) [
            "FINC_ID"        => $carteira->FINC_ID,
            "FINC_DESCRICAO" => $carteira->FINC_DESCRICAO,
            "FINC_STATUS"    => $carteira->FINC_STATUS,
            "FINC_PAINEL"    => $carteira->FINC_PAINEL,
            "FITG_ID"        => $carteira->FITG_ID,
            "USUA_ID"        => $carteira->USUA_ID,
            "GRUPOS"     => [],
          ];

          $grupos = $this->grupo->where('FINC_ID', '=', $carteira->FINC_ID)->get()->toArray();
          
          foreach ($grupos as $grupo) {
            $grupo = (object)$grupo;
            $g = (object) [
              "FIGP_ID"        => $grupo->FIGP_ID,
              "FIGP_DESCRICAO" => $grupo->FIGP_DESCRICAO,
              "FIGP_SLUG"      => $grupo->FIGP_SLUG,
              "FIGP_STATUS"    => $grupo->FIGP_STATUS,
              "FITP_ID"        => $grupo->FITP_ID,
              "FINC_ID"        => $grupo->FINC_ID,
              "CATEGORIAS"     => [],
            ];

            $categorias = $this->categoria->where('FIGP_ID', '=', $grupo->FIGP_ID)->get()->toArray();
              
            foreach ($categorias as $categoria) {
              $categoria = (object)$categoria;
              
              $c = (object) [
                "FICT_ID"         => $categoria->FICT_ID,
                "FICT_DESCRICAO"  => $categoria->FICT_DESCRICAO,
                "FICT_STATUS"     => $categoria->FICT_STATUS,
                "FICT_OBS"        => $categoria->FICT_OBS,
                "FICT_ADD_COFRE"  => $categoria->FICT_ADD_COFRE,
                "FIGP_ID"         => $categoria->FIGP_ID,
              ];

              $g->CATEGORIAS[] = $c;
            }
            
            $crt->GRUPOS[] = $g;
          }
          $carteiras[] = $crt;
        }

        $situacao = $this->situacao->all();

        $tipo = $this->tipo->all();
        

        $rule = new Financa_Item__getMes;
        $rule->FINC_ID    = $FINC_ID;
        $rule->TIPO       = 'consolidado';
        $rule->MES        = $this->helpers->periodo_get( isset($_GET['mes']) ? $_GET['mes'] : false );
        $rule->M_ITEM     = $this->item;
        $rule->M_SITUACAO = $this->situacao;
        $rule->M_TIPO     = $this->tipo;
        $consolidadoMes   = $rule->exec();

            
        $rule = new Financa_Item__getAno;
        $rule->FINC_ID  = $FINC_ID;
        $rule->TIPO     = 'consolidadoPorTipo';
        $rule->ANO      = $this->helpers->periodo_getAno( isset($_GET['mes']) ? $_GET['mes'] : false );
        $rule->ITEM     = $this->item;
        $consolidadoAno = $rule->exec();

          
        $result = (object)[
          'STATUS' => 'success',
          'data'  => (object)[
            'USUARIO'         => $USUARIO,
            'FINC_ID'         => $FINC_ID,
            'PERIODO'         => $PERIODO,
            'CARTEIRA'        => $carteiras,
            'SITUACAO'        => $situacao,
            'TIPO'            => $tipo,
            'CONSOLIDADO_MES' => $consolidadoMes,
            'CONSOLIDADO_ANO' => $consolidadoAno,
            
            'FINANCA'   => (object)[
              'PERIODO'    => $PERIODO,
              'FINC_ID'    => $FINC_ID,
              'CARTEIRA'   => $this->carteira->all(),
              'GRUPO'      => $this->grupo->all(),
              'CATEGORIA'  => $this->categoria->all(),
              'SITUACAO'   => $this->situacao->all(),
              'TIPO'       => $this->tipo->all(),
              'CONSOLIDADO_MES' => $consolidadoMes,
              'CONSOLIDADO_ANO' => $consolidadoAno,

            ],
          ]
        ];

      }else{

        $result = (object)[
          'STATUS' => 'success',
          'data'  => (object)[
            'USUARIO'         => $USUARIO,
            'FINC_ID'         => $FINC_ID,
            'PERIODO'         => $PERIODO,
            'CARTEIRA'        => $carteiras,
            'SITUACAO'        => $situacao,
            'TIPO'            => $tipo,
            'CONSOLIDADO_MES' => $consolidadoMes,
            'CONSOLIDADO_ANO' => $consolidadoAno,

            'FINANCA'   => (object)[
              'FINC_ID'    => $FINC_ID,
              'CARTEIRA'   => $this->carteira->all(),
              'GRUPO'      => $this->grupo->all(),
              'CATEGORIA'  => $this->categoria->all(),
              'SITUACAO'   => $this->situacao->all(),
              'TIPO'       => $this->tipo->all(),
              'CONSOLIDADO_MES' => $consolidadoMes,
              'CONSOLIDADO_ANO' => $consolidadoAno,

            ],

            'INVESTIMENTO'  => (object)[
              // 'CARTEIRA'    => 
            ]

          ]
        ];

      }

    }
    
    die(json_encode($result));
  }
  

  private function consolidadoMes()
  {
    return [
      (object)[ 'name' => 'RECEITA',  'value' => 0 ],
      (object)[ 'name' => 'DESPESA',  'value' => 0 ],
      (object)[ 'name' => 'SOBRA',    'value' => 0 ],
      (object)[ 'name' => 'ESTIMADO', 'value' => 0 ]
    ];
  }

  
  private function consolidadoAno()
  {
    $tmp = [ 'RECEITA' => [], 'DESPESA' => [] ];

    for ($i=1; $i <= 12; $i++) { 
      $tmp['RECEITA'][] = 0;
      $tmp['DESPESA'][] = 0;
    }

    return $tmp;
  }
}

