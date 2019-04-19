<?php

/**
 * Class Leads отвечает за сущность Сделок
 */
class Leads extends cURL
{
    /**
     * @param $ids_contacts - массив id контактов для создания связей
     * @param $ids_companies - массив id компаний для создания связей
     * @return array - массив запросов
     */
    public function create_add($ids_contacts, $ids_companies) {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/leads';
        $i = 1;
        $entity = [];
        foreach($ids_contacts as $id_contact) {            // Заполняем массив на N элементов значениями СДЕЛОК
            $entity['add'][] = [
                'name' => 'Lead ' . $i,
                'contacts_id' => $id_contact,              // Привязываем к каждой сделке контакт по id
                'company_id'  => $ids_companies[$i-1]      // Привязываем к каждой сделке компанию по id
            ];
            $i++;
        }
        return $entity;
    }
}

