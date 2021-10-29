<?php 
namespace App\Repositories\Eloquent;
//https://bosnadev.com/2015/03/07/using-repository-pattern-in-laravel-5/
use App\Repositories\Contracts\CriteriaInterface;
use App\Repositories\Criteria\Criteria as Criteria;

use App\Repositories\Contracts\RepositoryInterface;
//use Bosnadev\Repositories\Exceptions\RepositoryException;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Container\Container as App;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;

use Illuminate\Support\Arr;

/**
 * Class Repository
 * @package Bosnadev\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface, CriteriaInterface  {

    // use \App\Traits\UserUtilsTrait;
    use \App\Traits\ErrorTrait;    
    use \App\Traits\FieldAutofillTrait;
    /**
     * @var App
     */
    private $app;

    /**
     * @var model
     * @var model_name_space
     */
    protected $model;
    protected $model_name_space;
	
    /**
     * @var criteria
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;	
    /**
     *
     * @var type ARRAY
     */
    protected $form_rules=[];
    protected $rules_msg=[];
       

    /**
     * @param App $app
	 * @param Collection $collection
     * @throws \Bosnadev\Repositories\Exceptions\RepositoryException
     */
    public function __construct() {
        //App $app, Collection $collection
        $this->app = App::getInstance();
	$this->criteria =new Collection(); //$collection;
        $this->resetScope();
        $this->makeModel();
    }

    /**
     * Specify Model class name
     * 
     * @return mixed
     */
    abstract function model();
    protected function setRules($values){
        $this->form_rules=$values;
        return $this;
    }
    protected function setRulesMsg($values){
        $this->rules_msg=$values;
        return $this;
    }
    protected function setModelNameSpace($value){
        $this->model_name_space=$value;
        return $this;
    }
    public function getModelNameSpace(){ return $this->model_name_space;}
    public function getModelInstance(){ return $this->model; }
    public function getModelEntity(){
        $m=$this->getModelInstance();
        if($m instanceof Builder){
            return $m->getModel();
        }
        return $m;
    }
    public function getRules(){ return $this->form_rules;}
    public function getRulesMsg(){ return $this->rules_msg;}
    protected function setValidator($rules, $msg){
        $this->setRules($rules)->setRulesMsg($msg);
        return $this;      
    }
    private function validator($form_data, $form_rules=array(), $messages=array(), $customAttributes=array()){
        $validatorClass=app(Factory::class); //make($request->all(), $rules, $messages, $customAttributes);
        $validator = $validatorClass->make($form_data , $form_rules,  $messages, $customAttributes);
        if($validator->fails()){
            $this->setError($validator->errors()->all());
        }
        return $validator;
    }
    /**
     * @param array $columns
     * @return mixed
     */
    public function all($columns = array('*')) {
		$this->applyCriteria();
        return $this->model->get($columns);
    }

    public function allWith($relation_ship, $columns=array('*')){
        $this->applyCriteria();
        return $this->model->with($relation_ship)->get($columns);
    }

    public function structuringRelation($prefix,$all_data=[],$fields=[]){
        
        
        $key = array_keys($fields);
        $value = array_values($fields);
        
        for ($i=0; $i <count($all_data) ; $i++) { 
            for ($j=0; $j <count($fields) ; $j++) { 
                
                $all_data[$i][$value[$j]] = $all_data[$i][$prefix][$key[$j]];
            } 
            unset($all_data[$i][$prefix]);
         }
         
         return $all_data;
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*')) {
		$this->applyCriteria();
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data) {
        
        /**
         * Não esta sendo considerado a possibilidade de usar o terceiro parametro
         */
        
        
        $validator=$this->clearError()->validator($data, $this->getRules(), $this->getRulesMsg());
        
        if($validator->fails()){
            return false;
        } 

        
        // $data['created_at']=null;
        // $data['updated_at']=null;
        // $data['created_by']=null;
        // $data['updated_by']=null;

        return $this->model->create($data);
        // $entity = new ; 
        // $entity = $this->makeModel();
        // dd('hgfhdsbfvhj', $entity);

        $this->makeModel();

        return $this->getModelEntity()->createErp($data);

    }

    public function getKeysName(){
        $keys=$this->getModelEntity()->getKeyName();
        return $keys;
    }
    
    /**
     * 
     * @param array $data
     * @param MIXED|INTEGER $id Pode ser um inteiro ou um array com as chaves sendo o nome do atributo e o valor sendo o valor
     * @param type $attribute
     * @return boolean
     */
    public function update(array $data, $id, $attribute="id") {
                 

        $validator=$this->clearError()->validator($data, $this->getRules(), $this->getRulesMsg());

        // dd($this->getRules());
        
        if($validator->fails()){
            return false;
        }

        // $data = $this->mergeWithAutoFields($data, true);

    
        $keys=$this->getKeysName();
        $keys_primary= is_array($keys) ? $keys : [$keys];
        if(is_array($id)){
            $query = find_mult_in_keys($this->getModelInstance(), $id);
            return $query->update($data);
        }
        // if($attribute=='id'){
        //     $tem = $this->getModelInstance()->find($id);
        //     if(!$tem){
        //         return $this->setError('Registro não encontrado. Erro: 190920191805.');
 
        //     }

        //     return $tem->update($data); 
        // }
        // dd($data,($this->getModelInstance()->getModel()->fillable));
            
        $data=Arr::only($data, array_values($this->getModelInstance()->getModel()->fillable));

        // dd($data);


        $result = $this->getModelEntity()->find($id);
        if($result->count()==0){
            $this->setError("Registro não encontrado. Erro: 190920191844");
            return false;
        }
        $this->makeModel();

        
        return $result->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id) {
       
        $result = (is_array($id)) ? $this->getModelInstance()->whereIn('id',$id) : $this->getModelInstance()->find($id);


        if(empty($result)){
            $this->setError("Registro não localizado . error: 190920191844");
            return false;
        }
        $this->makeModel();
        return $result->delete();
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = array('*')) {
            $this->applyCriteria();    
        return $this->getModelInstance()->find($id, $columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = array('*')) {
		$this->applyCriteria();
        return $this->getModelInstance()->findById($attribute, '=', $value)->first($columns);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws RepositoryException
     */
    public function makeModel() {
        $model = $this->app->make($this->model());
        
        
        /**
        if (!$model instanceof Model){
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }
         * 
         */

        return $this->model = $model->newQuery();
    }
	
	
    /**
     * @return $this
     */
    public function resetScope() {
        $this->skipCriteria(false);
        return $this;
    }	
	
    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true){
        $this->skipCriteria = $status;
        return $this;
    }	
	
    /**
     * @return mixed
     */
    public function getCriteria() {
        return $this->criteria;
    }
	
    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function getByCriteria(Criteria $criteria) {
        $this->model = $criteria->apply($this->getModelInstance(), $this);
        return $this;
    }	
	
    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function pushCriteria(Criteria $criteria) {
        $this->criteria->push($criteria);
        return $this;
    }	
	
    /**
     * @return $this
     */
    public function  applyCriteria() {
        if($this->skipCriteria === true)
            return $this;

        foreach($this->getCriteria() as $criteria) {
            if($criteria instanceof Criteria)
                $this->model = $criteria->apply($this->getModelInstance(), $this);
        }

        return $this;
    }

    
    public function filter($field, $value, $middleSearch = false){
        $valueFilter = ($middleSearch) ? '%'.$value.'%' : $value.'%';
		
        $dadosQuery = $this->getModelInstance();

		if(!is_null($field)){
			if(is_array($field)){
				for ($i=0; $i < count($field) ; $i++) { 
					if($i == 0){
						$dadosQuery->where($field[$i],'like', $valueFilter);
					}
					else{
						$dadosQuery->orWhere($field[$i],'like', $valueFilter);
					}
				}
			}
			else{
				$dadosQuery->where($field,'like', $valueFilter);
			}
		}
		
		$dados = $dadosQuery->get();

		return $dados;
	}

    
}