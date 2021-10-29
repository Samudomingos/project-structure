<?php
namespace App\Support;

/**
 * Description of AtmToken
 *
 * @author Samuel Domingos de Lima
 */
class AtmToken {
    private $time;
    private $expire;
    private $token_str;
    private $token_obj;
    private $ip_client;
    public function __construct($obj) {
        $this->setTime(isset($obj->time)? intval($obj->time) : 0)
             ->setExpire(isset($obj->expire)? intval($obj->expire) : 0)
             ->setTokenStr(isset($obj->token) ?$obj->token  :  '')
             ->setIpClient(isset($obj->ip)? $obj->ip : '')    
             ->setTokenObj($obj);   
        
    }
    public function getTime(){return $this->time;}
    public function getTokenStr(){return $this->token_str;}
    public function getExpire(){return $this->expire;}
    public function getTokenObj(){return $this->token_obj;}
    public function getIpClient(){ return $this->ip_client;}
    private function setTime($value){
        $this->time=$value;
        return $this;
    }
    private function setTokenStr($value){
        $this->token_str=$value;
        return $this;
    }
    private function setExpire($value){
        $this->expire=$value;
        return $this;
    }
    private function setTokenObj($value){        
      $this->token_obj=$value;//empty($value) ? safeDecrypt($value) : null;
        return $this;
    }
    private function setIpClient($value){
        $this->ip_client=$value;
        return $this;
    }
    public function has(){ return is_null($this->getTokenObj()) ? false : true; }

}
