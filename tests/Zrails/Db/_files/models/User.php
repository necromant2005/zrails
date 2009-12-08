<?php
require_once 'Zrails/Db/Table/Row.php';

class Test_Model_User extends Zrails_Db_Table_Row
{
    public function _getClassName($classname)
    {
        return parent::_getClassName($classname);
    }

    public function _getViaClassByClassForManyReference($className)
    {
        return parent::_getViaClassByClassForManyReference($className);
    }
}

