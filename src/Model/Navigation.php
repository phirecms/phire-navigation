<?php
/**
 * Phire Navigation Module
 *
 * @link       https://github.com/phirecms/phire-navigation
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Phire\Navigation\Model;

use Phire\Navigation\Table;
use Phire\Model\AbstractModel;
use Pop\Web\Session;

/**
 * Navigation Model class
 *
 * @category   Phire\Navigation
 * @package    Phire\Navigation
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 * @version    1.0.0
 */
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
        return Table\Navigation::findAll(['order' => $order])->rows();
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
            $data         = $navigation->getColumns();
            $data['tree'] = $this->getTree($navigation->id);
            $this->data = array_merge($this->data, $data);
        }
    }

    /**
     * Get select from options from the Content and Categories
     *
     * @param boolean $contentLoaded
     * @param boolean $categoriesLoaded
     * @return array
     */
    public function getSelectFrom($contentLoaded = false, $categoriesLoaded = false)
    {
        $options = [
            'content'    => [],
            'categories' => []
        ];

        if ($contentLoaded) {
            $sess = Session::getInstance();
            unset($sess->lastSortField);
            unset($sess->lastSortOrder);
            unset($sess->lastPage);

            $types = \Phire\Content\Table\ContentTypes::findAll();
            foreach ($types->rows() as $type) {
                $content = new \Phire\Content\Model\Content();
                $content->getAll($type->id, 'id');
                $options['content'][$type->name] = $content->getFlatMap();
            }
        }

        if ($categoriesLoaded) {
            $categories = new \Phire\Categories\Model\Category();
            $categories->getAll();
            $options['categories'] = $categories->getFlatMap();
        }

        return $options;
    }

    /**
     * Get navigation tree
     *
     * @param int $id
     * @return array
     */
    public function getTree($id)
    {
        $tree = [];

        $parents = Table\NavigationItems::findBy(['navigation_id' => $id, 'parent_id' => null], ['order' => 'order ASC']);
        foreach ($parents->rows() as $parent) {
            $branch = [
                'id'         => $parent->id,
                'item_id'    => $parent->item_id,
                'type'       => $parent->type,
                'name'       => $parent->name,
                'href'       => $parent->href,
                'order'      => $parent->order,
                'attributes' => (null !== $parent->attributes) ? unserialize($parent->attributes) : [],
                'children'   => $this->getBranchChildren($id, $parent->id)
            ];

            $tree[] = $branch;
        }

        return $tree;
    }

    /**
     * Get navigation tree branch children
     *
     * @param int $id
     * @return array
     */
    public function getBranchChildren($id, $parentId)
    {
        $branchChildren = [];
        $children       = Table\NavigationItems::findBy(['navigation_id' => $id, 'parent_id' => $parentId], ['order' => 'order ASC']);

        foreach ($children->rows() as $child) {
            $branch = [
                'id'         => $child->id,
                'item_id'    => $child->item_id,
                'type'       => $child->type,
                'name'       => $child->name,
                'href'       => $child->href,
                'order'      => $child->order,
                'attributes' => (null !== $child->attributes) ? unserialize($child->attributes) : [],
                'children'   => $this->getBranchChildren($id, $child->id)
            ];

            $branchChildren[] = $branch;
        }

        return $branchChildren;
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
            'indent'            => (int)$fields['indent']
        ]);
        $navigation->save();

        if ($fields['create_nav_from'] != '----') {
            $this->createNavFrom($fields['create_nav_from'], $navigation->id);
        }

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

            $navigation->save();

            if ($fields['create_nav_from'] != '----') {
                $this->createNavFrom($fields['create_nav_from'], $navigation->id);
            }

            $this->data = array_merge($this->data, $navigation->getColumns());
        }
    }

    /**
     * Save the new navigation items
     *
     * @param  array $post
     * @return void
     */
    public function saveItems(array $post)
    {
        $navigation = Table\Navigation::findById((int)$post['id']);
        if (isset($navigation->id)) {
            // Edit a nav item
            if (isset($post['branch_to_edit'])) {
                $id      = substr($post['branch_to_edit'], 0, strpos($post['branch_to_edit'], '_'));
                $attribs = [];
                if ($post['nav_edit_target'] != '----') {
                    $attribs = ($post['nav_edit_target'] == 'false') ?
                        ['onclick' => 'return false;'] : ['target' => $post['nav_edit_target']];
                }

                $item = Table\NavigationItems::findById((int)$id);
                if (isset($item->id)) {
                    $item->name       = stripslashes(html_entity_decode($post['nav_edit_title'], ENT_COMPAT, 'UTF-8'));
                    $item->href       = $post['nav_edit_href'];
                    $item->attributes = serialize($attribs);
                    $item->save();
                }
            // Add nav item
            } else if (isset($post['nav_title']) && ($post['nav_title'] != '') && ($post['nav_href'] != '')) {
                $attribs = [];
                if ($post['nav_target'] != '----') {
                    $attribs = ($post['nav_target'] == 'false') ?
                        ['onclick' => 'return false;'] : ['target' => $post['nav_target']];
                }

                if ($post['nav_action_object'] == '----') {
                    $item = new Table\NavigationItems([
                        'navigation_id' => $navigation->id,
                        'parent_id'  => null,
                        'item_id'    => (!empty($post['nav_id']) ? $post['nav_id'] : null),
                        'type'       => (!empty($post['nav_type']) ? $post['nav_type'] : null),
                        'name'       => $post['nav_title'],
                        'href'       => $post['nav_href'],
                        'attributes' => serialize($attribs),
                        'order'      => 0
                    ]);
                    $item->save();
                } else {
                    $id = substr($post['nav_action_object'], (strpos($post['nav_action_object'], '_') + 1));
                    $id = substr($id, 0, strpos($id, '_'));
                    $item = new Table\NavigationItems([
                        'navigation_id' => $navigation->id,
                        'parent_id'  => $id,
                        'item_id'    => (!empty($post['nav_id']) ? $post['nav_id'] : null),
                        'type'       => (!empty($post['nav_type']) ? $post['nav_type'] : null),
                        'name'       => $post['nav_title'],
                        'href'       => $post['nav_href'],
                        'attributes' => serialize($attribs),
                        'order'      => 0
                    ]);
                    $item->save();
                }

            }

            // Order nav items
            foreach ($post as $key => $value) {
                if (substr($key, 0, 11) == 'leaf_order_') {
                    $id = substr($key, 11);
                    $id = substr($id, 0, strpos($id, '_'));
                    $item = Table\NavigationItems::findById((int)$id);
                    if (isset($item->id)) {
                        $item->order = (int)$value;
                        $item->save();
                    }
                }
            }

            // Remove nav items
            if (isset($post['rm_nav']) && (count($post['rm_nav']) > 0)) {
                foreach ($post['rm_nav'] as $navId) {
                    $item = Table\NavigationItems::findById((int)$navId);
                    if (isset($item->id)) {
                        $item->delete();
                    }
                }
            }
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
     * @param  mixed $type
     * @param  int   $navId
     * @return void
     */
    protected function createNavFrom($type, $navId)
    {
        if ($type == 'categories') {
            $category   = new \Phire\Categories\Model\Category();
            $contentAry = $category->getAll();
            $cat        = true;
        } else {
            $sess = Session::getInstance();
            unset($sess->lastSortField);
            unset($sess->lastSortOrder);
            unset($sess->lastPage);

            $content    = new \Phire\Content\Model\Content();
            $contentAry = $content->getAll($type, 'id');
            $cat        = false;
        }

        foreach ($contentAry as $c) {
            $item = new Table\NavigationItems([
                'navigation_id' => $navId,
                'item_id'       => $c->id,
                'type'          => ($cat) ? 'category' : 'content',
                'name'          => $c->title,
                'href'          => ($cat) ? '/category' . $c->uri : $c->uri,
                'order'         => 0
            ]);
            $item->save();

            if ((isset($c->status) && $c->status == 1) || !isset($c->status)) {
                $this->createNavChildren($item->id, $navId, $c, 0, $cat);
            }
        }
    }

    /**
     * Create navigation children
     *
     * @param  int                $parentId
     * @param  int                $navId
     * @param  \ArrayObject|array $content
     * @param  int                $depth
     * @param  boolean            $cat
     * @return void
     */
    protected function createNavChildren($parentId, $navId, $content, $depth = 0, $cat = false)
    {
        $child = ($cat) ?
            \Phire\Categories\Table\Categories::findBy(['parent_id' => $content->id], ['order' => 'order ASC']) :
            \Phire\Content\Table\Content::findBy(['parent_id' => $content->id], ['order' => 'order ASC']);

        if ($child->hasRows()) {
            foreach ($child->rows() as $c) {
                if ((isset($c->status) && $c->status == 1) || !isset($c->status)) {
                    $item = new Table\NavigationItems([
                        'navigation_id' => $navId,
                        'parent_id'     => $parentId,
                        'item_id'       => $c->id,
                        'type'          => ($cat) ? 'category' : 'content',
                        'name'          => $c->title,
                        'href'          => ($cat) ? '/category' . $c->uri : $c->uri,
                        'order'         => 0
                    ]);
                    $item->save();

                    $this->createNavChildren($item->id, $navId, $c, ($depth + 1), $cat);
                }
            }
        }
    }

}
