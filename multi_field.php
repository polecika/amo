<?php
/**
 * Class field формирует запрос на создание и обновление кастомных полей
 */

class field extends cURL
{
    private $_entity = [];   // массив с сущностями
    private $_type;          // add или update

    /**
     * @param $name - наименование дополнительного поля
     * @param $field_type - тип кастомного поля(int)
     * @param $entity_type - тип сущности(int)
     * @param null $enums - массив значений для мультисписка(по умолчанию NULL)
     * @return array
     */
    public function create_add($name, $field_type, $entity_type, $enums = NULL)
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
            'enums' => $enums,
        ];
        if ($entity_type == "12") {
            $this->_entity[$this->_type][0]['next_date'] = strtotime("+1 week") ;
        }
        return $this->_entity;
    }

    /**
     * @param $id - id сущности, у которой будем менять значение кастомного поля
     * @param $id_field - id кастомного поля
     * @param $text - текст изменения
     * @return array - массив запроса
     */
    public function create_update($id, $id_field, $text) {
        $this->_method = 'POST';
        $this->_type = 'update';
        $this->_entity[$this->_type][] = [
            'id' =>  $id,
            'updated_at' => strtotime("now"),
            'custom_fields'  => [
                [
                    'id' => $id_field,
                    'values' => [
                        [
                            'value' => $text
                        ]
                    ]
                ]
            ]
        ];
        return $this->_entity;
    }
}