<!DOCTYPE>
<html>
<body>
<?php
require ('config.php');
require ('cURL.php');
require ('Contacts.php');
require ('Companies.php');
require ('Leads.php');
require ('Customers.php');
require ('multi_field.php');
require ('Auth.php');
require ('Note.php');
require ('Task.php');
//попробуем сделать авторизация в ООП
$auth = new Auth();
$auth->Autorisation();
$func = $_GET["func"];// Получаем номер среагировавшей формы
switch ($func) {
    case 1:
        $n = $_POST['number'];      //получаем данные из формы
        //Создаем дополнительное поле мультичписок
        $fields = new field();
        $name = 'Новый мультисписок:';
        $enums = ["11", "21", "31", "41", "51", "61", "71", "81", "91", "101"];
        $fields->_data = $fields->create_add($name, 5, 1, $enums);
        $id_field = $fields->request();
        $id_field = $id_field['_embedded']['items'][0]['id'];     //id озданного мультисписка

        //Получаем id каждого элемента мультисписка
        $sun = new cURL();
        $sun->_link = SUBDOMAIN.'api/v2/account?with=custom_fields';
        $sun->_method = 'GET';
        $enum_array = $sun->request();
        $enum_array = array_keys($enum_array['_embedded']['custom_fields']['contacts'][$id_field]['enums']);

        $step = 200;     //количество сущностей, передаваемых в одном курл запросе
        //Добавляем контакты
        $contacts = new Contacts();
        $data_contacts = $contacts->create_add($n);
        $i = 0;
        foreach ($data_contacts ['add'] as $contact) {
            $how_much = mt_rand(1,10);// Количество значений мультисписка, которые будут отмечены
            $data_contacts['add'][$i]['custom_fields'][0] = [ 'id' => $id_field ];
            for($j = 0; $j < $how_much; $j++) {                        //будем добавлять поля в массив
                $data_contacts['add'][$i]['custom_fields'][0]['values'] [] =  ['enum' => $enum_array[mt_rand(0,9)]] ;
            }
            $i++;
        }
        $ids_contacts = $contacts->createElem($step, $data_contacts);

        //Добавляем компании
        $auth->Autorisation();
        $companies = new Companies();
        $ids_companies = $companies->createElem($step, $companies->create_add($ids_contacts));

        //Добавляем сделки
        $auth->Autorisation();
        $leads = new Leads();
        $leads->createElem($step, $leads->create_add($ids_contacts, $ids_companies));

        //Добавляем покупателей
        $auth->Autorisation();
        $customers = new Customers();
        $customers->createElem($step, $customers->create_add($ids_contacts, $ids_companies));

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
        var_dump($result);
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
            $field->_link = $link;
            echo $field->_link;
            $field->_data = $field->create_add('Тест', 1, $entity_type);
            $result = $field->request();
            $id_field = $result['_embedded']['items'][0]['id'];
        }
        //Теперь по id поля меняем значение доп.поля
        $update_entity = new field();
        $update_entity->_method = 'POST';
        $update_entity->_link = $link;
        $data = $update_entity->create_update($id, $id_field, $text);
        $update_entity->_data = $data;
        $update_entity->request();
        echo 'Поле типа текст было изменено у сущности с id = '.$id.'</br>';
        break;
    case 3:
        //Получаем значения из формы
        $id = $_POST['id'];
        $entity_type = $_POST['entity_type'];
        $note_type = $_POST['note_type'];
        $text = $_POST['text'];
        $add_note = new Note();
        $add_note->_data = $add_note->create_add($id, $entity_type, $note_type, $text);
        if($add_note->request()) {
            echo 'Примечание добавлено успешно'.'</br>';
        }
        break;
    case 4:
        //Получаем значения из формы
        $id = $_POST['id'];
        $entity_type = $_POST['entity_type'];
        $deadline_date = strtotime($_POST['date']);
        $id_main_user = $_POST['id_main_user'];
        $text = $_POST['text'];
        $add_task = new Task();
        $data = $add_task->create_add($id, $entity_type, $deadline_date, $text, $id_main_user);
        $add_task->_data = $data;
        if($add_task->request()) {
            echo 'Задание добавлено';
        }
        break;
    case 5:
        //Получаем значения из формы
        $id = $_POST['id'];
        $close_task = new Task();
        $close_task->_data = $close_task->create_update($id);
        $result = $close_task->request();
        if($result) {
            echo 'Задача завершена';
        }
        break;
}
?>
<a href="/"><button>Вернуться на главную</button></a>
</body>
</html>
