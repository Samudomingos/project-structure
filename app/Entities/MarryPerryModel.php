<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MarryPerryModel extends Authenticatable{

    use \App\Traits\FieldAutoFillTrait;

    public $timestamps = false;
    protected $fillable = [];
    protected $attributes = [];    


    public function setCreatedAtAttribute($value=null){
        $this->attributes['created_at']= $value!=null?$value:$this->fillCreatedAt();
   }

   public function setUpdatedAtAttribute($value=null){
        $this->attributes['updated_at']=$value!=null?$value:$this->fillUpdatedAt();//->format($this->dateFormat);//new DateTime();
   }
   
  public function setCreatedByAttribute($value){
      $this->attributes['created_by']=$this->fillCriadoPor();
  }
  public function setUpdatedByAttribute($value){
          $this->attributes['updated_by']=$this->fillModificadoPor();
  }

}






?>