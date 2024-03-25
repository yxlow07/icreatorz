<?php

namespace app\Controllers;

use app\Exceptions\UnderWorkException;
use app\Middleware\AuthMiddleware;
use app\Models\LoginModel;
use app\Models\ProfileModel;
use app\Models\UserModel;
use core\App;
use core\Controller;
use core\Database\CSVDatabase;
use core\Models\ValidationModel;
use core\View;

class UserController extends Controller
{
    public function renderHome(): void
    {
        echo View::make()->renderView('index', ['nav' => App::$app->nav]);
    }

    public static function navItems(): void
    {
        $navItems = [
            'user' => [
                '/profile' => ['fa-user', 'Profile'],
            ],
            'admin' => [
                '/add_admin' => [['fa-user-tie', 'fa-plus'], 'Add Admin', true],
                '/crud_users' => [['fa-users', 'fa-pencil-alt'], 'Manage Users', true],
                '/notify' => ['fa-megaphone', 'Emit Notifications'],
            ],
            'general' => [
                '/' => ['fa-house', 'Home'],
                '/message' => ['fa-message', 'Messages'],
            ],
            'end' => [
                '/logout' => ['fa-person-from-portal', 'Logout'],
            ],
            'guest' => [
                '/login' => ['fa-person-to-door', 'Login'],
                '/register' => ['fa-user-plus', 'Register'],
            ],
        ];


        $nav = [
            ...$navItems['general'],
            ...(AuthMiddleware::execute() ? (App::$app->user instanceof UserModel && \app()->user->type == 'user' ? $navItems['user'] + $navItems['end'] : $navItems['admin']+ $navItems['end']) : $navItems['guest']),
        ];

        App::$app->nav = $nav;
    }

    public function profile(): void
    {
        $model = new ProfileModel(App::$app->request->data());

        if (App::$app->request->isMethod('post')) {
            if ($model->form_type == ProfileModel::UPDATE_PROFILE) {
                $this->handleUpdateProfile($model);
            } else {
                $this->handleUpdatePassword($model);
            }
        }

        echo View::make()->renderView('profile', ['model' => $model]);
    }

    private function handleUpdateProfile(ProfileModel $model): void
    {
        if ($model->validate() && $model->verifyNoDuplicate() && $model->updateDatabase()) {
            App::$app->session->setFlashMessage('success', 'Profile Updated Successfully!');
            LoginModel::setNewUpdatedUserData($model->username, 'username');
        }
    }

    private function handleUpdatePassword(ProfileModel $model): void
    {
        if ($model->validate($model->rulesUpdatePassword()) && $model->checkPassword() && $model->updateDatabasePasswordOnly()) {
            App::$app->session->setFlashMessage('success', 'Password Updated Successfully!');
            LoginModel::setNewUpdatedUserData(App::$app->user->id);
        }
    }

    public function under_work()
    {
        throw new UnderWorkException();
    }

    public function web()
    {
        echo View::make()->renderView('message');
    }
}