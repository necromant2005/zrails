<?php


/**
 ** @see Zend_Db_Table
 */
require_once 'Zend/Db/Table.php';


/**
 ** @see Zrails_Db_Table_Rowset
 */
require_once 'Zend/Db/Table/Rowset.php';

/**
 ** @see Zrails_Db_Table_Row
 */
require_once 'Zend/Db/Table/Row.php';


/**
 * Class for connecting to cluster databases and performing common operations.
 *
 * @category   Zrails
 * @package    Zrails_Db
 * @subpackage Adapter
 * @author     necromant2005@gmail.com
 * @copyright  necromant2005 (http://necromant2005.blogspot.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zrails_Db_Table extends Zend_Db_Table_Abstract
{
    const REF_WHERE = "where";

    /**
     * Prefix of row classes
     *
     * @var string
     */
    protected static $_rowClassPrefix = "Default_Model_";

    /**
     * The primary key column or columns.
     * A compound key should be declared as an array.
     * You may declare a single-column primary key
     * as a string.
     *
     * @var mixed
     */
    protected $_primary = 'id';

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Zrails_Db_Table_Row';

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Zrails_Db_Table_Rowset';

    /**
     * Relationship for many to many dependence tables
     *
     * @var array
     * @example array('Photos' => 'Photos_Tags')
     */
    protected $_manyToManyTables = array();

    /**
     * Collectin of additional fields for form generator
     *
     * @var array
     * @example
     *  array(
     *   "NameOfField" => array(
     *       "Type" => "text",
     *       "Options" => array()
     * ))
     */
    protected $_additionalFormFields = array();

    /**
     * Get prefix of row classes
     *
     * @return string
     */
    public static function getRowClassPrefix()
    {
        return self::$_rowClassPrefix;
    }

    /**
     * Set prefix of row classes
     *
     * @param string $prefix
     * @return string
     */
    public static function setRowClassPrefix($prefix)
    {
        return self::$_rowClassPrefix = $prefix;
    }

    /**
     * Initialize table and schema names.
     *
     * If the table name is not set in the class definition,
     * use the class name itself as the table name.
     *
     * A schema name provided with the table name (e.g., "schema.table") overrides
     * any existing value for $this->_schema.
     *
     * @return void
     */
    protected function _setupTableName()
    {
        $this->_name = strtolower(str_replace(self::getRowClassPrefix(), '', get_class($this)));
    }

    /**
     * Fetches rows by primary key or create if cant found.  The argument specifies one or more primary
     * key value(s).  To find multiple rows by primary key, the argument must
     * be an array.
     *
     * This method accepts a variable number of arguments.  If the table has a
     * multi-column primary key, the number of arguments must be the same as
     * the number of columns in the primary key.  To find multiple rows in a
     * table with a multi-column primary key, each argument must be an array
     * with the same number of elements.
     *
     * The find() method always returns a Rowset object, even if only one row
     * was found.
     *
     * @param  mixed $key The value(s) of the primary keys.
     * @return Zrails_Db_Table_Row Row matching the criteria.
     * @throws Zend_Db_Table_Exception
     */
    public function findOrCreateRow($id)
    {
        if ($obj=$this->findRow($id)) return $obj;
        return $this->createRow();
    }

    /**
     * Fetches rows by primary key.  The argument specifies one or more primary
     * key value(s).  To find multiple rows by primary key, the argument must
     * be an array.
     *
     * This method accepts a variable number of arguments.  If the table has a
     * multi-column primary key, the number of arguments must be the same as
     * the number of columns in the primary key.  To find multiple rows in a
     * table with a multi-column primary key, each argument must be an array
     * with the same number of elements.
     *
     * The find() method always returns a Rowset object, even if only one row
     * was found.
     *
     * @param  mixed $key The value(s) of the primary keys.
     * @return Zrails_Db_Table_Row Row matching the criteria or Null.
     * @throws Zend_Db_Table_Exception
     */
    public function findRow($id)
    {
        return $this->find($id)->current();
    }

    /**
     * Caclculate count rows by criteria
     *
     * @param  mixed $where array or string of matches the criteria.
     * @return Zend_Db_Table_Rowset_Abstract Row(s) matching the criteria.
     * @throws Zend_Db_Table_Exception
     */
    protected function _calculationCountRows($where="")
    {
        // selection tool
        $select = $this->_db->select();

        // the FROM clause
        $select->from($this->_name, new Zend_Db_Expr("count(*)"));

        // the WHERE clause
        $where = (array) $where;
        foreach ($where as $key => $val) {
            // is $key an int?
            if (is_int($key)) {
                // $val is the full condition
                $select->where($val);
            } else {
                // $key is the condition with placeholder,
                // and $val is quoted into the condition
                $select->where($key, $val);
            }
        }
        return $this->_db->fetchOne($select);
    }

    /**
     * Fetches all rows.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array $where            OPTIONAL An SQL WHERE clause.
     * @param string|array $order            OPTIONAL An SQL ORDER clause.
     * @param int          $page             OPTIONAL An page offset.
     * @param int          $count            OPTIONAL An count result count.
     * @param int          $limit            OPTIONAL An SQL LIMIT count.
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     */
    public function fetchAllPages($where="1", $order="", $page=0, $limit=10, $count=0)
    {
        if (empty($where)) $where = "1";
        if (empty($count)) $count = $this->_calculationCountRows($where);

        $page = intval($page);
        $cnt_page = ($count>0 && $limit>0) ? ceil($count/$limit) : 0;
        if (empty($page) || $cnt_page<$page) $page = 1;
        $offset = ($page-1)*$limit;
        $rows = $this->fetchAll($where, $order, $limit, $offset);

        $rows->setItemCountPerPage($limit);
        $rows->setItems($count);
        $rows->setCurrentPageNumber($page);
        $rows->setPageRange($cnt_page);

        return $rows;
    }

    /**
     * Gets the metadata information returned by Zend_Db_Adapter_Abstract::describeTable().
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }

    /**
     * Get many to many relationships for table
     *
     * @return array
     */
    public function getManyToManyTables()
    {
        return $this->_manyToManyTables;
    }

    /**
     * Get reference by column name
     *
     * @param string $name
     * @return array
     */
    public function getReferenceByColumn($name)
    {
        foreach ($this->_referenceMap as $Model) {
            foreach ($Model["columns"] as $column_name) {
                if ($name==$column_name && $Model['refTableClass']) {
                    return $this->getReference($Model['refTableClass']);
                }
            }
        }
        return null;
    }

    /**
     * Test reference by column name
     *
     * @param string $name
     * @return bool
     */
    public function hasReferenceByColumn($name)
    {
        return ($this->getReferenceByColumn($name)) ? true : false;
    }

    /**
     * Get array of additional fields for form builder
     *
     * @return array
     */
    public function getAdditionalFields()
    {
        return $this->_additionalFormFields;
    }
}

