<?php
class Contacts extends Entities {
    public function get_data() {
        return [
            'id' => $this->get_id(),
            'updated_at' => $this->get_update_datetime(),
            'name' => $this->get_name(),
            'custom_fields' => [
                [
                    'id' => $this->get_field_id(),
                    'values' => $this->get_field_value()
                ]
            ]
        ];
    }
}

