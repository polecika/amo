<?php
class Leads extends cURL
{
    private $_entity = [];   // массив с сущностями
    private $_type;          // add или update
    private $_bool = TRUE;
    private $_array_id =[];  // id нашего массива
    public function create_add($ids_contacts, $ids_companies) {
        $this->_type = 'add';
        $i = 1;
        foreach($ids_contacts as $id_contact) {            // Заполняем массив на N элементов значениями СДЕЛОК
            $this->_entity[$this->_type][] = [
                'name' => 'Lead ' . $i,
                'contacts_id' => $id_contact,              // Привязываем к каждой сделке контакт по id
                'company_id'  => $ids_companies[$i-1]      // Привязываем к каждой сделке компанию по id
            ];
        }
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
