<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getRepository(){ return $this->repository; }    
    public function getModel(){ return $this->model_instance; }
    public function getModelCaminho(){return $this->model_caminho;}
    public function getFormRules(){ return $this->form_rules;}
    public function getRulesMsg(){ return $this->rule_msg;}
    protected function setFormRules($value){
        $this->form_rules=$value;
        return $this;
    }
    protected function setRulesMsg($value){
        $this->rule_msg=$value;
        return $this;                
    }
    protected function setModel($model_caminho){
        $this->model_instance=new $model_caminho();
        $this->model_caminho=$model_caminho;
        return $this;
    }
    protected function setRepository(\App\Repositories\Eloquent\Repository $repository){
        $this->repository=$repository;
        return $this; 
    }
    public function hasRepository(){ return empty($this->repository)===false;}
    public function hasModel(){ return empty($this->model_instance)===false;}
    protected function getRepositoryOrModel(){ return $this->hasRepository()? $this->getRepository() : $this->getModel(); }
    
     /**
     *@param String $tipo r=repository, m=model
     */
    public function getKeysName($tipo="r"){
        return $tipo=='r'? $this->getKeysNameRepository() : $this->getKeysModel();
    }
    public function getKeysModel($model_instance=null){
        $model=!$model_instance? $this->getModel() : $model_instance;
        return !$model? null : $model->getKeyName();
    }
    public function getKeysNameRepository($reposity=null){
        $repo=!$reposity ? $this->getRepository(): $reposity;
        return $this->getKeysModel($repo->getModelEntity());
    }
    public function keysCombine($data){
        $keys= is_array($kn=$this->getKeysName())?$kn : [$kn] ;
        $retorno=[];
        $num_key=[];
        foreach($keys as $k=>$key_name){
                if(!isset($data[$key_name])){
                    continue;
                }
            $retorno[$key_name]=$data[$key_name];
        }
        
        return $retorno;
        /**
        if(count($retorno)==count($keys)){
            return $retorno;
        }  
         */      
    }
    
    
    //InstiRepository $repository
    public function __construct(){ 
        if(empty($this->repository)==false){
            $this->setModel($this->getRepository()->getModelNameSpace());
        }
    }
    
    public function buscar(){
        
    }
    
    public function listar(){
       return new Response(json_encode($this->getRepositoryOrModel()->all()), 200);
    }
   
    public function insert(Request $request){
        $retorno=$this->getArrayRetornoDefault();
        $form_data=$request->all();

       
        if(! $this->getRepositoryOrModel()->create($form_data)){
            $retorno['message']=$this->getRepositoryOrModel()->getErrosFlatted();
            $retorno['detailedMessage']='Falha ao inserir o registro. Erro: 220820191723';
            goto saida;
        }
        
        $retorno["status"]="success";
        $retorno["message"]="Cadastro realizado com sucesso!";
        $retorno['type']="success";

        
        saida:
         return $this->retornoJsonDefault($retorno);
    }

    public function update(Request $request){
        $retorno=$this->getArrayRetornoDefault();
        $form_data=$request->all();      
   
        $id=$form_data['id'];  
        
        if(! $this->getRepositoryOrModel()->update($form_data, $id)){
                $retorno['message']=$this->getRepositoryOrModel()->getErrosFlatted();
                $retorno['detailedMessage']='Falha ao atualizar o registro. Erro: 260820191038.';
                goto saida;
            }
        
        $retorno["status"]="success";
        $retorno["message"]="Registro atualizado com sucesso!";
        $retorno['type']="success";

        saida:
        return $this->retornoJsonDefault($retorno);
    }

    public function delete(Request $request){
        $retorno=$this->getArrayRetornoDefault();
        $form_data=$request->all(); 
         
        if(!isset($form_data['id'])){
             $retorno['message']='Registro não encontrado';
             $retorno['detailedMessage']='Falha ao deletar o registro. Erro: 190920191839';
             goto saida;
        }
 
        if(!$this->getRepositoryOrModel()->delete($form_data['id'])){
             $retorno['message']= $this->getRepositoryOrModel()->getErrosFlatted();
             $retorno['detailedMessage']='Atualiza a página e tente novamente. Erro: 2608201910385.';
             goto saida;
         }
         
         $retorno["status"]="success";
         $retorno["message"]="Registro excluído com sucesso!";
         $retorno['type']="success";
         saida:
         return $this->retornoJsonDefault($retorno);
     }

        
    
    public function getGuardByServer(){
        
    }
     public function getHttpCodeByStatus($array_retorno){
        if(!isset($array_retorno['status'])){
            return 500;
        }
        return $array_retorno['status']=='success' ? 200: 500;
    }
     public function retornoJsonDefault($retorno, $http_code_force=0){
        $front_return=[]; 
        if($retorno['status']=='success' || $retorno['status']=='information'){
            $front_return=$retorno;
        }
        else{
            $front_return = $retorno;
        }


         return new Response(json_encode($front_return), $http_code_force>0 ? $http_code_force : $this->getHttpCodeByStatus($retorno)); 
    }
    public function getArrayRetornoDefault(){ return ['status'=>'error', 'message'=>'', 'detailedMessage'=>'']; }
    
    public function filter(Request $request){
        $data_request=$request->all(); 

        $field = $data_request['field'];
        $value = $data_request['value'];
        $middleSearch = array_key_exists ('middleSearch',$data_request) ? $data_request['middleSearch'] : false;

        return $this->repository->filter($field, $value, $middleSearch);
    }
}
