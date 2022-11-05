<?php

namespace App\controllers\auth;

use App\controllers\Controller;
use App\core\middlewares\AuthMiddleware;

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->registerMiddleware(new AuthMiddleware(['dashboard']));
    }

    public function dashboard()
    {
        $this->view('auth/dashboard');
    }
}
