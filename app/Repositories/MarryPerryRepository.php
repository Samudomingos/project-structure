<?php 
namespace App\Repositories;

use App\Repositories\Eloquent\Repository;
use App\Repositories\Criteria\Criteria as Criteria;
use Illuminate\Container\Container as App;
use Illuminate\Support\Collection;
/**
 * Class Repository
 * @package Bosnadev\Repositories\Eloquent
 */
class MarryPerryRepository extends Repository{
    
    public function model(){
       return $this->model_name_space;
   }	 
} 