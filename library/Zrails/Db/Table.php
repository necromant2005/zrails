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

    protected static $_rowClassPrefix = "Default_Model_";

    protected $_name = "";

    protected $_primary = 'id';

    protected $_rowClass = 'Zrails_Db_Table_Row';

    protected $_rowsetClass = 'Zrails_Db_Table_Rowset';

    protected $_manyToManyTables = array();

    /*=>array(
        "NameOfField" => array(
            "Type" => "text",
            "Options" => array()
        )
    )*/
    protected $_additional_fields = array();


    public static function getRowClassPrefix()
    {
        return self::$_rowClassPrefix;
    }

    public static function setRowClassPrefix($prefix)
    {
        return self::$_rowClassPrefix = $prefix;
    }


    protected function _setupTableName()
    {
        $this->_name = strtolower(str_replace(self::getRowClassPrefix(), '', get_class($this)));
    }

    public function findOrCreateRow($id)
    {
        if ($obj=$this->findRow($id)) return $obj;
        return $this->createRow();
    }

    public function findRow($id)
    {
        return $this->find($id)->current();
    }

    private function _calculationCountRows($where="")
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

    public function getMetadata()
    {
        return $this->_metadata;
    }

    public function getManyToManyTables()
    {
        return $this->_manyToManyTables;
    }

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

    public function hasReferenceByColumn($name)
    {
        return ($this->getReferenceByColumn($name)) ? true : false;
    }

    public function getAdditionalFields()
    {
        return $this->_additional_fields;
    }
}

