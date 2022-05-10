<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Employee extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    //protected $fillable = ['id'];
    protected $guarded = ['id'];
    //protected $casts = [];
}
