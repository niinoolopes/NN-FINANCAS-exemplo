<?php

namespace App\Model\Investimento;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class OrdemTaxas extends Model {

  private $DBTables;
  
  protected $table;
  
  protected $primaryKey = 'INTX_ID';

  public  $timestamps = false;

  // --

  public function __construct()
  {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestTaxas;
  }
}


