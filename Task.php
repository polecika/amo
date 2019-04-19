<?php
/**
 * Class Task отвечает за сущность Задачи
 */

class Task extends cURL
{
    /**
     * @param $id - id сущности, к которой добавляем задачу
     * @param $entity_type - тип сущности, к которой добавляем задачу
     * @param $deadline_date - до какого срока должна быть выполнена задача
     * @param $text - текст задачи
     * @param $id_main_user - id пользователя
     * @return array - массив запроса
     */
    public function create_add($id, $entity_type, $deadline_date, $text, $id_main_user) {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/tasks';
        $entity = [];
        $entity['add'][] = [
            'element_id' => $id,
            'element_type' => $entity_type,
            'complete_till_at' => $deadline_date,
            'task_type' => mt_rand(1,3),
            'text' => $text,
            'created_at' => strtotime("now"),
            'responsible_user_id' => $id_main_user
        ];
        return $entity;
    }

    /**
     * @param $id - id изменяемой задачи
     * @return array - массив запроса
     */
    public function create_update($id) {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/tasks';
        $entity = [];
        $entity['update'][] = [
            'id' => $id,
            'updated_at' => strtotime("now"),
            'text' => 'complete!',
            'is_completed' => TRUE
        ];
        return  $entity;
    }
}
