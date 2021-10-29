<?php

namespace App\Models;

use App\Entities\MarryPerryModel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject as JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends MarryPerryModel implements JWT
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'cpf',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token_object',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    } 

    public function setRememberToken($value){
        $this->remember_token = $value;
        $this->save();
        return $this;
    }

    public function setTokenObject(){
        $expire= Carbon::now()->addMinutes(5)->timestamp;
        $user_fields=[
            'id'=>$this->id,
            'cpf'=>$this->cpf,
            'email'=>$this->email,
            'password'=>$this->password
        ];

        $access_token =safeEncrypt(json_encode(array_merge([
            'expire'=>$expire,
            "time"=>time(),
            "token"=>Str::random(255),
            "ip"=>get_client_ip()
           
        ], $user_fields)));

       
        $user_fields['api_token']=$access_token;
        $this->setRememberToken($access_token);
        $this->token_object=[
            'expires_in'=>$expire,
            'token'=>$user_fields
        ];

        return $this;

    }

    public function getTokenObject(){
        return $this->token_object;
    }

}
