<?php

namespace app\Models;

use core\App;
use core\Cookies;
use JetBrains\PhpStorm\NoReturn;

class UserModel
{
    public int $id = 0;
    public string $username = '';
    public string $email = '';
    public string $type = '';
    public string $password = '';
    public string|array|null $info = '';

    public function __construct()
    {
        $this->info = json_decode($this->info, true);
    }

    #[NoReturn]
    public static function deleteUserFromDB(string $id): void
    {
        $res = App::$app->database->delete('users', ['id' => $id]);
        \app()->response::json_response(['success' => $res], true);
    }

    public function setCookies(): void
    {
        $sessionID = App::$app->session->generateSessionID();

        Cookies::setCookie('id', $this->id);
        Cookies::setCookie('sessionID', $sessionID);

        $this->info['sessionID'] = $sessionID;
        App::$app->database->update('users', ['info'], ['info' => json_encode($this->info)], ['id' => $this->id]);
    }

    public function isLogin(): bool
    {
        return !empty($this->id);
    }
}