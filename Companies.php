<?php

/**
 * Class Companies отвечает за обработку сощностей Компаний
 */
class Companies extends Entities
{
    public function get_data() {
        return [
            'id' => $this->get_id(),
            'name' => $this->get_name(),
            'updated_at' => $this->get_update_datetime(),
            'contacts_id' => [
                $this->get_contact_id()
            ],
            'custom_fields' => [
                [
                    'id' => $this->get_field_id(),
                    'values' => $this->get_field_value()
                ]
            ]
        ];
    }
}
