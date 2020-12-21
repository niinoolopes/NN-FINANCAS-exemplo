<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UsuarioModel;

class Usuario extends Controller
{
  public function __construct()
  {
    header('Access-Control-Allow-Origin: *'); 
    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 0');

    $this->usuario   = new UsuarioModel;
  }

  public function get($id = false)
  {
    if($id){
      $user = UsuarioModel::find($id);
    }else{
      $user = UsuarioModel::all();
    }
    die(json_encode($user));
  }

  public function store(Request $request)
  {
    $data = array_merge(
      $request->all(),
      ["USUA_SENHA" => base64_encode($request->all()['USUA_SENHA'])]
    );

    die(json_encode($data));
  }

  public function update($USUA_ID, Request $request)
  {
    $usuario = $this->usuario->find($USUA_ID);
    
    if($usuario){
      try{
        $dados = $request->all();


        if(!empty($dados['USUA_SENHA'])) 
          $usuario->USUA_SENHA = base64_encode($dados['USUA_SENHA']);


        // update
        $usuario->USUA_NOME  = $dados['USUA_NOME'];
        $usuario->USUA_EMAIL = $dados['USUA_EMAIL'];
        $usuario->save();

        // --
  
        $STATUS = 'success';
        $result = (object)[
          'USUA_ID'    => $USUA_ID,
          'USUA_NOME'  => $dados['USUA_NOME'],
          'USUA_EMAIL' => $dados['USUA_EMAIL'],
        ];
      }
      catch (\Exception $e){

        $STATUS = 'erro';
        $result   = (object) [
          'msg' => 'Erro ao executar Controller/Usuario/update'
        ];
      }

    } else {
      
      $STATUS = 'erro';
      $result   = (object) [
        'msg' => 'o ID não existe.'
      ];
    }

    // --

    return response()->json(['STATUS' => $STATUS, 'data' => $result]);
  }
}

