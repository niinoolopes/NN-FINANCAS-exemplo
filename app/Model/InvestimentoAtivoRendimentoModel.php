<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\DBTables;

class InvestimentoAtivoRendimentoModel extends Model {

  private $DBTables;

  protected $table;
  
  protected $primaryKey = 'INAR_ID';

  public  $timestamps = false;
  
  public function __construct()
  {
    $this->DBTables = new DBTables;
    $this->table    = $this->DBTables->InvestAtivoRendimento;
  }

  public function get($get, $id = false)
  {
    // $corretora = $this;
    
  //   $corretora = $corretora->select('*');

  //   if(isset($get['status'])) $corretora = $corretora->where('INCR_STATUS', $get['status']);

  //   $corretora = ($id) ? $corretora->find($id)->toArray() : $corretora->get()->toArray();
    
  //   return $corretora;
  }
  
  public function rendimentoPorAtivoCorretora($get, $INCR, $INAV){

    $query  = "SELECT ";
    $query .= "INAR_TIPO, INAV_ID, INCR_ID, INAR_VALOR, DATE_FORMAT(INAR_DATA,'%Y-%m') INAR_DATA ";
    $query .= "FROM {$this->DBTables->InvestAtivoRendimento} ";
    $query .= "WHERE INAR_STATUS = 1 AND ( INCR_ID = {$INCR} AND INAV_ID = {$INAV} ) ";
    $query .= "ORDER BY INAR_DATA ";

    // die($query);
    return DB::select($query);
  }

}