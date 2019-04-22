<?php

class Auth extends cURL
{
    public function Autorisation() {
        $data = [
            'USER_LOGIN' => LOGIN, #Логин (электронная почта)
            'USER_HASH' => HASH #Хэш для доступа к API (смотрите в профиле пользователя)
        ];
        $_response = cURL::request('private/api/auth.php?type=json', $data);
        return $_response['response']['auth'];
    }
}