<!DOCTYPE>
<html>
<body>
<?php
require('config.php');
class cURL {
    public $link;
    public $method;
    public $data;

    public function curl1($link, $method, $data)
    { //Функция курл запроса, принимает ссылку, метод
        $curl = curl_init();
        #Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $out = json_decode(curl_exec($curl), TRUE); #Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = [
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable'
        ];
        try {
            #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
            if ($code != 200 && $code != 204)
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        } catch (Exception $E) {
            die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
        }
        return $out;
        curl_close($curl); //Закрываем курл
    }
}
class AddElements extends cURL{
    public $num;
    public $cut_step;
    public $ElemArray = [];
    public $IdArray = [];
    public $name;
    public  $link;
    private $bool = TRUE;
    public function createElem($name,  $cut_step) {
        for ($i = 1; $i <= $this->num; $i++) {                    // Заполняем массив на N элементов значениями КОНТАКТОВ
            $this->ElemArray['add'][] = [
                'name' => $name . $i
            ];
        }
        foreach(array_chunk( $this->ElemArray['add'], $cut_step, $this->bool) as $cutArray) {//Делим массив по 200 элементов и отправляем cURL
            // Отправляем cURL по 200 сущностей
            $contact200_curl = [];
            $contact200_curl['add'] = $cutArray;
            $cont = new cURL();
            $cont->link = SUBDOMAIN.$this->link;
            $cont->method = 'POST';
            $cont->data = $contact200_curl;
            $result = $cont->curl1($cont->link, $cont->method, $cont->data);
            foreach($result['_embedded']['items'] as $item) {    // Сохраняем id контактов для последующего создания связей
                $this->IdArray[] = $item['id'];
            }
        }
        return $this->IdArray;

    }
}
//попробуем сделать авторизация в ООП
$auth = new cURL;
$auth->link = SUBDOMAIN.'private/api/auth.php?type=json';
$auth->method = 'POST';
$auth->data = [
    'USER_LOGIN' => LOGIN, #Логин (электронная почта)
    'USER_HASH' => HASH #Хэш для доступа к API (смотрите в профиле пользователя)
];
$auth->curl1($auth->link, $auth->method, $auth->data);



function curl($link, $method, $data)
{ //Функция курл запроса, принимает ссылку, метод
    $curl = curl_init();
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    $out = json_decode(curl_exec($curl), TRUE); #Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code = (int)$code;
    $errors = [
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable'
    ];
    try {
        #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
        if ($code != 200 && $code != 204)
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
    } catch (Exception $E) {
        die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode());
    }
    return $out;
    curl_close($curl); //Закрываем курл
}
 #Сохраняем дескриптор сеанса cURL

$func = $_GET["func"];// Получаем номер среагировавшей формы
switch ($func) {
    case 1:
                      //получаем данные из формы
        $n = $_POST['number'];
        if(create_n_el($n)) {             //запускаем функцию и проверяем
            echo 'Было добавлено '.$n.' контактов, сделок, покупателей и компаний';
            echo '</br>';
            echo 'Был создан мультисписок и всем контактам назначено рандомок количество значений';
            echo '</br>';
        };
        break;
    case 2:
        break;
    case 3:
        break;
    case 4:
        break;
    case 5:
        break;
}
                      //надо узнать сколько раз отправляются запросы курл



