<?php
class Companies extends cURL
{
    private $_entity = [];   // массив с сущностями
    private $_type;          // add или update
    private $_bool = TRUE;
    private $_array_id =[];  // id нашего массива

    public function create_add($ids_contacts)
    {
        $this->_type = 'add';
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/companies';
        $i = 1;
        foreach ($ids_contacts as $id_contact) {
            $this->_entity[$this->_type][] = [
                'name' => 'Company ' . $i,
                'contacts_id' => $ids_contacts[$i-1],
            ];
            $i++;
        }
        return $this->_entity;
    }

    /**
     * @param $cut_step
     * @param $data
     * @return array
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
