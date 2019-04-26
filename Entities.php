<?php


abstract class Entities
{
    private $_id;
    private $_name;
    private $_contact_id;
    private $_field_id;
    private $_field_value;
    private $_field_type;
    private $_entity_type;
    private $_enums = [];
    private $_update_datetime;
    private $_company_id;
    private  $_next_date;
    private $_note_type;
    private $_element_id;
    private $_text;
    private $_param_src;
    private $_param_link;
    private $_param_phone;
    private $_deadline_date;
    private $_task_type;
    private $_id_main_user;
    private $_is_completed;
    public function set_id($id) {
        $this->_id = $id;
    }
    public function get_id() {
        return $this->_id;
    }
    public function set_name($name) {
        $this->_name = $name;
    }
    public function get_name() {
        return $this->_name;
    }
    public function set_field_id($field_id) {
        $this->_field_id = $field_id;
    }
    public function get_field_id() {
        return $this->_field_id;
    }
    public function set_field_value($field_value) {
        $this->_field_value = $field_value;
    }
    public function get_field_value() {
        return $this->_field_value;
    }
    public function set_field_type($field_type) {
        $this->_field_type = $field_type;
    }
    public function get_field_type() {
        return $this->_field_type;
    }
    public function set_entity_type($entity_type) {
        $this->_entity_type = $entity_type;
    }
    public function get_entity_type() {
        return $this->_entity_type;
    }
    public function set_enums($enums) {
        $this->_enums = $enums;
    }
    public function get_enums() {
        return $this->_enums;
    }
    public function set_update_datetime($update_datetime) {
        $this->_enums = $update_datetime;
    }
    public function get_update_datetime() {
        return $this->_update_datetime;
    }
    public function set_contact_id($contact_id) {
        $this->_contact_id = $contact_id;
    }
    public function get_contact_id() {
        return $this->_contact_id;
    }
    public function set_company_id($company_id) {
        $this->_company_id = $company_id;
    }
    public function get_company_id() {
        return $this->_company_id;
    }
    public function set_next_date($next_date) {
        $this->_next_date = $next_date;
    }
    public function get_next_date() {
        return $this->_next_date;
    }
    public function set_note_type($note_type) {
        $this->_note_type = $note_type;
    }
    public function get_note_type() {
        return $this->_note_type;
    }
    public function set_element_id($element_id) {
        $this->_element_id = $element_id;
    }
    public function get_element_id() {
        return $this->_element_id;
    }
    public function set_text($text) {
        $this->_text = $text;
    }
    public function get_text() {
        return $this->_text;
    }
    public function set_param_src($param_src) {
        $this->_param_src = $param_src;
    }
    public function get_param_src() {
        return $this->_param_src;
    }
    public function set_param_link($param_link) {
    $this->_param_link = $param_link;
    }
    public function get_param_link() {
        return $this->_param_link;
    }
    public function set_param_phone($param_phone) {
    $this->_param_phone = $param_phone;
    }
    public function get_param_phone() {
        return $this->_param_phone;
    }
    public function set_deadline_date($deadline_date) {
        $this->_deadline_date = $deadline_date;
    }
    public function get_deadline_date() {
        return $this->_deadline_date;
    }
    public function set_task_type($task_type) {
        $this->_task_type = $task_type;
    }
    public function get_task_type() {
        return $this->_task_type;
    }
    public function set_id_main_user($id_main_user) {
        $this->_id_main_user = $id_main_user;
    }
    public function get_id_main_user() {
        return $this->_id_main_user;
    }
    public function set_is_completed($is_completed) {
        $this->_is_completed = $is_completed;
    }
    public function get_is_completed() {
        return $this->_is_completed;
    }
    static function create(array $entity, $api, $request_type) {
        $ids = [];
        foreach (array_chunk($entity, 200, TRUE) as $entity_chunk) {
            $data = [];
            foreach ($entity_chunk as $entities) {
                $data[$request_type][] = $entities->get_data();
            }
            $result = cURL::request($api, $data);
            foreach($result['_embedded']['items'] as $item) {
                $ids[] = $item['id'];
            }
        }
        return $ids;
    }
}
