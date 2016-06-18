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
namespace Phire\Navigation\Event;

use Phire\Navigation\Model;
use Phire\Navigation\Table;
use Pop\Application;
use Pop\Nav\Nav;
use Pop\Filter\Slug;
use Phire\Controller\AbstractController;

/**
 * Navigation Event class
 *
 * @category   Phire\Navigation
 * @package    Phire\Navigation
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.phirecms.org/license     New BSD License
 * @version    1.0.0
 */
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
            (null !== $controller->view()->form) && ($controller->view()->form !== false) && ($controller->view()->form instanceof \Phire\Content\Form\Content)) {
            $navItems = Table\NavigationItems::findBy(['type' => 'content']);
            foreach ($navItems->rows() as $item) {
                if (null !== $item->item_id) {
                    $i = \Phire\Content\Table\Content::findById($item->item_id);
                    if (isset($i->id)) {
                        $n = Table\NavigationItems::findById($item->id);
                        if (isset($n->id)) {
                            $n->name = $i->title;
                            $n->href = $i->uri;
                            $n->save();
                        }
                    }
                }
            }
        } else if (($_POST) && $application->isRegistered('phire-categories') && ($controller->hasView()) && (null !== $controller->view()->id) &&
            (null !== $controller->view()->form) && ($controller->view()->form !== false) && ($controller->view()->form instanceof \Phire\Categories\Form\Category)) {
            $navItems = Table\NavigationItems::findBy(['type' => 'category']);
            foreach ($navItems->rows() as $item) {
                if (null !== $item->item_id) {
                    $i = \Phire\Categories\Table\Categories::findById($item->item_id);
                    if (isset($i->id)) {
                        $n = Table\NavigationItems::findById($item->id);
                        if (isset($n->id)) {
                            $n->name = $i->title;
                            $n->href = '/category' . $i->uri;
                            $n->save();
                        }
                    }
                }
            }
        }

        if (($_POST) && isset($_POST['process_content']) && (count($_POST['process_content']) > 0)
            && isset($_POST['content_process_action']) && ($_POST['content_process_action'] == -3)) {
            foreach ($_POST['process_content'] as $id) {
                $navItems = new Table\NavigationItems();
                $navItems->delete(['type' => 'content', 'item_id' => $id]);
            }
        }

        if (($_POST) && isset($_POST['rm_categories']) && (count($_POST['rm_categories']) > 0)) {
            foreach ($_POST['rm_categories'] as $id) {
                $navItems = new Table\NavigationItems();
                $navItems->delete(['type' => 'category', 'item_id' => $id]);
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
                $tree   = (new Model\Navigation())->getTree($nav->id);
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
                    $sess   = $application->services()->get('session');
                    $roleId = (isset($sess->user) && isset($sess->user->role_id)) ? $sess->user->role_id : null;
                    self::checkTreeStatus($tree, $roleId);
                }

                $navObject = new Nav($tree, $config);
                $controller->view()->set($name, $navObject);
            }
        }
    }

    /**
     * Check tree for status
     *
     * @param  array  $tree
     * @param  int    $roleId
     * @param  int    $depth
     * @return void
     */
    public static function checkTreeStatus(&$tree, $roleId = null, $depth = 0)
    {
        foreach ($tree as $i => &$branch) {
            if (isset($branch['item_id']) && isset($branch['type']) && ($branch['type'] == 'content')) {
                $content = \Phire\Content\Table\Content::findById($branch['item_id']);
                $allowed = true;
                if (null !== $content->roles) {
                    $roles = unserialize($content->roles);
                    if ((count($roles) > 0) && ((null === $roleId) || !in_array($roleId, $roles))) {
                        $allowed = false;
                    }
                }
                if (isset($content->id) && (((int)$content->status != 1) || (!$allowed))) {
                    unset($tree[$i]);
                }
            }
            if (isset($branch['children']) && (count($branch['children']) > 0)) {
                self::checkTreeStatus($branch['children'], $roleId, ($depth + 1));
            }
        }
    }

}
