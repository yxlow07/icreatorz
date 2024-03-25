<?php

namespace app\Middleware;

use app\Models\UserModel;
use core\App;
use core\Middleware\BaseMiddleware;

class AdminMiddleware extends BaseMiddleware
{

    public function execute()
    {
        $user = App::$app->session->get('user') ?? null;

        if (isset($_SESSION['user']) && ($user instanceof UserModel) && $user->type == 'admin'):
            return true;
        else:
            App::$app->session->setFlashMessage('error', 'You cannot view because you are not an admin');
            redirect();
        endif;
    }

    public function next()
    {
        // TODO: Implement next() method.
    }
}