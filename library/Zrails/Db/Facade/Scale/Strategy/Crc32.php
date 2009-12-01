<?php

/**
 ** @see Zrails_Db_Facade_Scale_Strategy_Abstract.php
 */
require_once 'Zrails/Db/Facade/Scale/Strategy/Abstract.php';


/**
 * Abstract class for stratery of scale.
 *
 * @category   Zrails
 * @package    Zrails_Db
 * @subpackage Facade
 * @author     necromant2005@gmail.com
 * @copyright  necromant2005 (http://necromant2005.blogspot.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zrails_Db_Facade_Scale_Strategy_Crc32 extends Zrails_Db_Facade_Scale_Strategy_Abstract
{
    /**
     * Get Shard name by value
     *
     * @param string $value
     * @return string
     */
    public function getShardName($value)
    {
         return crc32("$this->_table::$value") % $this->_db->getCountShards();
    }
}

