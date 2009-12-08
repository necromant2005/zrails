<?php
class Zrails_Db_Table_Rowset extends Zend_Db_Table_Rowset
{
    private $_page = 1;
    private $_page_limit = 10;
    private $_page_count = 10;
    private $_page_count_pages = 10;

    public function getTable()
    {
        return $this->_table;
    }

    // SET
    public function setCurrentPageNumber($page)
    {
        $this->_page = $page;
    }

    public function setPageRange($count)
    {
        $this->_page_count_pages = $count;
    }

    public function setItemCountPerPage($limit)
    {
        $this->_page_limit = $limit;
    }

    public function setItems($count)
    {
        $this->_page_count = $count;
    }


    // GET
    public function getCurrentPageNumber()
    {
        return $this->_page;
    }

    public function getPageRange()
    {
        return $this->_page_count_pages;
    }

    public function getItemCountPerPage()
    {
        return $this->_page_limit;
    }

    public function getItems()
    {
        return $this->_page_count;
    }

    /**
     * Is need show pages(if count page>1)
     *
     * @return bool
     */
    public function isMoreThanOnePage()
    {
          return ($this->_page_count_pages>1) ? true : false;
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

