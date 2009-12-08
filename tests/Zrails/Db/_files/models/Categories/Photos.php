<?php
require_once 'Zrails/Db/Table.php';

class Test_Model_Categories_Photos extends Zrails_Db_Table
{
    protected $_primary = array("сategory_id", "photo_id");

    protected $_referenceMap = array(
        'Category' => array(
            'columns'       => array('category_id'),
            'refTableClass' => 'Test_Model_Categories',
            'refColumns'    => array('id')
        ),
        'Photo' => array(
            'columns'       => array('photo_id'),
            'refTableClass' => 'Test_Model_Users_Photos',
            'refColumns'    => array('id')
        ),
    );
/*
    protected $_referenceMap    = array(
        'Model1' => array(
            'columns'           => array('category_id'),
            'refTableClass'     => 'Test_Model_Categories',
            'refColumns'        => array('id')
        ),
        'Model2' => array(
            'columns'           => array('photo_id'),
            'refTableClass'     => 'Test_Model_Users_Photos',
            'refColumns'        => array('id')
        )
    );
*/
}

