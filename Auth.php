<?php
/**
 * Created by PhpStorm.
 * User: psvet
 * Date: 18.04.2019
 * Time: 12:54
 */

class Auth extends cURL
{
    public function Autorisation() {
        $this->_link = SUBDOMAIN.'private/api/auth.php?type=json';
        $this->_method = 'POST';
        $this->_data = [
            'USER_LOGIN' => LOGIN, #Логин (электронная почта)
            'USER_HASH' => HASH #Хэш для доступа к API (смотрите в профиле пользователя)
        ];
        $_response = $this->request();
        return $_response['response']['auth'];
    }
}