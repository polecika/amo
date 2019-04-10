<!DOCTYPE>
<html>
<body>
<?php
require('auth.php');                      // Тут сразу нужно авторизироваться
$func=$_GET["func"];                      // Получаем номер среагировавшей формы
switch ($func) {
    case 1:
        $n=$_POST["number"];              //получаем данные из формы
        if(create_n_el($n)) {             //запускаем функцию и проверяем
            echo 'Было введено число '.$n;
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



function create_n_el($num) {            //Функция создания контактов, компаний, сделок и покупателей
    //Начнём с контактов
    for($i = 1; $i <= $num; $i++) {       // Заполняем N контактов значениями
        $contacts['add'][] = [
            'name' => 'Contact ' . $i
        ];
    }
    /* Инициируем запрос с помощью cURL */
    $link = 'https://testPolinaSvet.amocrm.ru/api/v2/contacts';
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($contacts));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    require ('error_cheching.php');
    /*
     Данные получаем в формате JSON, поэтому, для получения читаемых данных,
     нам придётся перевести ответ в формат, понятный PHP
     */
    $result = json_decode($out,true);
    $ids_contacts = [];
    foreach($result['_embedded']['items'] as $item) {  //
        $ids_contacts[] = $item['id'];
    }
    echo "<pre>";
    var_dump($ids_contacts);
    echo "</pre>";
    unset($result);

    //теперь добавим компаний
    for($i = 0; $i < $num; $i++) {       // Заполняем N копаний значениями
        $companies['add'][] = [
            'name' => 'Company ' . ($i+1),
            'contacts_id' => $ids_contacts[$i]     // И привязываем к каждой компании контакт
        ];
    }
    #Формируем ссылку для запроса
    $link='https://testPolinaSvet.amocrm.ru/api/v2/companies';
    #Устанавливаем необходимые опции для сеанса cURL
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
#Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($companies));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    require('error_cheching.php'); // проверка на ошибки сервера
    $result = json_decode($out,true);
    $ids_companies = [];
    foreach($result['_embedded']['items'] as $item) {  //
        $ids_companies[] = $item['id'];
    }
    echo "<pre>";
    var_dump($ids_companies);
    echo "</pre>";
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
    /* Нам необходимо инициировать запрос к серверу. Воспользуемся библиотекой cURL (поставляется в составе PHP). Подробнее о
    работе с этой
    библиотекой Вы можете прочитать в мануале. */
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($leads));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

    require('error_cheching.php'); // проверка на ошибки сервера
    $result = json_decode($out,true);
    $ids_leads = [];
    foreach($result['_embedded']['items'] as $item) {  //
        $ids_leads[] = $item['id'];
    }
    echo "<pre>";
    var_dump($ids_leads);
    echo "</pre>";
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
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($customers));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    require('error_cheching.php'); // проверка на ошибки сервера
    $result = json_decode($out,true);
    $ids_customers = [];
    foreach($result['_embedded']['items'] as $item) {
        $ids_customers[] = $item['id'];
    }
    echo "<pre>";
    var_dump($ids_customers);
    echo "</pre>";
    unset($result);


    // создаем мультисписок и привязываем его ко всем контактам
    $fields['add'][] = [
        'name' => "Выбор номера:",
        'field_type' =>  5,
        'element_type' => 1,
        'origin' => "123456789_Polina_Svet",
        'is_editable' => "0", //что это????
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
    $curl=curl_init(); #Сохраняем дескриптор сеанса cURL
    #Устанавливаем необходимые опции для сеанса cURL
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($fields));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
    $out=curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    require ('error_cheching.php');
    $result = json_decode($out,true);
    echo "<pre>";
    $id_field = $result['_embedded']['items'][0]['id'];
    echo $id_field;
    echo "</pre>";
    unset($result);
    //добавляем рандомные значения мультисписка к контактам -- пока не реализовано
    foreach($ids_contacts as $contact) {
        $up_contacts ['update'][] = [
            'id' => $contact,
            'updated_at' => strtotime("now"),
            'custom_fields' => [
                [
                    'id' => $id_field,
                    'values' => [
                        "1",
                        "2",
                        "3"   // Пока сделаем обязательными первые три, потом добавим рандома... потом... все потом))
                    ]
                ]

        ]
        ];
    }
};


?>
<a href="/"><button>Вернуться на главную</button></a>
</body>
</html>
