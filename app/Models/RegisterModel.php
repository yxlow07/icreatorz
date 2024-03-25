<?php

namespace app\Models;

use core\App;
use core\Models\ValidationModel;

class RegisterModel extends ValidationModel
{
    public string $email;
    public string $username;
    public string $password;
    public string $confirm_password;
    public string $type = 'user';

    public function __construct(array $data)
    {
        parent::loadData($data);
    }

    public function rules(): array
    {
        return [
            'username' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 5], [self::RULE_MAX, 'max' => 100]],
            'email' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 10], [self::RULE_MAX, 'max' => 100]],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 5]],
            'confirm_password' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password', 'matchMsg' => 'must match with password']],
        ];
    }

    public function fieldNames(): array
    {
        return [
            'username' => 'Username',
            'email' => 'Email',
            'password' => 'Password',
            'confirm_password' => 'Confirm password',
        ];
    }

    public function registerUser(): bool
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);

        return App::$app->database->insert('users', ['email', 'username', 'type', 'password'], $this);
    }

    public function verifyNoDuplicate(): bool
    {
        $checkEmail = self::checkDatabaseForDuplicates($this->email, 'email');
        $checkUsername = self::checkDatabaseForDuplicates($this->username, 'username');

        if (!$checkEmail) {
            $this->addError(false, 'email', self::RULE_UNIQUE);
        }
        if (!$checkUsername) {
            $this->addError(false, 'username', self::RULE_UNIQUE);
        }

        return $checkEmail && $checkUsername;
    }

    /**
     * @param string $check
     * @param string $property
     * @param string $table
     * @return bool If user exists, then return false
     */
    public static function checkDatabaseForDuplicates(string $check, string $property = 'id', string $table = 'users'): bool
    {
        $user = App::$app->database->findOne($table, [$property => $check], class: UserModel::class);
        if ($user instanceof UserModel) {
            return false;
        } else {
            return true;
        }
    }
}