<?php
class Test_Zrails_Db_Facade_Cluster extends PHPUnit_Framework_TestCase
{
    private $db = null;

    protected function setUp()
    {
        $this->db = new Zrails_Db_Facade_Cluster(array(
            'node0' => array(
                'adapter' => 'Pdo_Mysql',
                'params'  => array(
                    'host'     => '127.0.0.1',
                    'username' => 'root',
                    'password' => '',
                    'dbname'   => 'test_cluster_node0'
                )
            ),
            'node1' => array(
                'adapter' => 'Pdo_Mysql',
                'params'  => array(
                    'host'     => '127.0.0.1',
                    'username' => 'root',
                    'password' => '',
                    'dbname'   => 'test_cluster_node1'
                )
            ),
        ));

        foreach ($this->db as $connection) {
            foreach (array(
              'CREATE TABLE `users` (`name` VARCHAR(10));',
              'CREATE TABLE `users` (`name` VARCHAR(10));',
              'INSERT INTO `users` (`name`) VALUES("some-user :)");',
            ) as $query) {
                try {
                    $connection->query(new Zend_Db_Expr($query));
                } catch (Exception $e) {}
            }
        }
    }

    protected function tearDown()
    {
        $this->db = null;
    }

    public function testDbSelectSlave()
    {
        $dbSelect = $this->db->select()->from('users')->limit(1);
        $rowset = $dbSelect->query()->fetchAll();
        $this->assertTrue($this->db->isConnected());
        $this->assertEquals(count($rowset), 1);
    }

    public function testDbInsert()
    {
        $this->db->insert('users', array('name'=>'jo'));
        $this->assertTrue($this->db->isConnected());
    }

    public function testDbUpdate()
    {
        $this->db->update('users', array('name'=>'joseph'));
        $this->assertTrue($this->db->isConnected());
    }

    public function testDbDelete()
    {
        $this->db->delete('users');
        $this->assertTrue($this->db->isConnected());
    }

    public function testConnectToAliveSlave()
    {
        $this->db->connectNode('node0');
        $this->assertTrue($this->db->isConnected());
    }

    public function testConnectToNoExistsKey()
    {
        $this->db->connectNode('node-non-exists-key');
        $this->assertTrue($this->db->isConnected());
    }

    public function testReConnectBeetwenNodes()
    {
        $this->db->connectNode('node0');
        $this->assertTrue($this->db->isConnected());
        $this->db->connectNode('node1');
        $this->assertTrue($this->db->isConnected());
    }
}

