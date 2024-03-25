<?php

namespace app\Models;

use core\App;
use core\Models\ValidationModel;

class LoginModel extends ValidationModel
{
    public string $username = '';
    public string $password = '';
    public bool $rememberMe = false;
    public array $userData = [];
    
    public function __construct(array $data)
    {
        parent::loadData($data);
    }

    public function rules(): array
    {
        return [
            'username' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 5]],
            'password' => [self::RULE_REQUIRED],
            'rememberMe' => []
        ];
    }

    public function fieldNames(): array
    {
        return [
            'username' => 'Username',
            'password' => 'Password'
        ];
    }

    public function verifyUser(): bool
    {
        /** @var UserModel $user */
        $user = self::getUserFromDB($this->username);

        if (!$user) {
            $this->addError(false, 'username', self::RULE_MATCH, ['match', 'must be a valid existing username']);
            return false;
        }

        if (!password_verify($this->password, $user->password)) {
            $this->addError(false, 'password', self::RULE_MATCH, ['match', 'is incorrect']);
            return false;
        }

        App::$app->user = $user;
        return true;
    }

    public static function getUserFromDB(string $username, $property = 'username'): UserModel|false
    {
        /** @var UserModel|false $user */
        $user = App::$app->database->findOne('users', conditions: [$property => $username], class: UserModel::class);

        return $user;
    }

    public static function setNewUpdatedUserData(string $id, string $property = 'id'): void
    {
        $user = self::getUserFromDB($id, $property);

        App::$app->user = $user;
        App::$app->session->set('user', App::$app->user);
    }

    public function getUserData(): array
    {
        return $this->userData;
    }
}