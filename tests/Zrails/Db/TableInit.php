<?php
require_once 'Zrails/Db/Table.php';

require_once 'Zrails/Db/_files/models/Users.php';
require_once 'Zrails/Db/_files/models/Users/Photos.php';
require_once 'Zrails/Db/_files/models/Categories.php';
require_once 'Zrails/Db/_files/models/Categories/Photos.php';

class Zrails_Db_TableInit extends PHPUnit_Framework_TestCase
{
    protected $users = null;
    protected $categories = null;
    protected $usersPhotos = null;
    protected $categoriesPhotos = null;

    protected $db = null;

    protected $fixtures = array(
        'users' => array(
            array('id'=>1, 'name'=>'jo'),
            array('id'=>2, 'name'=>'peter'),
        ),
        'users_photos' => array(
            array('id'=>1, 'user_id'=>1, 'name'=>'jo_photo1'),
            array('id'=>2, 'user_id'=>1, 'name'=>'jo_photo2'),
            array('id'=>3, 'user_id'=>1, 'name'=>'jo_photo3'),
            array('id'=>4, 'user_id'=>2, 'name'=>'peter_photo'),
        ),
        'categories' => array(
            array('id'=>1, 'name'=>'people'),
            array('id'=>2, 'name'=>'wild'),
        ),
        'categories_photos' => array(
            array('category_id'=>1, 'photo_id'=>1),
            array('category_id'=>1, 'photo_id'=>2),
            array('category_id'=>2, 'photo_id'=>2),
            array('category_id'=>1, 'photo_id'=>3),
            array('category_id'=>1, 'photo_id'=>4),
        ),
    );

    protected function setUp()
    {
        $this->db = Zend_Db::factory('Pdo_Mysql', array(
          'host'     => '127.0.0.1',
          'dbname'   => 'test',
          'username' => 'root',
          'password' => '',
        ));

        foreach ($this->fixtures as $table=>$inserts) {
            $this->db->delete($table);
            foreach ($inserts as $insert) {
                $this->db->insert($table, $insert);
            }
        }
        Zrails_Db_Table::setRowClassPrefix('Test_Model_');

        $this->users = new Test_Model_Users($this->db);
        $this->usersPhotos = new Test_Model_Users_Photos($this->db);
        $this->categories = new Test_Model_Categories($this->db);
        $this->categoriesPhotos = new Test_Model_Categories_Photos($this->db);
    }

    public function testEmpty()
    {
        $this->assertTrue(true);
    }
}

