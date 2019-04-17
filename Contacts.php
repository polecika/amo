<?php
class Contacts extends cURL
{
    private $_entity = [];   // массив с сущностями
    private $_type;          // add или update
    private $_bool = TRUE;
    private $_array_id =[];  // id нашего массива
    public function create_add($n)
    {
        $this->_type = 'add';
        for ($i = 1; $i <= $n; $i++) {
            $this->_entity[$this->_type][] = [
                'name' => 'Contact ' . $i
            ];
        }
        return $this->_entity;
    }
    public function create_update($_contact_id, $_field_id) {
        $this->_type = 'update';
            $this->_entity[$this->_type][] = [
                'id' => $_contact_id,
                'updated_at' => strtotime("now"),
                'custom_fields' => [
                    [
                        'id' => $_field_id,
                        'values' => []
                    ]
                ]
            ];
        return $this->_entity;
    }
    public function createElem($cut_step, $data)               //метод деления массива
    {
        foreach (array_chunk($data[$this->_type], $cut_step, $this->_bool) as $cutArray) {
            // Отправляем cURL по определенному количеству сущностей
            $data_curl = [];
            $data_curl[$this->_type] = $cutArray;
            $this->_data = $data_curl;
            $result = $this->request();
            foreach ($result['_embedded']['items'] as $item) {    // Сохраняем id контактов для последующего создания связей
                $this->_array_id[] = $item['id'];
            }
        }
        return $this->_array_id;
    }
}
