<?php

namespace App\Model\Financa;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class Integrante extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'FITG_ID';
  
  public $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->financaIntegrante;
  }
}


