<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class TestController extends Controller
{
    public function index(Request $request)
    {
        return 'home';
    }

    public function test($id)
    {
        return $id;
    }
}