function create_n_el($num) {                                // Функция создания контактов, компаний, сделок и покупателей

    $contacts = new AddElements();
    $contacts->name = 'Contacts';
    $contacts->cut_step = 200;
    $contacts->num = $num;
    $contacts->link = 'api/v2/contacts';
    $ids_contacts = $contacts->createElem($contacts->name, $contacts->cut_step, $contacts->link);
    //Создаем  массивы с данными


    for ($i = 1; $i <= $num; $i++) {                     // Заполняем массив на N элементов значениями КОМПАНИЙ
        $companies['add'][] = [
            'name' => 'Company ' . $i,
            'contacts_id' => $ids_contacts[$i-1],     // Привязываем к каждой компании контакт по id
        ];
    }
    $ids_companies = [];                                 // Массив для хранения id компаний
    foreach(array_chunk($companies['add'], 200, TRUE) as $company200) {//Делим массив по 200 элементов и отправляем cURL
        // Отправляем cURL по 200 сущностей
        $company200_curl = [];
        $company200_curl['add'] = $company200;
        $link = 'https://testPolinaSvet.amocrm.ru/api/v2/companies';
        $result = curl($link, 'POST', $company200_curl);
        foreach($result['_embedded']['items'] as $item) {  // Сохраняем id компаний для последующего создания связей
            $ids_companies[] = $item['id'];
        }
        unset($result);
    }

    for ($i = 1; $i <= $num; $i++) {                      // Заполняем массив на N элементов значениями СДЕЛОК
        $leads['add'][] = [
            'name' => 'Lead ' . $i,
            'contacts_id' => $ids_contacts[0][$i-1],      // Привязываем к каждой сделке контакт по id
            'company_id'  => $ids_companies[0][$i-1]      // Привязываем к каждой сделке компанию по id
        ];
    }
    $ids_leads = [];                                     // Массив для хранения id сделок
    foreach(array_chunk($leads['add'], 200, TRUE) as $lead200) {//Делим массив по 200 элементов и отправляем cURL
        // Отправляем cURL по 200 сущностей
        $lead200_curl = [];
        $lead200_curl['add'] = $lead200;
        $link = 'https://testPolinaSvet.amocrm.ru/api/v2/leads';
        $result = curl($link, 'POST', $lead200_curl);
        foreach($result['_embedded']['items'] as $item) {  // Сохраняем id сделок для последующего создания связей
            $ids_leads[] = $item['id'];
        }
        unset($result);
    }

    for ($i = 1; $i <= $num; $i++) {                       // Заполняем массив на N элементов значениями ПОКУПАТЕЛЕЙ
        $customers['add'][] = [
            'name' => 'Customer ' . $i,
            'company_id'  => $ids_companies[0][$i-1],      // Привязываем к каждому покупателю компанию
            'next_date' => strtotime("now"),          // Обязательный параметр,
            'contacts_id' => array_rand($ids_contacts, 1)
        ];
    }
    $ids_customers = [];                                 // Массив для хранения id покупателей
    foreach(array_chunk($customers['add'], 200, TRUE) as $customer200) {//Делим массив по 200 элементов и отправляем cURL
        // Отправляем cURL по 200 сущностей
        $customer200_curl = [];
        $customer200_curl['add'] = $customer200;
        $link = 'https://testPolinaSvet.amocrm.ru/api/v2/customers';
        $result = curl($link, 'POST', $customer200_curl);
        foreach($result['_embedded']['items'] as $item) {
            $ids_customers[] = $item['id'];
        }
        unset($result);
    }

    $fields['add'][] = [                                       // Cоздаем мультисписок
        'name' => "Выбор номера:",
        'field_type' =>  5,
        'element_type' => 1,
        'origin' => "123456789_Polina_Svet",
        'is_editable' => "1", // Значение: 1 - можно редактировать, 0 - нельзя
        'enums' => [
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "10"
        ]
    ];
    $link = 'https://testPolinaSvet.amocrm.ru/api/v2/fields';
    $result = curl($link, 'POST', $fields);
    $id_field = $result['_embedded']['items'][0]['id'];
    unset($result);

    //добавляем рандомные значения мультисписка к контактам
    foreach(array_chunk($ids_contacts, 20, TRUE) as $contact200) {
        $up_contacts = [];
        $n = 0;
        foreach ($contact200 as $contact) {
            $up_contacts ['update'][] = [
                'id' => $contact,
                'updated_at' => strtotime("now"),
                'custom_fields' => [
                    [
                        'id' => $id_field,
                        'values' => [
                            [
                                'enum' => mt_rand(1,10)
                            ],
                        ]
                    ]
                ]
            ];
            $how_much = mt_rand(1,10);                // Количество значений мультисписка, которые будут отмечены
            for($i = 0; $i < $how_much; $i++) {                       //будем добавлять поля в массив
                array_push($up_contacts ['update'][$n]['custom_fields'][0]['values'], ['enum' => mt_rand(1,10)]);
            }
            $n++;
        }
        $link='https://testPolinaSvet.amocrm.ru/api/v2/contacts';
        curl($link, 'POST', $up_contacts);

    }
    return TRUE;
};

?>
<a href="/"><button>Вернуться на главную</button></a>
</body>
</html>
