<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomUserController extends Controller
{
    public function index($name,$nickname = null)
    {
        $name = ucfirst($name);
        $nickname = ucfirst($nickname);

        if($nickname) {
            return "Bienvenido {$name}, tu apodo es {$nickname}";
        }else{
            return "Bienvenido {$name}";
        }
    }
}
