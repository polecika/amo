<?php
/**
 * Created by PhpStorm.
 * User: psvet
 * Date: 18.04.2019
 * Time: 20:11
 */

class Task extends cURL
{
    private $_entity = [];   // массив с сущностями
    private $_type;          // add или update
    public function create_add($id, $entity_type, $deadline_date, $text, $id_main_user) {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/tasks';
        $this->_type = 'add';
        $this->_entity[$this->_type][] = [
            'element_id' => $id,
            'element_type' => $entity_type,
            'complete_till_at' => $deadline_date,
            'task_type' => mt_rand(1,3),
            'text' => $text,
            'created_at' => strtotime("now"),
            'responsible_user_id' => $id_main_user
        ];
        return $this->_entity;
    }
    public function create_update($id) {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/tasks';
        $this->_type = 'update';
        $this->_entity[$this->_type][] = [
            'id' => $id,
            'updated_at' => strtotime("now"),
            'text' => 'complete!',
            'is_completed' => TRUE
        ];
        return  $this->_entity;
    }

}