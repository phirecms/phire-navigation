<?php

namespace Navigation\Model;

use Navigation\Table;
use Phire\Model\AbstractModel;
use Pop\Nav\Nav;

class Navigation extends AbstractModel
{

    /**
     * Get all navigation
     *
     * @param  string $sort
     * @return array
     */
    public function getAll($sort = null)
    {
        $order = (null !== $sort) ? $this->getSortOrder($sort) : 'id ASC';
        return Table\Navigation::findBy(['parent_id' => $pid], null, ['order' => $order])->rows();
    }

    /**
     * Get navigation by ID
     *
     * @param  int $id
     * @return void
     */
    public function getById($id)
    {
        $navigation = Table\Navigation::findById($id);
        if (isset($navigation->id)) {
            $data = $navigation->getColumns();
            $this->data = array_merge($this->data, $data);
        }
    }

    /**
     * Save new navigation
     *
     * @param  array $fields
     * @return void
     */
    public function save(array $fields)
    {
        $navigation = new Table\Navigation([
            'title'     => $fields['title']
        ]);
        $navigation->save();

        $this->data = array_merge($this->data, $navigation->getColumns());
    }

    /**
     * Update an existing navigation
     *
     * @param  array $fields
     * @return void
     */
    public function update(array $fields)
    {
        $navigation = Table\Navigation::findById((int)$fields['id']);
        if (isset($navigation->id)) {
            $navigation->title     = $fields['title'];
            $navigation->save();

            $this->data = array_merge($this->data, $navigation->getColumns());
        }
    }

    /**
     * Remove a navigation
     *
     * @param  array $fields
     * @return void
     */
    public function remove(array $fields)
    {
        if (isset($fields['rm_navigation'])) {
            foreach ($fields['rm_navigation'] as $id) {
                $navigation = Table\Navigation::findById((int)$id);
                if (isset($navigation->id)) {
                    $navigation->delete();
                }
            }
        }
    }

    /**
     * Determine if list of navigation has pages
     *
     * @param  int $limit
     * @return boolean
     */
    public function hasPages($limit)
    {
        return (Table\Navigation::findAll()->count() > $limit);
    }

    /**
     * Get count of navigation
     *
     * @return int
     */
    public function getCount()
    {
        return Table\Navigation::findAll()->count();
    }

}
