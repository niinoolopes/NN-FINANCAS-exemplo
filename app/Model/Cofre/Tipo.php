<?php

namespace App\Model\Cofre;

use Illuminate\Database\Eloquent\Model;
use App\Model\DBTables;

class Tipo extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'COTP_ID';

  public  $timestamps = false;

  // --

  public function __construct() {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->cofreTipo;
  }
}


