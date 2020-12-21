<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\DBQuerys;

class Db extends Controller
{
  private $DBQuerys;
  public function __construct()
  {
    if( !env('DB_QUERY', false) ) die('acesso negado');

    $this->DBQuerys = new DBQuerys;
  }

  public function index()
  {

    $this->DBQuerys->criaTabelas();

    $this->DBQuerys->gravaValoresIniciais();
    
    // $this->DBQuerys->migrarDados();

    die('feito');
  }
}