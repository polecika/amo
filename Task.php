<?php
/**
 * Class Task отвечает за сущность Задачи
 */
class Task extends Entities
{
    public function get_data() {
        return [
            'id' => $this->get_id(),
            'element_id' => $this->get_element_id(),
            'element_type' => $this->get_entity_type(),
            'complete_till_at' => $this->get_deadline_date(),
            'text' => $this->get_text(),
            'task_type' => $this->get_task_type(),
            'responsible_user_id' => $this->get_id_main_user(),
            'updated_at' => $this->get_update_datetime(),
            'is_completed' => $this->get_is_completed()
        ];
    }
}
