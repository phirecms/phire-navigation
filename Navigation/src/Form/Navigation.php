<?php

namespace Navigation\Form;

use Pop\Form\Form;
use Pop\Validator;
use Navigation\Table;

class Navigation extends Form
{

    /**
     * Constructor
     *
     * Instantiate the form object
     *
     * @param  array  $fields
     * @param  string $action
     * @param  string $method
     * @return Navigation
     */
    public function __construct(array $fields, $action = null, $method = 'post')
    {
        parent::__construct($fields, $action, $method);
        $this->setAttribute('id', 'navigation-form');
        $this->setIndent('    ');
    }

}