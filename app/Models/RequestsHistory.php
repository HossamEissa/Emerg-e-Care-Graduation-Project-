<?php

namespace App\Models;

use App\Models\Palmers\Palmer;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestsHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'palmer_id', 'status' , 'distance'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function palmers()
    {
        return $this->belongsTo(Palmer::class, 'palmer_id', 'id');
    }
}
