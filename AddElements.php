<?php
class AddElements extends cURL
{
    public $_cut_step;
    public $_elem_array = [];
    public $_id_array = [];
    private $_bool = TRUE;
    public $_result;
    public $_type;

    public function createElem()
    {
        foreach (array_chunk($this->_elem_array[$this->_type], $this->_cut_step, $this->_bool) as $cutArray) {//Делим массив по 200 элементов и отправляем cURL
            // Отправляем cURL по 200 сущностей
            $data_curl = [];
            $data_curl[$this->_type] = $cutArray;
            $this->_data = $data_curl;
            $result = $this->request();
            foreach ($result['_embedded']['items'] as $item) {    // Сохраняем id контактов для последующего создания связей
                $this->_id_array[] = $item['id'];
            }
        }
        return $this->_id_array;
    }
}