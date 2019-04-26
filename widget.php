<?php
header("Access-Control-Allow-Origin: *");
require ('config.php');
function __autoload( $className ) {
    $className = str_replace( "..", "", $className );
    require_once( "$className.php" );
}
function fill_cell($array_data, $need_get_request = FALSE) {
    $cell = '';
    if(!empty($array_data)) {
        if($need_get_request === TRUE) {
            $link = substr($array_data['_links']['self']['href'], 1);
            $info = cURL::request($link);
            $array_data = $info['_embedded']['items'];
        }
        foreach($array_data as $elem) {
            $cell .= $elem['name'];
            if(isset($elem['values'])) {
                $cell .= ': ';
                foreach ($elem['values'] as $value) {
                    $cell .= $value['value'].', ';
                }
            }
            $cell .= ', ';
        }
        $cell = substr_replace($cell, ';', -2);
    }
    else {
        $cell = 'НЕТ';
    }
   return $cell;
}
//авторизация
$auth = new Auth();
$auth->Autorisation();
//экспорт выбранных сделок (название, дата создания, теги,
// информация из кастомных полей),  названия связанных  контактов, компаний в файл csv.
if(isset($_POST['data'])) {
    $ids = $_POST['data'];
    $id_array = explode(";", $_POST['data']);
    //открываем файл для записи
    $fp = fopen('file.csv', 'w');
    //заполняем шапку страницы
    $table_header = ['Назание сделки', 'Дата создания', 'Теги', 'Кастомные поля', 'Контакты', 'Компании'];
    fputcsv($fp, $table_header);
    foreach ($id_array as $id) {
        if($id!='') {
            $result = cURL::request('api/v2/leads?id='.$id);
            $result = $result['_embedded']['items'][0];
            //Заполняем ячейку "Теги"
            //Будет выводить название всех тегов, через запятую
            $tag_names = fill_cell($result['tags']);
            //Содержание ячейки "Дополнительные поля"
            //Будет выводить название дополнительного поля и значения
            $fields = fill_cell($result['custom_fields']);
            //Содержание яцейки "Контакты"
            $contacts = fill_cell($result['contacts'], TRUE);
            //Содержание яцейки "Компании"
            $companies = fill_cell($result['company'], TRUE);
           //Составляем массив для новой строки файла
            $leads_info = [
                $result['name'],
                date('d.m.Y ', $result['created_at']),
                $tag_names,
                $fields,
                $contacts,
                $companies
            ];
            $result = [];
            fputcsv($fp, $leads_info);
        }
    }
    fclose($fp);
}
