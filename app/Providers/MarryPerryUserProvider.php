<?php

namespace App\Providers;
use App\User; 
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;


class MarryPerryUserProvider implements UserProvider
{
    protected $fields=['id','email','name','remember_token','password'];
    protected $user_model;
    const SODIUM_CRYPTO_KEY_64='NycjlXSHlwUXA1UjhVb2U3KytkbVUyUVhUcVwvR3hkem1zRkFXUVB3RlVkenNXIi';
    const SODIUM_CRYPTO_KEY_32='eyJpdiI6Ik9yMTVuV3BOdSt2bkdOZ09y';
    
    
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
       
        $qry = User::where('id','=',$identifier);
        
        if($qry->count() >0)
        {   
            $user = $qry->select($this->fields)->first();
            return $user;
        }
        return null;
    }
    
    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
        dd('8574263155555756454');
        $qry = User::where('id_usuario','=',$identifier)->where('remember_token','=',$token);
    
        if($qry->count() >0)
        {
            $user = $qry->select('dj_id', 'dj_name', 'first_name', 'last_name', 'email', 'password')->first();
    
            $attributes = array(
                'id' => $user->dj_id,
                'dj_name' => $user->dj_name,
                'password' => $user->password,
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
            );
    
            return $user;
        }
        return null;
    }
    
    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {   
        $user->setRememberToken($token);
        $user->save();
    }
    
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {  
        $qry = User::where('email','=',$credentials['email'])
                         ->where('status','=','1'); 
    
       return ($qry->count() > 0) ? $qry->select($this->fields)->first() : null;
    }
     
    
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
        public function validateCredentials(Authenticatable $user, array $credentials)
        {              
    
                $plain_password = trim($credentials['password']);
                $db_pssw= is_null($user)? '' : $user->getAuthPassword();
               //$encrypt= safeEncrypt('123456');
                
                if(empty($plain_password) || empty(trim($credentials['email'])) || empty(trim($db_pssw))){
                    return false;
                }
                
                $decrypted_pssw=safeDecrypt($db_pssw);
                
               // dd('5456132123134564', $encrypt, $decrypted_pssw);
                
                if(($user->email != $credentials['email']) || ($plain_password != $decrypted_pssw)){
                    return false;
                }
         //$token = JWTAuth::fromUser($user);
                
           $this->setUserModelInstance($user)->afterLogin();            
                
                return true;
        }
        private function setUserModelInstance(Authenticatable $user){
            $this->user_model=(object)['user'=>$user, 'token'=>$user->setTokenObject()];
            return $this;
        }
        public function getUserModelInstance(){
            return $this->user_model;
        }
       
        private function afterLogin(){
            $this->getUserModelInstance();//->setProfile();//->hasProfile();
        }   
}
