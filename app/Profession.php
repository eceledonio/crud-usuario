<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Profession extends Model
{
    protected $fillable = ['title'];
    protected $table = 'profession';

    public function users()
    {
        return$this->hasMany(User::Class);
    }
}

