<?php

/**
 * Класс для формирования запроса на добавление и/или изменения сущности Кортактов
 * Class Contacts
 */
class Contacts extends cURL {
    /**
     * @param $n
     * @return array
     */
    public function create_add($n)
    {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/contacts';
        $entity = [];
        for ($i = 1; $i <= $n; $i++) {
            $entity['add'][] = [
                'name' => 'Contact ' . $i,
                'custom_fields' => [
                    [
                        'values' => []
                    ]
                ]
            ];
        }
        return $entity;
    }

    /**
     * * Создание массива на обновление
     * @param $_contact_id
     * @param $_field_id
     * @return array
     */
    public function create_update($_contact_id, $_field_id) {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/contacts';
        $entity['update'][] = [
            'id' => $_contact_id,
            'updated_at' => strtotime("now"),
            'custom_fields' => [
                [
                    'id' => $_field_id,
                    'values' => []
                ]
            ]
        ];
        return $entity;
    }
}

