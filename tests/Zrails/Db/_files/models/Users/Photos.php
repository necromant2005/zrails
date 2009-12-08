<?php
require_once 'Zrails/Db/Table.php';

class Test_Model_Users_Photos extends Zrails_Db_Table
{
    protected $_referenceMap    = array(
        'key' => array(
            'columns'           => array('user_id'),
            'refTableClass'     => 'Test_Model_Users',
            'refColumns'        => array('id'),
        ));
}

