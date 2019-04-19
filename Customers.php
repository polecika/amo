<?php

/**
 * Class Customers отвечает за сущность ПОкупателей
 */
class Customers extends cURL
{
    /**
     * @param $ids_contacts -  массив с id контактов для создания связей
     * @param $ids_companies - массив с id компаний для создания связей
     * @return array - массив запроса на добавление покупателя
     */
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

