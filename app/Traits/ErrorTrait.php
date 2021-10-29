<?php

namespace App\Traits;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Log;


trait ErrorTrait
{
   private $error=[];
     
    private function clearError(){
        $this->error=[];
        return $this;
    }
    public function setError($value){
        if(is_array($value)){
            $this->error=$value;
            return $this;
        }
        $this->error[]=$value;
        return $this;
    }
    public function getError(){ return $this->error;}

    public function getErrosFlatted(){
        $erros=$this->getError();
        $str_out="";
       
        foreach ($erros as $k=>$v){
            $sufix=is_array($v) ? implode(' | ', $v) : $v;
            $str_out.=($str_out==""? "": ' | ').$sufix;
        }
        return $str_out;
    }
    public function hasError(){
        $error=$this->getError();
        return count($error)>0;
    }
}
