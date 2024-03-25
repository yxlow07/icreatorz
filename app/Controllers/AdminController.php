<?php

namespace app\Controllers;

use app\Exceptions\MethodNotAllowedException;
use app\Exceptions\UnderWorkException;
use app\Exceptions\UserNotFoundException;
use app\Models\LoginModel;
use app\Models\ProfileModel;
use app\Models\RegisterModel;
use app\Models\UserModel;
use core\App;
use core\Controller;
use core\Exceptions\ViewNotFoundException;
use core\Models\BaseModel;
use core\Models\ValidationModel;
use core\View;

class AdminController extends Controller
{
    public function render(string $view, array $params = []): void
    {
        echo View::make(['/views/admin/'])->renderView($view, $params);
    }

    public function list_users(): void
    {
        $users = (array) App::$app->database->findAll('users');

        $this->render('users', ['users' => $users]);
    }

    public function createUsers(): void
    {
        $model = new RegisterModel(App::$app->request->data());

        if (App::$app->request->isMethod('post')) {
            if ($model->validate() && $model->verifyNoDuplicate() && $model->registerUser()) {
                App::$app->session->setFlashMessage('success', 'Successfully registered user.');
                redirect('/crud_users');
            }
        }

        $this->render('create_users', ['model' => $model]);
    }

    /**
     * @throws ViewNotFoundException
     * @throws UserNotFoundException|MethodNotAllowedException
     */
    public function crud_users($id, $action): void
    {
        $data = (array) LoginModel::getUserFromDB($id, 'id');

        match ($action) {
            BaseModel::READ => '',
            BaseModel::UPDATE => $this->editUser($data),
            BaseModel::DELETE => UserModel::deleteUserFromDB($id),
            default => $data = BaseModel::UNDEFINED,
        };

        if (isset($data[0]) && $data[0] === false) {
            throw new UserNotFoundException();
        }

        if ($data === BaseModel::UNDEFINED) {
            throw new MethodNotAllowedException();
        }

        $this->render('user_profile', ['data' => $data, 'action' => $action]);
    }

    private function editUser($data)
    {
        $model = new ProfileModel($data);

        if (App::$app->request->isMethod('post')) {
            $model = new ProfileModel(App::$app->request->data());

            if ($model->validate() && $model->verifyNoDuplicate($data) && $model->updateDatabase($data)) {
                App::$app->session->setFlashMessage('success', 'Profile Updated Successfully!');
            }
        }

        $this->render('profile', ['model' => $model, 'isAdmin' => true]);
    }

//    public function add_admin()
//    {
//        $model = new AdminModel();
//        if (App::$app->request->isMethod('post')) {
//            $model = new AdminModel(App::$app->request->data());
//
//            if ($model->validate($model->newAdminRules()) && $model->verifyNoDuplicate() && $model->updateDatabase()) {
//                App::$app->session->setFlashMessage('success', 'Admin Created Successfully!');
//                redirect();
//            }
//        }
//
//        $this->render('add_admin', ['model' => $model, 'isAdmin' => true]);
//    }

    public function under_work()
    {
        throw new UnderWorkException();
    }

    public function emit(): void
    {
        $this->render('emit_message');
    }
}