<?php
require_once 'Zrails/Db/Table.php';

require_once 'Zrails/Db/_files/models/Category.php';

class Test_Model_Categories extends Zrails_Db_Table
{
    protected $_rowClass = 'Test_Model_Category';

    protected $_manyToManyTables = array('Test_Model_Users_Photos' => 'Test_Model_Categories_Photos');
}

