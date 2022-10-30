<?php

namespace App\controllers\auth;

use App\controllers\Controller;
use App\core\exceptions\ForbiddenException;
use App\core\middlewares\AuthMiddleware;
use App\core\Request;

class DeleteAccountController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->registerMiddleware(new AuthMiddleware(['show']));
    }

    public function show()
    {
        $this->view('auth/delete-account');
    }

    public function destroy(Request $request)
    {
        $rules = ['email' => ['required']];

        $validated = $request->validate($_POST, $rules, '/delete-account');

        if ($validated['email'] !== $this->app->session->get('user')['email']) {
            throw new ForbiddenException();
        }

        $this->app->builder->delete('users', 'id', $this->app->session->get('user')['id']);

        $this->app->session->remove('user');
        $this->app->session->destroySession();

        $this->app->response->redirect('/');
    }
}
