<!DOCTYPE>
<html>
<body>
<?php
require ('config.php');
function __autoload( $className ) {
  $className = str_replace( "..", "", $className );
  require_once( "$className.php" );
}

//попробуем сделать авторизация в ООП
$auth = new Auth();
$auth->Autorisation();
$func = $_GET["func"];// Получаем номер среагировавшей формы
switch ($func) {
    case 1:
        $n = $_POST['number'];      //получаем данные из формы
        //Создаем дополнительное поле мультичписок
        $fields = new field();
        $fields->set_name('Новый мультисписок:');
        $fields->set_entity_type(1);
        $fields->set_field_type(5);
        $fields->set_enums(["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"]);
        $field [] = $fields;
        $id_field = Entities::create($field, 'api/v2/fields', 'add');
        $id_field = $id_field[0];     //id озданного мультисписка

        //Получаем id каждого элемента мультисписка
        $sun = new cURL();
        $enum_array = cURL::request('api/v2/account?with=custom_fields');
        $enum_array = array_keys($enum_array['_embedded']['custom_fields']['contacts'][$id_field]['enums']);

        //Добавляем контакты
        $contacts = [];
        for($i = 0; $i < $n; $i++) {
            $contact = new Contacts();
            $contact->set_name('Contact '.($i+1));
            $contact->set_field_id($id_field);
            $field = [];
            $how_much = mt_rand(1,10);// Количество значений мультисписка, которые будут отмечены
            for($j = 0; $j < $how_much; $j++) {                        //будем добавлять поля в массив
                $field[] =  ['enum' => $enum_array[mt_rand(0,9)]] ;
            }
            $contact->set_field_value($field);
            $contacts[] = $contact;
        }
        $ids_contacts = Entities::create($contacts, 'api/v2/contacts', 'add');
        //Добавляем компании
        $companies = [];
        for($i = 0; $i < $n; $i++) {
            $company = new Companies();
            $company->set_name('Company '.($i+1));
            $company->set_contact_id($ids_contacts[$i]);
            $companies[] = $company;
        }
        $ids_companies = Entities::create($companies, 'api/v2/companies', 'add');
        //Добавляем сделки
        $leads = [];
        for($i = 0; $i < $n; $i++) {
            $lead = new Leads();
            $lead->set_name('Lead '.($i+1));
            $lead->set_contact_id($ids_contacts[$i]);
            $lead->set_company_id($ids_companies[$i]);
            $leads[] = $lead;
        }
        Entities::create($leads, 'api/v2/leads', 'add');
        //Добавляем покупателей
        $customers = [];
        for($i = 0; $i < $n; $i++) {
            $customer = new Customers();
            $customer->set_name('Customer '.($i));
            $customer->set_company_id($ids_companies[$i]);
            $customer->set_contact_id($ids_contacts[$i]);
            $customers[] = $customer;
        }
        Entities::create($customers, 'api/v2/customers', 'add');
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
                $link = 'api/v2/contacts/';
                $update_entity = new Contacts();
                break;
            case "2":
                $link = 'api/v2/leads/';
                $update_entity = new Leads();
                break;
            case "3":
                $link = 'api/v2/companies/';
                $update_entity = new Companies();
                break;
            case "12":
                $link = 'api/v2/customers/';
                $update_entity = new Customers();
                break;
        }
        //Получаем все id полей custom_fields полученной сущности
        $result = cURL::request($link.'?id='.$id);
        $result = $result['_embedded']['items'][0]["custom_fields"];
        if(isset($result)) {
            foreach($result as $item) {          // Для каждого доп. поля проверяем если оно текстовое
                if (count($item['values']) && is_string($item['values'][0]['value'])) {
                    // И id еще не назначено
                    $id_field = $item["id"]; // То назначаем. Потом будем по этому id менять
                    break;
                }
            }
        }
        $result = [];
        if(!isset($id_field)) {   //Если такое поле так и не найдено - создаем его
            $field = new field();
            $field->set_name('Тест');
            $field->set_field_type(1);
            $field->set_entity_type($entity_type);
            $fields[] = $field;
            $id_field = Entities::create($fields, $link, 'add');
        }
        //Теперь по id поля меняем значение доп.поля
        $update_entity->set_id($id);
        $update_entity->set_update_datetime(strtotime("now"));
        $update_entity->set_field_id($id_field);
        $update_entity->set_field_value(
            [
                [
                        'value' => $text
                ]
            ]
        );
        $update_value[] = $update_entity;
        Entities::create($update_value, $link, 'update');
        echo 'Поле типа текст было изменено у сущности с id = '.$id.'</br>';
        break;
    case 3:
        //Получаем значения из формы
        $id = $_POST['id'];
        $entity_type = $_POST['entity_type'];
        $note_type = $_POST['note_type'];
        $text = $_POST['text'];
        $note = new Note();
        $note->set_id($id);
        $note->set_entity_type($entity_type);
        $note->set_note_type($note_type);
        $note->set_text($text);
        if($note_type == 10) {
            $note->set_param_src('http://example.com');
            $note->set_param_link('http://example.com');
            $note->set_param_phone('79854723575');
        }
        $add_note[] = $note;
        Entities::create($add_note, 'api/v2/notes', 'add');
        echo 'Примечание добавлено успешно'.'</br>';
        break;
    case 4:
        //Получаем значения из формы
        $id = $_POST['id'];
        $entity_type = $_POST['entity_type'];
        $deadline_date = strtotime($_POST['date']);
        $id_main_user = $_POST['id_main_user'];
        $text = $_POST['text'];
        $task = new Task();
        $task->set_element_id($id);
        $task->set_entity_type($entity_type);
        $task->set_id_main_user($id_main_user);
        $task->set_deadline_date($deadline_date);
        $task->set_task_type(mt_rand(1,3));
        $task->set_text($text);
        $add_task[] = $task;
        Entities::create($add_task, 'api/v2/tasks', 'add');
        echo 'Задание добавлено</br>';
        break;
    case 5:
        //Получаем значения из формы
        $id = $_POST['id'];
        $task = new Task();
        $task->set_id($id);
        $task->set_is_completed('TRUE');
        $task->set_update_datetime(strtotime("now"));
        $close_task[] = $task;
        Entities::create($close_task, 'api/v2/tasks', 'update');
        echo 'Задача завершена';
        break;
}
?>
<a href="/"><button>Вернуться на главную</button></a>
</body>
</html>
