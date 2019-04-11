<!DOCTYPE>
<html>
<body>
<?php
require('config.php');
// Тут сразу нужно авторизироваться
 #Сохраняем дескриптор сеанса cURL
$user = [
    'USER_LOGIN' => LOGIN, #Логин (электронная почта)
    'USER_HASH' => HASH #Хэш для доступа к API (смотрите в профиле пользователя)
];
$link = 'https://testPolinaSvet.amocrm.ru/private/api/auth.php?type=json';
curl($link, 'POST', $user);
$func = $_GET["func"];// Получаем номер среагировавшей формы
switch ($func) {
    case 1:
        $n = $_POST["number"];              //получаем данные из формы

        if(create_n_el($n)) {             //запускаем функцию и проверяем
            echo 'Было добавлено '.$n.' контактов, сделок, покупателей и компаний';
            echo '</br>';
            echo 'Был создан мультисписок и всем контактам назначено рандомок количество значений';
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
function curl($link, $method, $data) { //Функция курл запроса, принимает ссылку, метод
    $curl = curl_init();
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($data));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out = json_decode(curl_exec($curl),TRUE); #Инициируем запрос к API и сохраняем ответ в переменную
    $code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code =(int)$code;
    $errors = [
        301 => 'Moved permanently',
        400 => 'Bad request',
        401 =>'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not found',
        500 => 'Internal server error',
        502 => 'Bad gateway',
        503 => 'Service unavailable'
    ];
    try
    {
        #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
        if($code != 200 && $code != 204)
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
    }
    catch(Exception $E)
    {
        die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
    }
    return $out;
    curl_close($curl); //Закрываем курл
}



function create_n_el($num) {//Функция создания контактов, компаний, сделок и покупателей
    //проверим, насколько много
    $circle = ceil($num / 100);
    for($j = 1; $j <= $circle; $j++) {                         //Изменить на array_chunk!!!!
        echo $j.' круг отправки:';
        //Создаем  массивы с данными
        for ($i = 1 * $j; $i <= 100 * $j; $i++) {
            if($i <= $num) {
                $contacts['add'][] = [                           // Заполняем N контактов значениями
                    'name' => 'Contact ' . $i
                ];
            }
        }
        //100 значений добавлено в массив, можно отправлять URL
        $link = 'https://testPolinaSvet.amocrm.ru/api/v2/contacts';
        /*
         Данные получаем в формате JSON, поэтому, для получения читаемых данных,
         нам придётся перевести ответ в формат, понятный PHP
         */
        $result = curl($link, 'POST', $contacts);
        $ids_contacts = [];
        foreach($result['_embedded']['items'] as $item) {  //
            $ids_contacts[] = $item['id'];
        }
        //теперь добавим компаний
        for($i = 1 * $j; $i <= 100 * $j; $i++) {// Заполняем N копаний значениями
            //echo $i.'   '.$ids_contacts[$i];
            $companies['add'][] = [
                'name' => 'Company ' . $i,
                'contacts_id' => $ids_contacts[0][$i-1],  // И привязываем к каждой компании контакт
            ];
        }
        $link = 'https://testPolinaSvet.amocrm.ru/api/v2/companies';
        $result = curl($link, 'POST', $companies);
        $ids_companies = [];
        foreach($result['_embedded']['items'] as $item) {  //
            $ids_companies[] = $item['id'];
        }
        unset($result);
        //создаем сделки
        for($i = 1 * $j; $i <= 100 * $j; $i++) {       // Заполняем N сделок значениями
            $leads['add'][] = [
                'name' => 'Lead ' . $i,
                'contacts_id' => $ids_contacts[0][$i-1],  // Привязываем к каждой сделке контакт
                'company_id'  => $ids_companies[0][$i-1]  // Привязываем к каждой сделке компанию
            ];
        }
        #Формируем ссылку для запроса
        $link = 'https://testPolinaSvet.amocrm.ru/api/v2/leads';
        $result = curl($link, 'POST', $leads);
        $ids_leads = [];
        foreach($result['_embedded']['items'] as $item) {  //
            $ids_leads[] = $item['id'];
        }
        unset($result);
        //создаем покупателей
        for($i = 1 * $j; $i <= 100 * $j; $i++) {       // Заполняем N покупателей значениями
            $customers['add'][] = [
                'name' => 'Customer ' . $i,
                'company_id'  => $ids_companies[0][$i-1], //Привязываем к каждому покупателю компанию
                'next_date' => strtotime("now"),   //Обязательный параметр,
                'contacts_id' => array_rand($ids_contacts, 1)
            ];
        }
        #Формируем ссылку для запроса
        $link = 'https://testPolinaSvet.amocrm.ru/api/v2/customers';

        $result = curl($link, 'POST', $customers);
        $ids_customers = [];
        foreach($result['_embedded']['items'] as $item) {
            $ids_customers[] = $item['id'];
        }
        unset($result);
    }
        // создаем мультисписок и привязываем его ко всем контактам
        $fields['add'][] = [
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
        echo "<pre>";
        $id_field = $result['_embedded']['items'][0]['id'];
        echo 'id созданного мультисписка: '.$id_field;
        echo "</pre>";
        unset($result);
        //добавляем рандомные значения мультисписка к контактам -- пока не реализовано


return TRUE;
};
?>
<a href="/"><button>Вернуться на главную</button></a>
</body>
</html>
