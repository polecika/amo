<?php
/**
 * Class Note отвечает за сущность Примечанний
 */

class Note extends Entities
{
    public function get_data() {
        return [
            'id' => $this->get_id(),
            'element_id' => $this->get_element_id(),
            'element_type' => $this->get_entity_type(),
            'text' => $this->get_text(),
            'note_type' => $this->get_note_type(),
            'updated_at' => $this->get_update_datetime(),
            'params' => [
                [
                    'UNIQ' => 'BCEFA2341',
                    'DURATION' => '33',
                    'SRC' => $this->get_param_src(),
                    'LINK' => $this->get_param_link(),
                    'PHONE' => $this->get_param_phone()
                ]
            ]
        ];
    }
}
