<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function unauthorized(){
        return $this->retornoJsonDefault(['status'=>'error','message'=>'Não Autorizado'],401);
    }

    public function register(Request $request){

        $array = $this->getArrayRetornoDefault();
        $http_code = 500;

        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'cpf'=>'required|digits:11|unique:users',
            'password'=>'required|same:password_confirm',
            'password_confirm'=>'required'
        ]);

        if(!$validator->fails()){

            $name = $request->input('name');
            $email = $request->input('email');
            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $hash = safeEncrypt($password);

            $new_user = new User();
            $new_user->name=$name;
            $new_user->email=$email;
            $new_user->cpf=$cpf;
            $new_user->password=$hash;
            $new_user->setCreatedAtAttribute();
            $new_user->setUpdatedAtAttribute();
            $new_user->save();


            $token = auth()->login($new_user);

            if(!$token){
                $array['message'] = 'Ocorreu um erro interno';
                $array['detailedMessage'] = 'Erro:281020211605';
                goto saida;
            }

        }else{
            $array['message'] = $validator->errors()->first();
            $array['detailedMessage'] = 'Erro:281020211638';

            goto saida;
        }

        $new_user->setTokenObject();
        $array['message'] = 'Login efetuado com sucesso. Bem Vindo ao sistema!';
        $array['status'] = 'success';
        $http_code = 200;

        saida:
        return $this->retornoJsonDefault($array,$http_code);
        
    }
    
    public function authenticate(Request $request){
        session()->flush();
        $http_code = 500;
        $return = $this->getArrayRetornoDefault();
        
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if(!$validator->fails()){
            $email = $request->input('email');
            $password = $request->input('password');

            $user = User::where(['email'=>$email])->first();

            if(safeDecrypt($user->password)===$password){
                $token = auth()->login($user);
                $data = Auth::guard()->user();
                // dd($a);
                $return['message']='Login Efetuado com Sucesso';
                $return['status']='success';
                $http_code = 200;
                $return['data']=
                [
                    'api_authorizations' => safeEncrypt(json_encode($user->setTokenObject()->getTokenObject())),
                    'user_data'=>$user
                ];
            }

        }else{
            $return['message'] = $validator->errors()->first();
            $return['detailedMessage'] = 'Erro:281020211638';

            goto saida;
        }

        saida:

        return $this->retornoJsonDefault($return,$http_code);

       
    }

    public function list(){

        return 'ddwdadawdwadwdw';
    }
}
