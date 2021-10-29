<?php

namespace App\Traits;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait UserUtilsTrait 
{    
    private $is_teste=false;
    private $user_for_teste=1515;
    
    private function parseServers($value){ return explode(',', $value); }
    public function getLocalServers(){ return $this->parseServers(env('LOCAL_SERVERS')); }
    public function getLocalGuard(){ 
        $local_guard=env('LOCAL_GUARD');
        return $local_guard; 
    }    
    public function getDefaultGuard(){
        $default_guard=env('DEFAULT_GUARD');
        return $default_guard;  
    } 
    
    public function getCurrentGuard(){
        $guard_sesssion=session('current_guard');
         $local_guard=env('LOCAL_GUARD', 'api');
        return $local_guard;
    }
    function getUserFromCurrentGuard(){
        $g=$this->getCurrentGuard();
        
      $user=empty($g=$this->getCurrentGuard()) ? null : Auth::guard($g)->user();

       return $user;
    }
    function getCurrServer(){
        
        /*if($this->is_teste==true && $this->serverIsLocal()===true){
           return $this->teste_on ;
        }*/
        if($this->serverIsLocal()===true){
            return 'local';
        }
        elseif ($this->serverIsIntranet()===true) {  
            return 'intranet';
        }
        elseif($this->serverIsExtranet()===true)
        {
           return 'extranet'; 
        }
        return 'servidor_desconhecido';
    }
    // function getServerName(){
        
    //     if(isset($_SERVER['SERVER_ADDR'])){
    //       return  $_SERVER['SERVER_ADDR'];
    //     }
        
    //     if(\App::runningInConsole()===false){
    //        return 'DESCONHECIDO';
    //     }
        
    //     $server_name=gethostname();//system('hostname');
    //     $re = '/^(TI[0-9]{3})$/D';
    //     if($server_name=='intranet'){
    //         return 'intranet';
    //     }
        
    //     if($server_name=='extranet'){
    //         return 'extranet';
    //     }
    //     if(preg_match_all($re, $server_name, $matches, PREG_SET_ORDER, 0)==1){
    //        return 'local';
    //     }
        
    //     $domain_win= isset($_SERVER['USERDNSDOMAIN'])? trim($_SERVER['USERDNSDOMAIN']) :'';
    //     $dominio_name=isset($_SERVER['xxxxxxx']) ? trim($_SERVER['xxxxx']) : '';
        
    //     if($domain_win=='MEUDOMINIO.LOCAL' && strpos($dominio_name, 'spv')!==false){
    //         return 'local';
    //     }
    //     return '';
    // }
    function serverIsLocal(){ return in_array($this->getServerName(), $this->getLocalServers());}
    function isLocaHost(){ return $this->serverIsLocal(); }
    function isApi(){ return $this->getCurrentGuard() == $this->getLocalGuard();}
    function getUserIdForTeste(){ return $this->user_for_teste; }
    function isTestMode(){ return $this->serverIsLocal()===false? false : $this->is_teste; }
    function getGuardByServer(){
        
        return 'local';
        
        $current_server=$this->getCurrServer();
        
        if($current_server=='xxxxx'){
            return 'xxxxxxx';
        }
        
        if($current_server=='local'){
            return $this->getLocalGuard();
        }
        return '';
    } 
  
    
}
