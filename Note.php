<?php
/**
 * Created by PhpStorm.
 * User: psvet
 * Date: 18.04.2019
 * Time: 20:00
 */

class Note extends cURL
{
    private $_entity = [];   // массив с сущностями
    private $_type;          // add или update
    public function create_add($id, $entity_type, $note_type, $text) {
        $this->_type = 'add';
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/notes';
        $this->_entity[$this->_type][] = [
            'element_id' => $id,
            'element_type' => $entity_type,
            'text' => $text,
            'note_type' => $note_type,
            'created_at' => strtotime("now"),
        ];
        if($note_type == 10) {
            $this->_entity[$this->_type][] = [
                'params' => [
                    'UNIQ' => 'BCEFA2341',
                    'DURATION' => '33',
                    'SRC' => '',
                    'LINK' => '',
                    'PHONE' => ''
                ]
            ];
        }
        return $this->_entity;
    }
}