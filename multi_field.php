<?php

class field extends cURL
{
    private $_entity = [];   // массив с сущностями
    private $_type;          // add или update
    public function create_add($name, $field_type, $entity_type, $enums)
    {
        $this->_type = 'add';
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/fields';
        $this->_entity[$this->_type][] = [
            'name' => $name,
            'field_type' =>  $field_type,
            'element_type' => $entity_type,
            'origin' => "987654321_Polina_Svet",
            'is_editable' => "1", // Значение: 1 - можно редактировать, 0 - нельзя
            'enums' => $enums
        ];
        return $this->_entity;
    }
}