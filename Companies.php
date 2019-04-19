<?php

/**
 * Class Companies отвечает за обработку сощностей Компаний
 */
class Companies extends cURL
{
    /**
     * @param $ids_contacts - массив с id контактов для создания связей
     * @return array - массив с id компаний
     */
    public function create_add($ids_contacts)
    {
        $this->_method = 'POST';
        $this->_link = SUBDOMAIN.'api/v2/companies';
        $entity = [];
        $i = 0;
        foreach ($ids_contacts as $id_contact) {
            $entity['add'][] = [
                'name' => 'Company ' . $i,
                'contacts_id' => $id_contact,
            ];
            $i++;
        }
        return $entity;
    }
}
