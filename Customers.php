<?php

/**
 * Class Customers отвечает за сущность ПОкупателей
 */
class Customers extends Entities
{
    public function get_data() {
        return [
            'id' => $this->get_id(),
            'updated_at' => $this->get_update_datetime(),
            'name' => $this->get_name(),
            'contacts_id' => [
                $this->get_contact_id()
            ],
            'next_date' => $this->get_next_date(),
            'custom_fields' => [
                [
                    'id' => $this->get_field_id(),
                    'values' => $this->get_field_value()
                ]
            ]
        ];
    }
    public function create_add($ids_contacts, $ids_companies) {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/customers';
        $i = 1;
        $entity = [];
        foreach($ids_companies as $id_company) {// Заполняем массив на N элементов значениями ПОКУПАТЕЛЕЙ
            $entity['add'][] = [
                'name' => 'Customer ' . $i,
                'company_id'  => $id_company,      // Привязываем к каждому покупателю компанию
                'next_date' => strtotime("now"),          // Обязательный параметр,
                'contacts_id' => $ids_contacts[array_rand($ids_contacts, 1)]
            ];
            $i++;
        }
        return $entity;
    }
}

