<?php

namespace App\Model\Financa;

use Illuminate\Database\Eloquent\Model;
use App\Model\DBTables;

class Situacao extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'FNIS_ID';

  public  $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->financaSituacao;
  }
}


