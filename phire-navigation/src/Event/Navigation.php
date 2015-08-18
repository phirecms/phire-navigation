<?php

namespace Phire\Navigation\Event;

use Phire\Navigation\Model;
use Phire\Navigation\Table;
use Pop\Application;
use Pop\Nav\Nav;
use Pop\Filter\Slug;
use Phire\Controller\AbstractController;

class Navigation
{

    /**
     * Update the navigation objects
     *
     * @param  AbstractController $controller
     * @param  Application        $application
     * @return void
     */
    public static function updateNavigation(AbstractController $controller, Application $application)
    {
        if (($_POST) && $application->isRegistered('phire-content') && ($controller->hasView()) && (null !== $controller->view()->id) &&
            (null !== $controller->view()->form) && ($controller->view()->form instanceof \Phire\Content\Form\Content)) {
            $title = $controller->view()->form->title;
            $uri   = $controller->view()->form->uri;
            $id    = $controller->view()->id;
            $roles = (isset($_POST['roles']) ? $_POST['roles'] : null);

            $navigation = Table\Navigation::findAll();
            foreach ($navigation->rows() as $nav) {
                $tree = (null !== $nav->tree) ? unserialize($nav->tree) : [];
                if (count($tree) > 0) {
                    self::traverseTree($tree, $id, $title, $uri, 'content', $roles);
                    $revisedNav = Table\Navigation::findById($nav->id);
                    if (isset($revisedNav->id)) {
                        $revisedNav->tree = serialize($tree);
                        $revisedNav->save();
                    }
                }
            }
        } else if (($_POST) && $application->isRegistered('phire-categories') && ($controller->hasView()) && (null !== $controller->view()->id) &&
            (null !== $controller->view()->form) && ($controller->view()->form instanceof \Phire\Categories\Form\Category)) {
            $title = $controller->view()->form->title;
            $uri   = '/category' . $controller->view()->form->uri;
            $id    = $controller->view()->id;

            $navigation = Table\Navigation::findAll();
            foreach ($navigation->rows() as $nav) {
                $tree = (null !== $nav->tree) ? unserialize($nav->tree) : [];
                if (count($tree) > 0) {
                    self::traverseTree($tree, $id, $title, $uri, 'category');
                    $revisedNav = Table\Navigation::findById($nav->id);
                    if (isset($revisedNav->id)) {
                        $revisedNav->tree = serialize($tree);
                        $revisedNav->save();
                    }
                }
            }
        }
    }

