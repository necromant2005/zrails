<?php
require_once 'Zrails/Db/Table.php';

require_once 'Zrails/Db/_files/models/User.php';

class Test_Model_Users extends Zrails_Db_Table
{
    protected $_rowClass = 'Test_Model_User';
}

