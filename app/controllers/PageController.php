<?php

namespace App\controllers;

class PageController extends Controller
{
    public function home()
    {
        $this->view('homepage');
    }
}
