<?php
class Zrails_Db_Table_Rowset extends Zend_Db_Table_Rowset
{
    private $_currentPageNumber = 1;
    private $_pageRange         = 1;
    private $_itemCountPerPage  = 10;
    private $_items             = 10;

    public function getTable()
    {
        return $this->_table;
    }

    // SET
    public function setCurrentPageNumber($page)
    {
        $this->_currentPageNumber = $page;
    }

    public function setPageRange($count)
    {
        $this->_pageRange = $count;
    }

    public function setItemCountPerPage($limit)
    {
        $this->_itemCountPerPage = $limit;
    }

    public function setItems($count)
    {
        $this->_items = $count;
    }


    // GET
    public function getCurrentPageNumber()
    {
        return $this->_currentPageNumber;
    }

    public function getPageRange()
    {
        return $this->_pageRange;
    }

    public function getItemCountPerPage()
    {
        return $this->_itemCountPerPage;
    }

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

