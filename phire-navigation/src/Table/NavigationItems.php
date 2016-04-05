<?php

namespace Phire\Navigation\Table;

use Pop\Db\Record;

class NavigationItems extends Record
{

    /**
     * Table prefix
     * @var string
     */
    protected $prefix = DB_PREFIX;

    /**
     * Primary keys
     * @var array
     */
    protected $primaryKeys = ['id'];

}