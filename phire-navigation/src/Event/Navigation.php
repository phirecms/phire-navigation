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
     * Update navigation
     *
     * @param  AbstractController $controller
     * @param  Application        $application
     * @return void
     */
    public static function updateNavigation(AbstractController $controller, Application $application)
    {

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

                $controller->view()->set($name, new Nav($tree, $config));
            }
        }
    }

}
