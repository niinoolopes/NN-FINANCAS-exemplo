<?php

namespace App;

class Helpers {
    
    public function __construct()
    {
    }


    public function number_decimal($valor)
    {
      return floatval (number_format( $valor, 2 , '.' ,''));
    }

    public function periodo_get($PERIODO)
    {
      if(!$PERIODO) return date('Y-m');

      return $PERIODO;
    }

    public function periodo_getAno($ANO)
    {
      if(!$ANO) return date('Y');

      return (strlen($ANO) != 4) ? substr($ANO, 0 , 4) : $ANO ;
    }

    public function periodo_getMes($MES)
    {
      if(!$MES) return date('m');

      return (strlen($MES) != 4) ? substr($MES, 5, 2) : $MES ;
    }

}
