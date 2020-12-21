<?php

namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use App\Model\DBTables;

class Tipo extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'INTP_ID';

  public  $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestTipo;
  }
}


