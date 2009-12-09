<?php
/**
 ** @see Zrails_Db_Table_Rowset
 */
require_once 'Zend/Db/Table/Rowset.php';

/**
 * Class rowset of objects Zrails_Db_Table_Row
 *
 * @category   Zrails
 * @package    Zrails_Db
 * @subpackage Adapter
 * @author     necromant2005@gmail.com
 * @copyright  necromant2005 (http://necromant2005.blogspot.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zrails_Db_Table_Rowset extends Zend_Db_Table_Rowset
{
    /**
     * Current page number
     *
     * @var int
     */
    private $_currentPageNumber = 1;

    /**
     * Page range
     *
     * @var int
     */
    private $_pageRange         = 1;

    /**
     * Count items per page
     *
     * @var int
     */
    private $_itemCountPerPage  = 10;

    /**
     * Count items
     *
     * @var int
     */
    private $_items             = 10;

    /**
     * Zend_Db_Table_Abstract parent class or instance.
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getTable()
    {
        return $this->_table;
    }

    // SET
    /**
     * Set current page number
     *
     * @param int $page
     */
    public function setCurrentPageNumber($page)
    {
        $this->_currentPageNumber = $page;
    }

    /**
     * Set range of pages
     *
     * @param int $count
     */
    public function setPageRange($count)
    {
        $this->_pageRange = $count;
    }

    /**
     * Set count item per page
     *
     * @param int $limit
     */
    public function setItemCountPerPage($limit)
    {
        $this->_itemCountPerPage = $limit;
    }

    /**
     * Set count items
     *
     * @param int $count
     */
    public function setItems($count)
    {
        $this->_items = $count;
    }


    // GET
    /**
     * Get current page number
     *
     * @return int
     */
    public function getCurrentPageNumber()
    {
        return $this->_currentPageNumber;
    }

    /**
     * Get page range
     *
     * @return int
     */
    public function getPageRange()
    {
        return $this->_pageRange;
    }

    /**
     * Get count item per page
     *
     * @return int
     */
    public function getItemCountPerPage()
    {
        return $this->_itemCountPerPage;
    }

    /**
     * Get count items
     *
     * @return int
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Is need show pages(if count page>1)
     *
     * @return bool
     */
    public function isMoreThanOnePage()
    {
          return ( $this->_pageRange > 1 ) ? true : false;
    }

    /**
     * Is not need show pages (if count page<=1)
     *
     * @return bool
     */
    public function isLessThanOnePage()
    {
          return !$this->isMoreThanOnePage();
    }
}

