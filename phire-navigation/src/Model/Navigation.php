<?php

namespace Phire\Navigation\Model;

use Phire\Navigation\Table;
use Phire\Model\AbstractModel;
use Pop\Nav\Nav;
use Pop\Web\Session;

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
            $data['tree'] = (null !== $data['tree']) ? unserialize($data['tree']) : [];
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
                $content = new \Phire\Content\Model\Content(['tid' => $type->id]);
                $content->getAll('id');
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
     * Save the new navigation tree
     *
     * @param  array $post
     * @return void
     */
    public function saveTree(array $post)
    {
        $navigation = Table\Navigation::findById((int)$post['id']);
        if (isset($navigation->id)) {
            $currentTree      = (null !== $navigation->tree) ? unserialize($navigation->tree) : [];
            $navigation->tree = $this->modifyTree($currentTree, $post);
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

        if ($id == 'categories') {
            $category   = new \Phire\Categories\Model\Category();
            $contentAry = $category->getAll();
            $cat        = true;
        } else {
            $sess = Session::getInstance();
            unset($sess->lastSortField);
            unset($sess->lastSortOrder);
            unset($sess->lastPage);

            $content    = new \Phire\Content\Model\Content(['tid' => $id]);
            $contentAry = $content->getAll('id');
            $cat        = false;
        }


        foreach ($contentAry as $c) {
            $branch = [
                'id'       => $c->id,
                'type'     => ($cat) ? 'category' : 'content',
                'name'     => $c->title,
                'href'     => $c->uri,
                'children' => ((isset($c->status) && $c->status == 1) || !isset($c->status)) ?
                    $this->getNavChildren($c, 0, $cat) : []
            ];

            if (isset($c->roles)) {
                $roles = unserialize($c->roles);
                if (count($roles) > 0) {
                    $branch['acl'] = [
                        'resource' => 'content-' . $c->id
                    ];
                }
            }

            if ((isset($c->status) && $c->status == 1) || !isset($c->status)) {
                $tree[] = $branch;
            }
        }

        return serialize($tree);
    }

    /**
     * Get navigation children
     *
     * @param  \ArrayObject|array $content
     * @param  int                $depth
     * @param  boolean            $cat
     * @return array
     */
    protected function getNavChildren($content, $depth = 0, $cat = false)
    {
        $children = [];
        $child    = ($cat) ?
            \Phire\Categories\Table\Categories::findBy(['parent_id' => $content->id], ['order' => 'order ASC']) :
            \Phire\Content\Table\Content::findBy(['parent_id' => $content->id], ['order' => 'order ASC']);

        if ($child->hasRows()) {
            foreach ($child->rows() as $c) {
                $branch = [
                    'id'       => $c->id,
                    'type'     => ($cat) ? 'category' : 'content',
                    'name'     => $c->title,
                    'href'     => $c->uri,
                    'children' => ((isset($c->status) && $c->status == 1) || !isset($c->status)) ?
                        $this->getNavChildren($c, ($depth + 1)) : []
                ];

                if (isset($c->roles)) {
                    $roles = unserialize($c->roles);
                    if (count($roles) > 0) {
                        $branch['acl'] = [
                            'resource' => 'content-' . $c->id
                        ];
                    }
                }

                if ((isset($c->status) && $c->status == 1) || !isset($c->status)) {
                    $children[] = $branch;
                }
            }
        }

        return $children;
    }

    /**
     * Modify nav tree
     *
     * @param  array $currentTree
     * @param  array $post
     * @return mixed
     */
    protected function modifyTree(array $currentTree, array $post)
    {
        // Edit nav items
        if (isset($post['action_edit']) && ($post['action_edit'])) {
            $objectAry = explode('_', $post['branch_to_edit']);
            $node      = [];

            $post['nav_edit_title'] = stripslashes(html_entity_decode($post['nav_edit_title'], ENT_COMPAT, 'UTF-8'));

            foreach ($objectAry as $key => $index) {
                if ($key == 0) {
                    if ($key == (count($objectAry) - 1)) {
                        if (isset($currentTree[$index]['name'])) {
                            $currentTree[$index]['name'] = $post['nav_edit_title'];
                        }
                        if (isset($currentTree[$index]['href'])) {
                            $currentTree[$index]['href'] = $post['nav_edit_href'];
                        }
                        if ($post['nav_edit_target'] != '----') {
                            if (isset($currentTree[$index]['attributes'])) {
                                if (isset($currentTree[$index]['attributes']['onclick'])) {
                                    unset($currentTree[$index]['attributes']['onclick']);
                                }
                                if (isset($currentTree[$index]['attributes']['target'])) {
                                    unset($currentTree[$index]['attributes']['target']);
                                }
                            }
                            $currentTree[$index]['attributes'] = ($post['nav_edit_target'] == 'false') ?
                                ['onclick' => 'return false;'] : ['target' => $post['nav_edit_target']];
                        }
                    } else {
                        $node = &$currentTree[$index];
                    }
                } else if (isset($node['children']) && isset($node['children'][$index])) {
                    if ($key == (count($objectAry) - 1)) {
                        if (isset($node['children'][$index]['name'])) {
                            $node['children'][$index]['name'] = $post['nav_edit_title'];
                        }
                        if (isset($node['children'][$index]['href'])) {
                            $node['children'][$index]['href'] = $post['nav_edit_href'];
                        }
                        if ($post['nav_edit_target'] != '----') {
                            if (isset($node['children'][$index]['attributes'])) {
                                if (isset($node['children'][$index]['attributes']['onclick'])) {
                                    unset($node['children'][$index]['attributes']['onclick']);
                                }
                                if (isset($node['children'][$index]['attributes']['target'])) {
                                    unset($node['children'][$index]['attributes']['target']);
                                }
                            }
                            $node['children'][$index]['attributes'] = ($post['nav_edit_target'] == 'false') ?
                                ['onclick' => 'return false;'] : ['target' => $post['nav_edit_target']];
                        }
                    } else {
                        $node = &$node['children'][$index];
                    }
                }
            }
        // Add/remove nav items
        } else if ((($post['nav_title'] != '') && ($post['nav_href'] != '')) || (isset($post['rm_nav']) && (count($post['rm_nav']) > 0))) {
            if (($post['nav_title'] != '') && ($post['nav_href'] != '')) {
                if ($post['nav_action_object'] != '----') {
                    $objectAry = explode('_', $post['nav_action_object']);
                    $node      = [];
                    foreach ($objectAry as $key => $index) {
                        if ($key == 0) {
                            $node = &$currentTree[$index];
                        } else if (isset($node['children']) && isset($node['children'][$index])) {
                            $node = &$node['children'][$index];
                        }
                    }

                    $leaf = [
                        'id'   => $post['nav_id'],
                        'type' => $post['nav_type'],
                        'name' => $post['nav_title'],
                        'href' => $post['nav_href'],
                        'children' => []
                    ];
                    if ($post['nav_target'] != '----') {
                        $leaf['attributes'] = ($post['nav_target'] == 'false') ?
                            ['onclick' => 'return false;'] : ['target' => $post['nav_target']];
                    }
                    if (!isset($node['children'])) {
                        $node['children'] = [];
                    }
                    if ($post['nav_action'] == 'prepend') {
                        $node['children'] = array_merge([$leaf], $node['children']);
                    } else {
                        $node['children'][] = $leaf;
                    }
                } else {
                    $leaf = [
                        'id'   => $post['nav_id'],
                        'type' => $post['nav_type'],
                        'name' => $post['nav_title'],
                        'href' => $post['nav_href'],
                        'children' => []
                    ];
                    if ($post['nav_target'] != '----') {
                        $leaf['attributes'] = ($post['nav_target'] == 'false') ?
                            ['onclick' => 'return false;'] : ['target' => $post['nav_target']];
                    }
                    if ($post['nav_action'] == 'prepend') {
                        $currentTree = array_merge([$leaf], $currentTree);
                    } else {
                        $currentTree[] = $leaf;
                    }
                }
            }

            // Remove nav items
            if (isset($post['rm_nav']) && (count($post['rm_nav']) > 0)) {
                foreach ($post['rm_nav'] as $object) {
                    $objectAry = explode('_', $object);
                    //$node      = [];
                    foreach ($objectAry as $key => $index) {
                        if ($key == 0) {
                            if ($key == (count($objectAry) - 1)) {
                                unset($currentTree[$index]);
                            } else {
                                $node = &$currentTree[$index];
                            }
                        } else if (isset($node['children']) && isset($node['children'][$index])) {
                            if ($key == (count($objectAry) - 1)) {
                                unset($node['children'][$index]);
                            } else {
                                $node = &$node['children'][$index];
                            }
                        }
                    }
                }
            }
        // Order the nav items
        } else {
            $order = [];
            foreach ($post as $key => $value) {
                if (strpos($key, 'leaf_order_') !== false) {
                    $leaf = substr($key, 11);
                    if (strpos($leaf, '_') !== false) {
                        $leaf = substr($leaf, 0, -1) . '*';
                    } else {
                        $leaf = '_';
                    }
                    if (!isset($order[$leaf])) {
                        $order[$leaf] = [];
                    }
                    $order[$leaf][$value - 1] = count($order[$leaf]);
                }
            }

            foreach ($order as $leaf => $value) {
                ksort($order[$leaf]);
            }

            arsort($order, SORT_NUMERIC);

            foreach ($order as $leaf => $value) {
                if ($leaf == '_') {
                    $newTree = [];
                    foreach ($order[$leaf] as $key) {
                        $newTree[] = $currentTree[$key];
                    }
                    $currentTree = $newTree;
                } else {
                    $leafAry = explode('_', $leaf);
                    array_pop($leafAry);
                    foreach ($leafAry as $key => $index) {
                        if ($key == 0) {
                            $node = &$currentTree[$index];
                        } else {
                            $node = &$node['children'][$index];
                        }
                        if ($key == count($leafAry) - 1) {
                            $newNode = [];
                            foreach ($order[$leaf] as $key) {
                                $newNode[] = &$node['children'][$key];
                            }
                            $node['children'] = $newNode;
                        }
                    }
                }
            }
        }

        return serialize($currentTree);
    }

}
