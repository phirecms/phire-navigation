<?php

namespace Navigation\Controller;

use Navigation\Model;
use Navigation\Form;
use Navigation\Table;
use Phire\Controller\AbstractController;

class IndexController extends AbstractController
{

    /**
     * Index action method
     *
     * @return void
     */
    public function index()
    {
        $this->prepareView('navigation/index.phtml');
        $navigation = new Model\Navigation();
        $this->view->title      = 'Navigation';
        $this->view->navigation = $navigation->getAll($this->request->getQuery('sort'));

        $this->send();
    }

    /**
     * Add action method
     *
     * @return void
     */
    public function add()
    {
        $this->prepareView('navigation/add.phtml');
        $this->view->title = 'Navigation : Add';

        $fields = $this->application->config()['forms']['Navigation\Form\Navigation'];

        if ($this->application->isRegistered('Content')) {
            $contentTypes = \Content\Table\ContentTypes::findAll(null, ['order' => 'order ASC']);
            foreach ($contentTypes->rows() as $contentType) {
                $fields[0]['create_nav_from']['value'][$contentType->id] = $contentType->name;
            }
        }

        $this->view->form = new Form\Navigation($fields);

        if ($this->request->isPost()) {
            $this->view->form->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
                 ->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                     ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                     ->filter();
                $navigation = new Model\Navigation();
                $navigation->save($this->view->form->getFields());
                $this->view->id = $navigation->id;
                $this->redirect(BASE_PATH . APP_URI . '/navigation/edit/'. $navigation->id . '?saved=' . time());
            }
        }

        $this->send();
    }

    /**
     * Edit action method
     *
     * @param  int $id
     * @return void
     */
    public function edit($id)
    {
        $navigation = new Model\Navigation();
        $navigation->getById($id);

        if (!isset($navigation->id)) {
            $this->redirect(BASE_PATH . APP_URI . '/navigation');
        }

        $this->prepareView('navigation/edit.phtml');
        $this->view->title         = 'Navigation';
        $this->view->navigation_title = $navigation->title;

        $fields = $this->application->config()['forms']['Navigation\Form\Navigation'];

        if ($this->application->isRegistered('Content')) {
            $contentTypes = \Content\Table\ContentTypes::findAll(null, ['order' => 'order ASC']);
            foreach ($contentTypes->rows() as $contentType) {
                $fields[0]['create_nav_from']['value'][$contentType->id] = $contentType->name;
            }
        }

        $fields[1]['title']['attributes']['onkeyup'] = 'phire.changeTitle(this.value);';
        $this->view->form = new Form\Navigation($fields);
        $this->view->form->addFilter('htmlentities', [ENT_QUOTES, 'UTF-8'])
             ->setFieldValues($navigation->toArray());

        if ($this->request->isPost()) {
            $this->view->form->setFieldValues($this->request->getPost());

            if ($this->view->form->isValid()) {
                $this->view->form->clearFilters()
                     ->addFilter('html_entity_decode', [ENT_QUOTES, 'UTF-8'])
                     ->filter();
                $navigation = new Model\Navigation();

                $navigation->update($this->view->form->getFields());
                $this->view->id = $navigation->id;
                $this->redirect(BASE_PATH . APP_URI . '/navigation/edit/'. $navigation->id . '?saved=' . time());
            }
        }

        $this->send();
    }

    /**
     * Manage action method
     *
     * @param  int $id
     * @return void
     */
    public function manage($id)
    {
        $navigation = new Model\Navigation();
        $navigation->getById($id);

        if (!isset($navigation->id)) {
            $this->redirect(BASE_PATH . APP_URI . '/navigation');
        }

        $this->prepareView('navigation/manage.phtml');
        $this->view->title            = 'Navigation : Manage';
        $this->view->navigation_title = $navigation->title;
        $this->view->id               = $navigation->id;
        $this->view->tree             = $navigation->tree;

        $this->send();
    }

    /**
     * Remove action method
     *
     * @return void
     */
    public function remove()
    {
        if ($this->request->isPost()) {
            $navigation = new Model\Navigation();
            $navigation->remove($this->request->getPost());
        }
        $this->redirect(BASE_PATH . APP_URI . '/navigation?removed=' . time());
    }

    /**
     * Prepare view
     *
     * @param  string $navigation
     * @return void
     */
    protected function prepareView($navigation)
    {
        $this->viewPath = __DIR__ . '/../../view';
        parent::prepareView($navigation);
    }

}
