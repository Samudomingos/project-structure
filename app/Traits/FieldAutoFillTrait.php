<?php

namespace App\Traits;

use Carbon\Carbon;

trait FieldAutoFillTrait{

    function fillCreatedAt($value=''){
        return Carbon::now()->format('Y-m-d H:i:s');
    }
    protected function fillUpdatedAt($value=''){
        return Carbon::now()->format('Y-m-d H:i:s');
    }
    protected function fillDeletedAt($value=''){
        return Carbon::now()->format('Y-m-d H:i:s');
   }
    function fillCreatedAtStr($value=''){
        return Carbon::now()->format('d/m/Y');
    }
    function fillHourStr($value=''){
        return Carbon::now()->format('H:i:s');
    }
    function fillIdUser($value=''){
        return $this->getUserFromCurrentGuard() ? $this->getUserFromCurrentGuard()->id : $value ;
     }
     function fillCreatedBy($value=''){
         $user=$this->getUserFromCurrentGuard();
         return $user ? $user->id : $value;
     }
     protected function fillUpdatedBy($value=''){
        $user=$this->getUserFromCurrentGuard();
        return $user? $user->id : $value;
    }
    function fillDeletedBy($value=''){
        $user=$this->getUserFromCurrentGuard();
        return $user ? $user->id : $value;
    }   
     function fillLogin($value=''){
         $user=$this->getUserFromCurrentGuard();
         return $user? $user->login : $value;
     }

     public function getFormData(){ return $this->form_data;}

     public function fillAutoFieldsInsert(){
          return [
                    'created_by'=>$this->fillCreatedBy(),
                    'created_at'=>$this->fillCreatedAt()
                 ];
     }

     public function fillAutoFieldsUpdate(){
          return [
                    'updated_at'=>$this->fillUpdatedAt(),
                    'updated_by'=>$this->fillUpdatedBy()
                 ];
     }
     public function fillAutoFieldsDelete(){
        return [
                  'updated_at'=>$this->fillDeletedAt(),
                  'updated_by'=>$this->fillDeletedBy()
               ];
    }

     public function fillAutoFields(){
          return  array_merge($this->fillAutoFieldsInsert(), $this->fillAutoFieldsUpdate());
     }

     public function mergeWithAutoFields($fields, $for_update=false){
          
          if($for_update==='all'){
               return array_merge($fields, $this->fillAutoFields());
          }
          return array_merge($fields, $for_update===true ? $this->fillAutoFieldsUpdate() : $this->fillAutoFieldsInsert());
     }

}


?>