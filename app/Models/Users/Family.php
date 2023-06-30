<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'kinship', 'phone_number', 'user_id',
    ];

    public function personBelong()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
