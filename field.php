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
}
