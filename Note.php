<?php
/**
 * Class Note отвечает за сущность Примечанний
 */

class Note extends cURL
{
    /**
     * @param $id - id сущности, к которой будет добавляться примечание
     * @param $entity_type - тип сущнссти(int)
     * @param $note_type - тип примечания(int)
     * @param $text - текст примечания
     * @param string $example_phone - занчение по умолчанию номера телефона(для типа примечания 'звонок')
     * @param string $example_link - занчение по умолчанию ссылки(для типа примечания 'звонок')
     * @param string $example_src - занчение по умолчанию ссылки(для типа примечания 'звонок')
     * @return array
     */
    public function create_add($id, $entity_type, $note_type, $text,
                               $example_phone = '79854723575',
                               $example_link = 'http://example.com',
                               $example_src = 'http://example.com'
    )
    {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/notes';
        $entity = [];
        $entity['add'][] = [
            'element_id' => $id,
            'element_type' => $entity_type,
            'text' => $text,
            'note_type' => $note_type,
            'created_at' => strtotime("now"),
        ];
        if($note_type == 10) {
            $entity['add'][0] ['params'] = [
                'UNIQ' => 'BCEFA2341',
                'DURATION' => '33',
                'SRC' => $example_src,
                'LINK' => $example_link,
                'PHONE' => $example_phone
            ];
        }
        return $entity;
    }
}