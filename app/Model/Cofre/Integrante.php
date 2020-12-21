<?php

namespace App\Model\Cofre;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Integrante extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'COTG_ID';

  public $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->cofreIntegrante;
  }
  
}


