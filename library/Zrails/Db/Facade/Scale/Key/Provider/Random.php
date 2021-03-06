<?php

/**
 ** @see Zrails_Db_Facade_Scale_Key_Provider_Abstract
 */
require_once 'Zrails/Db/Facade/Scale/Key/Provider/Abstract.php';

/**
 * Class for scale key prodiver generator of random new key
 *
 * @category   Zrails
 * @package    Zrails_Db
 * @subpackage Facade
 * @author     necromant2005@gmail.com
 * @copyright  necromant2005 (http://necromant2005.blogspot.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zrails_Db_Facade_Scale_Key_Provider_Random extends Zrails_Db_Facade_Scale_Key_Provider_Abstract
{
    /**
     * Generate unique id for new row in table
     *
     * @return int|string
     */
    public function getUniqueId()
    {
        do {
            $uid = mt_rand();
            $dbSelect =
                $this->_db
                ->select()
                ->from($this->_table)
                ->where($this->_db->quoteInto($this->_field . '=?', $uid));
        } while($this->_db->query("$dbSelect")->rowCount());
        return $uid;
    }
}

