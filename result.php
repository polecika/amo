<!DOCTYPE>
<html>
<body>
<?php
require('config.php');
class cURL {
    public $link;
    public $method;
    public $data;

    public function curl1()
    { //Функция курл запроса, принимает ссылку, метод
        $curl = curl_init();
        #Устанавливаем необходимые опции для сеанса cURL
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $this->link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->data));
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
    public $cut_step;
    public $ElemArray = [];
    public $IdArray = [];
    private $bool = TRUE;
    public $result;
    public $type;
    public function createElem() {
        foreach(array_chunk($this->ElemArray[$this->type], $this->cut_step, $this->bool) as $cutArray) {//Делим массив по 200 элементов и отправляем cURL
            // Отправляем cURL по 200 сущностей
            $data_curl = [];
            $data_curl[$this->type] = $cutArray;
            $this->data = $data_curl;
            $result = $this->curl1();
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
$auth->curl1();

$func = $_GET["func"];// Получаем номер среагировавшей формы
switch ($func) {
    case 1:
        $n = $_POST['number'];      //получаем данные из формы

        //Добавляем контакты
        $contacts = new AddElements();
        $contacts->cut_step = 200;
        $contacts->type = 'add';
        $contacts->link = SUBDOMAIN.'api/v2/contacts';
        $contacts->method = 'POST';
        for ($i = 1; $i <= $n; $i++) {                    // Заполняем массив на N элементов значениями КОНТАКТОВ
            $contacts->ElemArray[$contacts->type][] = [
                'name' => 'Contacts ' . $i
            ];
        }
        $ids_contacts = $contacts->createElem();

        $companies = new AddElements();
        $companies->cut_step = 200;
        $companies->link = SUBDOMAIN.'api/v2/companies';
        $companies->type = 'add';
        $companies->method = 'POST';
        for ($i = 1; $i <= $n; $i++) {                     // Заполняем массив на N элементов значениями КОМПАНИЙ
            $companies->ElemArray[$companies->type][] = [
                'name' => 'Company ' . $i,
                'contacts_id' => $ids_contacts[$i-1],     // Привязываем к каждой компании контакт по id
            ];
        }
        $ids_companies = $companies->createElem();

        $leads = new AddElements();
        $leads->cut_step = 200;
        $leads->link = SUBDOMAIN.'api/v2/leads';
        $leads->type = 'add';
        $leads->method = 'POST';
        for ($i = 1; $i <= $n; $i++) {                      // Заполняем массив на N элементов значениями СДЕЛОК
            $leads->ElemArray[$leads->type][] = [
                'name' => 'Lead ' . $i,
                'contacts_id' => $ids_contacts[$i-1],      // Привязываем к каждой сделке контакт по id
                'company_id'  => $ids_companies[$i-1]      // Привязываем к каждой сделке компанию по id
            ];
        }
        $leads->createElem();
        $customers = new AddElements();
        $customers->cut_step = 200;
        $customers->type = 'add';
        $customers->link = SUBDOMAIN.'api/v2/customers';
        $customers->method = 'POST';
        for ($i = 1; $i <= $n; $i++) {// Заполняем массив на N элементов значениями ПОКУПАТЕЛЕЙ
            $customers->ElemArray[$customers->type][] = [
                'name' => 'Customer ' . $i,
                'company_id'  => $ids_companies[$i-1],      // Привязываем к каждому покупателю компанию
                'next_date' => strtotime("now"),          // Обязательный параметр,
                'contacts_id' => $ids_contacts[array_rand($ids_contacts, 1)]
            ];
        }
        $customers->createElem();
        $field['add'][] = [                                       // Cоздаем мультисписок
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
        $fields = new cURL();
        $fields->data = $field;
        $fields->method = 'POST';
        $fields->link = SUBDOMAIN.'api/v2/fields';
        $id_field = $fields->curl1();
        $id_field = $id_field['_embedded']['items'][0]['id'];

        $up_cont = new AddElements();
        $up_cont->link = SUBDOMAIN.'api/v2/contacts';
        $up_cont->method = 'POST';
        $up_cont->cut_step = 20;
        $up_cont->type = 'update';
        for ($i = 1; $i <= $n; $i++) {
            $up_cont->ElemArray[$up_cont->type][] = [
                'id' => $ids_contacts[$i-1],
                'updated_at' => strtotime("now"),
                'custom_fields' => [
                    [
                        'id' => $id_field,
                        'values' => []
                    ]
                ]
            ];
            $how_much = mt_rand(1,10);                // Количество значений мультисписка, которые будут отмечены
            for($j = 0; $j < $how_much; $j++) {                       //будем добавлять поля в массив
                array_push($up_cont->ElemArray[$up_cont->type][$i-1]['custom_fields'][0]['values'], ['enum' => mt_rand(1,10)]);
            }
        }
        $up_cont->createElem();

        echo 'Было добавлено '.$n.' контактов, сделок, покупателей и компаний';
        echo '</br>';
        echo 'Был создан мультисписок и всем контактам назначено рандомок количество значений';
        echo '</br>';

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


?>
<a href="/"><button>Вернуться на главную</button></a>
</body>
</html>
