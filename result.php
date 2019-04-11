<!DOCTYPE>
<html>
<body>
<?php

// Тут сразу нужно авторизироваться
$curl=curl_init(); #Сохраняем дескриптор сеанса cURL
$user=[
    'USER_LOGIN'=>'psvet@team.amocrm.com', #Логин (электронная почта)
    'USER_HASH'=>'11622c37a45f475e911e5e21766f727c85ddbde0' #Хэш для доступа к API (смотрите в профиле пользователя)
];
$user = json_encode($user);
$link='https://testPolinaSvet.amocrm.ru/private/api/auth.php?type=json';
curl($curl, $link, 'POST', $user);
$func=$_GET["func"];// Получаем номер среагировавшей формы
switch ($func) {
    case 1:
        $n=$_POST["number"];              //получаем данные из формы

        if(create_n_el($curl, $n)) {             //запускаем функцию и проверяем
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
function curl($curl, $link, $method, $data) { //Функция курл запроса, принимает ссылку, метод

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
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
    $code=(int)$code;
    $errors=[
        301=>'Moved permanently',
        400=>'Bad request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not found',
        500=>'Internal server error',
        502=>'Bad gateway',
        503=>'Service unavailable'
    ];
    try
    {
        #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
        if($code!=200 && $code!=204)
            throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
    }
    catch(Exception $E)
    {
        die('Ошибка: '.$E->getMessage().PHP_EOL.'Код ошибки: '.$E->getCode());
    }

    return $out;

}



function create_n_el($curl, $num) {            //Функция создания контактов, компаний, сделок и покупателей
    //Начнём с контактов
    for($i = 1; $i <= $num; $i++) {       // Заполняем N контактов значениями
        $contacts['add'][] = [
            'name' => 'Contact ' . $i
        ];
    }
    /* Инициируем запрос с помощью cURL */
    $link = 'https://testPolinaSvet.amocrm.ru/api/v2/contacts';
    /*
     Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     нам придётся перевести ответ в формат, понятный PHP
     */
    $result = json_decode(curl($curl, $link, 'POST', $contacts),true);
    $ids_contacts = [];
    foreach($result['_embedded']['items'] as $item) {  //
        $ids_contacts[] = $item['id'];
    }
    //echo "<pre>";
    //var_dump($ids_contacts);
    //echo "</pre>";
    unset($result);

    //теперь добавим компаний
    for($i = 0; $i < $num; $i++) {       // Заполняем N копаний значениями
        $companies['add'][] = [
            'name' => 'Company ' . ($i+1),
            'contacts_id' => $ids_contacts[$i]     // И привязываем к каждой компании контакт
        ];
    }
    $link='https://testPolinaSvet.amocrm.ru/api/v2/companies';
    $result = json_decode(curl($curl, $link, 'POST', $companies),true);
    $ids_companies = [];
    foreach($result['_embedded']['items'] as $item) {  //
        $ids_companies[] = $item['id'];
    }
    //echo "<pre>";
    //var_dump($ids_companies);
    //echo "</pre>";
    unset($result);

    //создаем сделки
    for($i = 0; $i < $num; $i++) {       // Заполняем N сделок значениями
        $leads['add'][] = [
            'name' => 'Lead ' . ($i+1),
            'contacts_id' => $ids_contacts[$i],  // Привязываем к каждой сделке контакт
            'company_id'  => $ids_companies[$i]  // Привязываем к каждой сделке компанию
        ];
    }

    #Формируем ссылку для запроса
    $link='https://testPolinaSvet.amocrm.ru/api/v2/leads';


    $result = json_decode(curl($curl, $link, 'POST', $leads),true);
    $ids_leads = [];
    foreach($result['_embedded']['items'] as $item) {  //
        $ids_leads[] = $item['id'];
    }
    //echo "<pre>";
    //var_dump($ids_leads);
    //echo "</pre>";
    unset($result);

    //создаем покупателей
    for($i = 0; $i < $num; $i++) {       // Заполняем N покупателей значениями
        $customers['add'][] = [
            'name' => 'Customer ' . ($i+1),
            'company_id'  => $ids_companies[$i], //Привязываем к каждому покупателю компанию
            'next_date' => strtotime("now"),   //Обязательный параметр,
            'contacts_id' => array_rand($ids_contacts, $num)
        ];
    }
    #Формируем ссылку для запроса
    $link='https://testPolinaSvet.amocrm.ru/api/v2/customers';

    $result = json_decode(curl($curl, $link, 'POST', $customers),true);
    $ids_customers = [];
    foreach($result['_embedded']['items'] as $item) {
        $ids_customers[] = $item['id'];
    }
    //echo "<pre>";
    //var_dump($ids_customers);
    //echo "</pre>";
    unset($result);


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
    $link='https://testPolinaSvet.amocrm.ru/api/v2/fields';

    $result = json_decode(curl($curl, $link, 'POST', $fields),true);
    echo "<pre>";
    $id_field = $result['_embedded']['items'][0]['id'];
    echo 'id созданного мультисписка: '.$id_field;
    echo "</pre>";
    unset($result);
    //добавляем рандомные значения мультисписка к контактам -- пока не реализовано
    foreach($ids_contacts as $contact) {
        $how_much = mt_rand(1,10);
        echo 'Для контакта: '.$contact.' будет добавлено '.$how_much.' рандомных полей мультисписка</br>'; //тестим
        echo 'Начали:</br>';
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
        //будем добавлять поля в массив
        for($i=0; $i<$how_much; $i++) {
            array_push($up_contacts ['update'][0]['custom_fields'][0]['values'], ['enum' => mt_rand(1,10)]);
        }
        //echo "<pre>";
        //var_dump($up_contacts);
        //echo "<pre>";
        $link='https://testPolinaSvet.amocrm.ru/api/v2/contacts';
        curl($curl, $link, 'POST', $up_contacts);
        $up_contacts = [];
    }
};

curl_close($curl); //Закрываем курл
?>
<a href="/"><button>Вернуться на главную</button></a>
</body>
</html>
