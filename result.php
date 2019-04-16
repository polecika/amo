<!DOCTYPE>
<html>
<body>
<?php
require('config.php');
require('cURL.php');
require('AddElements.php');
//попробуем сделать авторизация в ООП
$auth = new cURL;
$auth->_link = SUBDOMAIN.'private/api/auth.php?type=json';
$auth->_method = 'POST';
$auth->_data = [
    'USER_LOGIN' => LOGIN, #Логин (электронная почта)
    'USER_HASH' => HASH #Хэш для доступа к API (смотрите в профиле пользователя)
];
$auth->request();

$func = $_GET["func"];// Получаем номер среагировавшей формы
switch ($func) {
    case 1:
        $n = $_POST['number'];      //получаем данные из формы

        //Добавляем контакты
        $contacts = new AddElements();
        $contacts->_cut_step = 200;
        $contacts->_type = 'add';
        $contacts->_link = SUBDOMAIN.'api/v2/contacts';
        $contacts->_method = 'POST';
        for ($i = 1; $i <= $n; $i++) {                    // Заполняем массив на N элементов значениями КОНТАКТОВ
            $contacts->_elem_array[$contacts->_type][] = [
                'name' => 'Contacts ' . $i
            ];
        }
        $ids_contacts = $contacts->createElem();

        $companies = new AddElements();
        $companies->_cut_step = 200;
        $companies->_link = SUBDOMAIN.'api/v2/companies';
        $companies->_type = 'add';
        $companies->_method = 'POST';
        for ($i = 1; $i <= $n; $i++) {                     // Заполняем массив на N элементов значениями КОМПАНИЙ
            $companies->_elem_array[$companies->_type][] = [
                'name' => 'Company ' . $i,
                'contacts_id' => $ids_contacts[$i-1],     // Привязываем к каждой компании контакт по id
            ];
        }
        $ids_companies = $companies->createElem();

        $leads = new AddElements();
        $leads->_cut_step = 200;
        $leads->_link = SUBDOMAIN.'api/v2/leads';
        $leads->_type = 'add';
        $leads->_method = 'POST';
        for ($i = 1; $i <= $n; $i++) {                      // Заполняем массив на N элементов значениями СДЕЛОК
            $leads->_elem_array[$leads->_type][] = [
                'name' => 'Lead ' . $i,
                'contacts_id' => $ids_contacts[$i-1],      // Привязываем к каждой сделке контакт по id
                'company_id'  => $ids_companies[$i-1]      // Привязываем к каждой сделке компанию по id
            ];
        }
        $leads->createElem();
        $customers = new AddElements();
        $customers->_cut_step = 200;
        $customers->_type = 'add';
        $customers->_link = SUBDOMAIN.'api/v2/customers';
        $customers->_method = 'POST';
        for ($i = 1; $i <= $n; $i++) {// Заполняем массив на N элементов значениями ПОКУПАТЕЛЕЙ
            $customers->_elem_array[$customers->_type][] = [
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
        $fields->_data = $field;
        $fields->_method = 'POST';
        $fields->_link = SUBDOMAIN.'api/v2/fields';
        $id_field = $fields->request();
        $id_field = $id_field['_embedded']['items'][0]['id'];

        $up_cont = new AddElements();
        $up_cont->_link = SUBDOMAIN.'api/v2/contacts';
        $up_cont->_method = 'POST';
        $up_cont->_cut_step = 20;
        $up_cont->_type = 'update';
        for ($i = 1; $i <= $n; $i++) {
            $up_cont->_elem_array[$up_cont->_type][] = [
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
                array_push($up_cont->_elem_array[$up_cont->_type][$i-1]['custom_fields'][0]['values'], ['enum' => mt_rand(1,10)]);
            }
        }
        $up_cont->createElem();

        echo 'Было добавлено '.$n.' контактов, сделок, покупателей и компаний';
        echo '</br>';
        echo 'Был создан мультисписок и всем контактам назначено рандомок количество значений';
        echo '</br>';

        break;
    case 2:
        $id = $_POST['id'];        //Получаем значения из формы
        $entity_type = $_POST['entity_type'];
        $text = $_POST['text'];
        //Определяем, с какой сущностью имеем дело и сразу формируем ссылку
        switch ($entity_type) {
            case "1":
                $link = SUBDOMAIN.'api/v2/contacts/';
                break;
            case "2":
                $link = SUBDOMAIN.'api/v2/leads/';
                break;
            case "3":
                $link = SUBDOMAIN.'api/v2/companies/';
                break;
            case "12":
                $link = SUBDOMAIN.'api/v2/customers/';
                break;
        }
        //Получаем все id полей custom_fields полученной сущности
        $taken_entity = new cURL();
        $taken_entity->_link = $link.'?id='.$id;
        $taken_entity->_method = 'GET';
        $result = $taken_entity->request();
        $result = $result['_embedded']['items'][0]["custom_fields"];
        $id_fields_array = [];
        foreach($result as $item) {          // Для каждого доп. поля проверяем если оно текстовое
            if (count($item['values']) && is_string($item['values'][0]['value'])) {
                // И id еще не назначено
                $id_field = $item["id"]; // То назначаем. Потом будем по этому id менять
                break;
            }
        }
        $result = [];
        if(!isset($id_field)) {   //Если такое поле так и не найдено - создаем его
            $field = new cURL();
            $field->_method = 'POST';
            $data['add'][] = [
                'name' => "Тест:",
                'field_type' =>  1,
                'element_type' => $entity_type,
                'origin' => "123456789_Polina_Svet",
                'is_editable' => "1"// Значение: 1 - можно редактировать, 0 - нельзя
            ];
            $field->_link = SUBDOMAIN.'api/v2/fields';
            $field->_data = $data;
            $result = $field->request();
            $data = [];
            $id_field = $result['_embedded']['items'][0]['id'];
            $result = [];
        }
        //Теперь по id поля меняем значение доп.поля
        $update_entity = new cURL();
        $update_entity->_method = 'POST';
        $update_entity->_link = $link;
        $data['update'][] = [
            'id' =>  $id,
            'updated_at' => strtotime("now"),
            'custom_fields'  => [
                 [
                    'id' => $id_field,
                    'values' => [
                        0 => [
                            'value' => $text
                        ]
                    ]
                ]
            ]
        ];
        if ($entity_type == "12") {
            (array_push($data['update'][0], ['next_date' => strtotime("now")]));
        }
        $update_entity->_data = $data;
        $result = $update_entity->request();
        echo 'Поле типа текст было изменено у сущности с id = '.$id.'</br>';
        break;
    case 3:
        //Получаем значения из формы
        $id = $_POST['id'];
        $entity_type = $_POST['entity_type'];
        $note_type = $_POST['note_type'];
        $text = $_POST['text'];
        $add_note = new cURL();
        $add_note->_link = SUBDOMAIN.'api/v2/notes';
        $add_note->_method = 'POST';
        $data = [];
        if($note_type==10) {
            $data['add'][] = [
                'element_id' => $id,
                'element_type' => $entity_type,
                'text' => $text,
                'note_type' => $note_type,
                'created_at' => strtotime("now"),
                'params' => [
                    'UNIQ' => 'BCEFA2341',
                    'DURATION' => '33',
                    'SRC' => '',
                    'LINK' => '',
                    'PHONE' => ''
                ]
            ];
        }
        elseif ($note_type==4) {
            $data['add'][] = [
                'element_id' => $id,
                'element_type' => $entity_type,
                'text' => $text,
                'note_type' => $note_type,
                'created_at' => strtotime("now")
            ];
        }
        $add_note->_data = $data;
        if($add_note->request()) {
            echo 'Примечание добавлено успешно</br>';
        }
        break;
    case 4:
        //Получаем значения из формы
        $id = $_POST['id'];
        $entity_type = $_POST['entity_type'];
        $deadline_date = strtotime($_POST['date']);
        $id_main_user = $_POST['id_main_user'];
        $text = $_POST['text'];
        $add_task = new cURL();
        $add_task->_method = 'POST';
        $add_task->_link = SUBDOMAIN.'api/v2/tasks';
        $data = [];
        $data['add'][] = [
            'element_id' => $id,
            'element_type' => $entity_type,
            'complete_till_at' => $deadline_date,
            'task_type' => mt_rand(1,3),
            'text' => $text,
            'created_at' => strtotime("now"),
            'responsible_user_id' => $id_main_user
        ];
        $add_task->_data = $data;
        if($add_task->request()) {
            echo 'Задание добавлено';
        }
        break;
    case 5:
        //Получаем значения из формы
        $id = $_POST['id'];
        $close_task = new cURL();
        $close_task->_link = SUBDOMAIN.'api/v2/tasks';
        $close_task->_method = 'POST';
        $data = [];
        $data['update'][] = [
            'id' => $id,
            'updated_at' => strtotime("now"),
            'text' => 'complete!',
            'is_completed' => TRUE
        ];
        $close_task->_data = $data;
        $result = [];
        $result = $close_task->request();
        if($result) { //Пока не совсем хорошая проверка, потом подправлю
            echo 'Задача завершена';
        }
        break;
}
?>
<a href="/"><button>Вернуться на главную</button></a>
</body>
</html>
