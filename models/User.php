<?php

namespace App\models;

class User extends Model
{
    public $fillables = [
        'username',
        'email',
        'password',
        'token'
    ];

    public function table(): string
    {
        return 'users';
    }
}