    /**
     * Set the navigation objects
     *
     * @param  AbstractController $controller
     * @param  Application        $application
     * @return void
     */
    public static function getNavigation(AbstractController $controller, Application $application)
    {
        if ((($application->isRegistered('phire-categories') && ($controller instanceof \Phire\Categories\Controller\IndexController)) ||
            (($application->isRegistered('phire-content') && ($controller instanceof \Phire\Content\Controller\IndexController))) &&
            ($controller->hasView()))) {
            $navigation = Table\Navigation::findAll();
            foreach ($navigation->rows() as $nav) {
                $tree   = (null !== $nav->tree) ? unserialize($nav->tree) : [];
                $slug   = Slug::filter($nav->title);
                $name   = str_replace('-', '_', $slug);
                $topId  = ((empty($nav->top_id)) ? $slug : $nav->top_id);
                $config = [];

                if (!empty($nav->on_class)) {
                    $config['on'] = $nav->on_class;
                }
                if (!empty($nav->off_class)) {
                    $config['off'] = $nav->off_class;
                }

                $config['top'] = ['id' => $topId];

                if (!empty($nav->top_node)) {
                    $config['top']['node'] = $nav->top_node;
                }
                if (!empty($nav->top_class)) {
                    $config['top']['class'] = $nav->top_class;
                }
                if (!empty($nav->top_attributes)) {
                    $attribs   = explode('" ', $nav->top_attributes);
                    $attribAry = [];
                    foreach ($attribs as $att) {
                        $val = explode('="', $att);
                        $attribAry[trim($val[0])] = trim($val[1]);
                    }
                    $config['top']['attributes'] = $attribAry;
                }

                if (!empty($nav->parent_node)) {
                    if (!isset($config['parent'])) {
                        $config['parent'] = [];
                    }
                    $config['parent']['node'] = $nav->parent_node;
                }

                if (!empty($nav->parent_id)) {
                    if (!isset($config['parent'])) {
                        $config['parent'] = [];
                    }
                    $config['parent']['id'] = $nav->parent_id;
                }
                if (!empty($nav->parent_class)) {
                    if (!isset($config['parent'])) {
                        $config['parent'] = [];
                    }
                    $config['parent']['class'] = $nav->parent_class;
                }
                if (!empty($nav->parent_attributes)) {
                    if (!isset($config['parent'])) {
                        $config['parent'] = [];
                    }
                    $attribs   = explode('" ', $nav->parent_attributes);
                    $attribAry = [];
                    foreach ($attribs as $att) {
                        $val = explode('="', $att);
                        $attribAry[trim($val[0])] =  trim($val[1]);
                    }
                    $config['parent']['attributes'] = $attribAry;
                }

                if (!empty($nav->child_node)) {
                    if (!isset($config['child'])) {
                        $config['child'] = [];
                    }
                    $config['child']['node'] = $nav->child_node;
                }

                if (!empty($nav->child_id)) {
                    if (!isset($config['child'])) {
                        $config['child'] = [];
                    }
                    $config['child']['id'] = $nav->child_id;
                }
                if (!empty($nav->child_class)) {
                    if (!isset($config['child'])) {
                        $config['child'] = [];
                    }
                    $config['child']['class'] = $nav->child_class;
                }
                if (!empty($nav->child_attributes)) {
                    if (!isset($config['child'])) {
                        $config['child'] = [];
                    }
                    $attribs   = explode('" ', $nav->child_attributes);
                    $attribAry = [];
                    foreach ($attribs as $att) {
                        $val = explode('="', $att);
                        $attribAry[trim($val[0])] =  trim($val[1]);
                    }
                    $config['child']['attributes'] = $attribAry;
                }

                if (!empty($nav->indent)) {
                    $config['indent'] = str_repeat(' ', (int)$nav->indent);
                }

                if ($application->isRegistered('phire-content')) {
                    self::checkTreeStatus($tree);
                }

                $navObject = new Nav($tree, $config);
                if ($application->services()->isAvailable('acl')) {
                    $sess = $application->services()->get('session');
                    $navObject->setAcl($application->services()->get('acl'));
                    if (isset($sess->user) && isset($sess->user->role)) {
                        $navObject->setRole($application->services()->get('acl')->getRole($sess->user->role));
                    }
                }
                $controller->view()->set($name, $navObject);
            }
        }
    }

    /**
     * Traverse tree for updates
     *
     * @param  array  $tree
     * @param  int    $id
     * @param  string $title
     * @param  string $uri
     * @param  string $type
     * @param  mixed  $roles
     * @param  int    $depth
     * @return void
     */
    public static function traverseTree(&$tree, $id, $title, $uri, $type, $roles = null, $depth = 0)
    {
        foreach ($tree as &$branch) {
            if (isset($branch['id']) && isset($branch['type']) && ($branch['id'] == $id) && ($branch['type'] == $type)) {
                $branch['name'] = $title;
                $branch['href'] = $uri;
                if ((null !== $roles) && is_array($roles) && (count($roles) > 0)) {
                    $branch['acl'] = [
                        'resource' => 'content-' . $id
                    ];
                } else if (isset($branch['acl'])) {
                    unset($branch['acl']);
                }
            }
            if (isset($branch['children']) && (count($branch['children']) > 0)) {
                self::traverseTree($branch['children'], $id, $title, $uri, $type, $roles, ($depth + 1));
            }
        }
    }

    /**
     * Check tree for status
     *
     * @param  array  $tree
     * @param  int    $depth
     * @return void
     */
    public static function checkTreeStatus(&$tree, $depth = 0)
    {
        foreach ($tree as $i => &$branch) {
            if (isset($branch['id']) && isset($branch['type']) && ($branch['type'] == 'content')) {
                $content = \Phire\Content\Table\Content::findById($branch['id']);
                if (isset($content->id) && ((int)$content->status != 1)) {
                    unset($tree[$i]);
                }
            }
            if (isset($branch['children']) && (count($branch['children']) > 0)) {
                self::checkTreeStatus($branch['children'], ($depth + 1));
            }
        }
    }

}
