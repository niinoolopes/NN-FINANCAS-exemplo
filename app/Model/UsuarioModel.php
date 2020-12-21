<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Model\DBTables;

class UsuarioModel extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'USUA_ID';
  
  public  $timestamps = false;

  // --

  public function __construct()
  {
    $this->DBTables = new DBTables;
    $this->table = $this->DBTables->usuario;
  }
}


