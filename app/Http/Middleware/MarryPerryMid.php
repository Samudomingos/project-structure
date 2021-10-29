<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Carbon\Carbon;
use App\Support\AtmToken;
use App\Traits\UserUtilsTrait;

class MarryPerryMid
{
    use UserUtilsTrait;

    private function setUserFromIdToken($user_token_id){
        Auth::loginUsingId($user_token_id,true);
  
        return $this; 
    }
    private function setUser($user){
        Auth::login($user);
        return $this; 
    }
    private function checkPayLoad($pay_load){
        if(!$pay_load){ return false;}
        if(!isset($pay_load->remember_token)){ return false;}
        /*Auth
        if(){
            
        }*/
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $expects=['api/login','/logout','api/register', '/csr-xtoken', 'forgotPassword'];
        $curr_path=$request->path();  

        if(in_array($curr_path, $expects)){
            return $next($request);
        }
        $hash = request()->bearerToken();  
        
        if(empty($hash)){
            return new Response(json_encode([
                'status'=>'error',
                'message'=>'Token inválido. Faça o login novamente.',
                'detailedMessage'=>'Erro: 281020210952']), 301);
        }
        
        $now_timestamp= Carbon::now()->timestamp;
        $decrypted="";
        $decrypted=safeDecrypt($hash);

        // dd($decrypted);

        if(is_null($decrypted)){
            return new Response(json_encode([
                'status'=>'error',
                'message'=>'Favor efetuar o login para ter acesso ao conteúdo',
                'detailedMessage'=>'Erro: 281020210953']), 301);
        }

        $is_json=$request->ajax() || $request->wantsJson();
        $returnJson=function(){        
             return new Response(json_encode(
                 ['status'=>'error',
                  'message'=>'Favor efetuar o login para ter acesso ao conteúdo',
                  'detailedMessage'=>'Erro: 281020210954']), 301); 
        };
        $returnView=function(){
            return redirect()->route('/login');
        };
        if(is_null($decrypted)){
            return $is_json? $returnJson() : $returnView();
        }


        $objt= json_decode($decrypted);

        $experes_in=isset($objt->expires_in) ? $objt->expires_in : 0;
        $user_token=isset($objt->token) ? $objt->token : null;
   
        $user_token_id=isset($user_token->id) ? intval($user_token->id) : 0;
        $user = User::find($user_token_id);
        $api_token=new AtmToken(isset($user->remember_token) ? json_decode(safeDecrypt($user->remember_token)) : null);

        // $user_data=$this->setUserFromIdToken($user_token->id)->getUserFromCurrentGuard();
        $user_data=$this->setUser($user)->getUserFromCurrentGuard();
        $user_token_db=new AtmToken(isset($user_data->remember_token) ? json_decode(safeDecrypt($user_data->remember_token)) : null);
        
        if($experes_in<=$now_timestamp){
              return new Response(json_encode([
                  'status'=>'error',
                  'message'=>'Token expirado. Faça o login novamente.',
                  'detailedMessage'=>'Erro: 291020210948']), 419); 
        }
        
        if(is_null($user_data)){
            return new Response(json_encode([
                'status'=>'error',
                'message'=>'Token inválido. Faça o login novamente.',
                'detailedMessage'=>'Erro: 2810202109546']), 301);
            
        }
        if($user_data->id!=$user_token_id){
             return new Response(json_encode([
                 'status'=>'error',
                 'message'=>'Token inválido. Faça o login novamente.',
                 'detailedMessage'=>'Erro: 281020210957']), 301);
        }
        if(!$user_token_db->has()){
            return new Response(json_encode([
                'status'=>'error',
                'message'=>'Token inválido. Faça o login novamente.',
                'detailedMessage'=>'Erro: 281020210958']), 301); 
        }
        if($user_token_db->getExpire()<=$now_timestamp){
            return new Response(json_encode([
                'status'=>'error',
                'message'=>'Token expirado. Faça o login novamente.',
                'detailedMessage'=>'Erro: 281020210959']), 419); 
        }
        if($user_token_db->getTokenStr() != $api_token->getTokenStr()){
            return new Response(json_encode([
                'status'=>'error',
                'message'=>'Token inválido. Faça o login novamente.',
                'detailedMessage'=>'Erro: 281020211000']), 301);
        }
        if( $user_token_db->getIpClient() != get_client_ip()){
            return new Response(json_encode([
                'status'=>'error',
                'message'=>'Token inválido. Faça o login novamente.',
                'detailedMessage'=>'Erro: 281020211001']), 301);
        }
        
        return $next($request);
    }
}
