<?php

/**
 * Класс для формирования запроса на добавление и/или изменения сущности Кортактов
 * Class Contacts
 */
class Contacts extends cURL {
    private $_entity = [];   // массив с сущностями
    private $_type;          // add или update
    private $_bool = TRUE;
    private $_array_id =[];  // id нашего массива

    /**
     * @param $n
     * @return array
     */
    public function create_add($n)
    {
        $this->_type = 'add';
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/contacts';
        for ($i = 1; $i <= $n; $i++) {
            $this->_entity[$this->_type][] = [
                'name' => 'Contact ' . $i,
                'custom_fields' => [
                    [
                        'values' => []
                    ]
                ]
            ];
        }
        return $this->_entity;
    }

    /**
     * * Создание массива на обновление
     * @param $_contact_id
     * @param $_field_id
     * @return array
     */
    public function create_update($_contact_id, $_field_id) {
        $this->_type = 'update';
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/contacts';
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

    /**
     * Разбиение и создание сущностей
     * @param $cut_step
     * @param $data
     * string $data['add']['name']
     * int $data
     * @return array ids
     */
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
