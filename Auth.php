<?php
/**
 * User: Polina Svet
 * Класс для создания запроса на авторизацию и отправка его
 */

class Auth extends cURL
{
    /**
     * @return mixed - возвращает флаг авторизации true/false
     */
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