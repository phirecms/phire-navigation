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
        return Table\Navigation::findAll(null, ['order' => $order])->rows();
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
            'title'             => $fields['title'],
            'top_node'          => (isset($fields['top_node']) ? $fields['top_node'] : null),
            'top_id'            => (isset($fields['top_id']) ? $fields['top_id'] : null),
            'top_class'         => (isset($fields['top_class']) ? $fields['top_class'] : null),
            'top_attributes'    => (isset($fields['top_attributes']) ? $fields['top_attributes'] : null),
            'parent_node'       => (isset($fields['parent_node']) ? $fields['parent_node'] : null),
            'parent_id'         => (isset($fields['parent_id']) ? $fields['parent_id'] : null),
            'parent_class'      => (isset($fields['parent_class']) ? $fields['parent_class'] : null),
            'parent_attributes' => (isset($fields['parent_attributes']) ? $fields['parent_attributes'] : null),
            'child_node'        => (isset($fields['child_node']) ? $fields['child_node'] : null),
            'child_id'          => (isset($fields['child_id']) ? $fields['child_id'] : null),
            'child_class'       => (isset($fields['child_class']) ? $fields['child_class'] : null),
            'child_attributes'  => (isset($fields['child_attributes']) ? $fields['child_attributes'] : null),
            'on_class'          => (isset($fields['on_class']) ? $fields['on_class'] : null),
            'off_class'         => (isset($fields['off_class']) ? $fields['off_class'] : null),
            'indent'            => (int)$fields['indent'],
            'tree'              => (($fields['create_nav_from'] != '----') ?
                $this->createNavFrom($fields['create_nav_from']) : null)
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
            $navigation->title             = $fields['title'];
            $navigation->top_node          = (isset($fields['top_node']) ? $fields['top_node'] : null);
            $navigation->top_id            = (isset($fields['top_id']) ? $fields['top_id'] : null);
            $navigation->top_class         = (isset($fields['top_class']) ? $fields['top_class'] : null);
            $navigation->top_attributes    = (isset($fields['top_attributes']) ? $fields['top_attributes'] : null);
            $navigation->parent_node       = (isset($fields['parent_node']) ? $fields['parent_node'] : null);
            $navigation->parent_id         = (isset($fields['parent_id']) ? $fields['parent_id'] : null);
            $navigation->parent_class      = (isset($fields['parent_class']) ? $fields['parent_class'] : null);
            $navigation->parent_attributes = (isset($fields['parent_attributes']) ? $fields['parent_attributes'] : null);
            $navigation->child_node        = (isset($fields['child_node']) ? $fields['child_node'] : null);
            $navigation->child_id          = (isset($fields['child_id']) ? $fields['child_id'] : null);
            $navigation->child_class       = (isset($fields['child_class']) ? $fields['child_class'] : null);
            $navigation->child_attributes  = (isset($fields['child_attributes']) ? $fields['child_attributes'] : null);
            $navigation->on_class          = (isset($fields['on_class']) ? $fields['on_class'] : null);
            $navigation->off_class         = (isset($fields['off_class']) ? $fields['off_class'] : null);
            $navigation->indent            = (int)$fields['indent'];
            $navigation->tree              = (($fields['create_nav_from'] != '----') ?
                $this->createNavFrom($fields['create_nav_from']) : $navigation->tree);

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

    /**
     * Create nav tree from content type
     *
     * @param  int $id
     * @return mixed
     */
    protected function createNavFrom($id)
    {
        $tree = [];

        $content    = new \Content\Model\Content(['tid' => $id]);
        $contentAry = $content->getAll('order');

        foreach ($contentAry as $c) {
            $branch = [
                'name'     => $c->title,
                'href'     => $c->uri,
                'children' => $this->getNavChildren($c)
            ];

            $roles = unserialize($c->roles);
            if (count($roles) > 0) {
                $branch['acl'] = [];
            }

            $tree[] = $branch;
        }

        return serialize($tree);
    }

    /**
     * Get navigation children
     *
     * @param  \ArrayObject|array $content
     * @param  int                $depth
     * @return array
     */
    protected function getNavChildren($content, $depth = 0)
    {
        $children = [];
        $child    = \Content\Table\Content::findBy(['parent_id' => $content->id], null, ['order' => 'order ASC']);

        if ($child->hasRows()) {
            foreach ($child->rows() as $c) {
                $branch = [
                    'name'     => $c->title,
                    'href'     => $c->uri,
                    'children' => $this->getNavChildren($c, ($depth + 1))
                ];

                $roles = unserialize($c->roles);
                if (count($roles) > 0) {
                    $branch['acl'] = [];
                }

                $children[]  = $branch;
            }
        }

        return $children;
    }

}
