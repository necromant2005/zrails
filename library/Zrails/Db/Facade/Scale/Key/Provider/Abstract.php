<?php
/**
 * Abstract class for scale key prodiver generator key
 *
 * @category   Zrails
 * @package    Zrails_Db
 * @subpackage Facade
 * @author     necromant2005@gmail.com
 * @copyright  necromant2005 (http://necromant2005.blogspot.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zrails_Db_Facade_Scale_Key_Provider_Abstract
{
    /**
     * Scale adapter
     *
     * @var Zrails_Db_Facade_Scale
     */
    protected $_db = null;

    /**
     * Table name
     *
     * @var string
     */
    protected $_table = "";

    /**
     * Name field who used in scale algo
     *
     * @var string
     */
    protected $_field = "";

    /**
     * Build strategy object
     *
     * @param Zrails_Db_Facade_Scale $db
     */
    public function __construct(Zrails_Db_Facade_Scale $db)
    {
        $this->_db = $db;
    }

    /**
     * Setup table name
     *
     * @param string $table
     */
    public function setTable($table)
    {
        $this->_table = $table;
    }

    /**
     * Setup scale field name
     *
     * @param string $field
     */
    public function setField($field)
    {
        $this->_field = $field;
    }

    /**
     * Get scale field
     *
     * @return string
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Generate unique id for new row in table
     *
     * @return int|string
     */
    abstract public function getUniqueId();
}

