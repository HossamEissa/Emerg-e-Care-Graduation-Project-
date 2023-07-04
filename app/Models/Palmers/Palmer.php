<?php

namespace App\Models\Palmers;

use App\Models\RequestsHistory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Palmer extends Model implements JWTSubject , Authenticatable
{
    use HasFactory  , AuthenticatableTrait , HasApiTokens;
    protected $fillable = [
        'name',
        'email',
        'national_id',
        'phone_number',
        'latitude',
        'longitude',
        'profile_image',
        'government',
        'city',
        'unit_name',
        'car_number',
        'status',
        'password',
    ];
    protected $attributes = [
        'profile_image' => 'default.png',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function requests(){
        return $this->hasMany(RequestsHistory::class ,'palmer_id');
    }


}
