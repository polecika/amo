<?php
/**
 * Class field формирует запрос на создание и обновление кастомных полей
 */

class field extends Entities
{
    public function get_data() {
        $data = [
            'name' => $this->get_name(),
            'field_type' => $this->get_field_type(),
            'element_type' => $this->get_entity_type(),
            'origin' => "987654321_Polina_Svet",
            'is_editable' => "1", // Значение: 1 - можно редактировать, 0 - нельзя
            'enums' => $this->get_enums(),
        ];
        if ($this->get_entity_type() == "12") {
            $entity['next_date'] = strtotime("+1 week") ;
        }
        return $data;
    }
    public function create_add($name, $field_type, $entity_type, $enums = NULL)
    {
        $entity['add'][] = [
            'name' => $name,
            'field_type' =>  $field_type,
            'element_type' => $entity_type,
            'origin' => "987654321_Polina_Svet",
            'is_editable' => "1", // Значение: 1 - можно редактировать, 0 - нельзя
            'enums' => $enums,
        ];
        if ($entity_type == "12") {
            $entity['add'][0]['next_date'] = strtotime("+1 week") ;
        }
        return $entity;
    }

    /**
     * @param $id - id сущности, у которой будем менять значение кастомного поля
     * @param $id_field - id кастомного поля
     * @param $text - текст изменения
     * @return array - массив запроса
     */
    public function create_update($id, $id_field, $text) {
        $this->_method = 'POST';
        $entity = [];
        $entity['update'][] = [
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
        return $entity;
    }
}