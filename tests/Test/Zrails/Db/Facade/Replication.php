<?php
class Test_Zrails_Db_Facade_Replication extends PHPUnit_Framework_TestCase
{
    private $db = null;

    protected function setUp()
    {
        $this->db = new Zrails_Db_Facade_Replication(array(
          'Masters' => array(
              'master0' => array(
                  'adapter' => 'Pdo_Mysql',
                  'params'  => array(
                      'host'     => '127.0.0.1',
                      'username' => 'root',
                      'password' => '',
                      'dbname'   => 'test_replication_master0'
                  )
              ),
              'master1' => array(
                  'adapter' => 'Pdo_Mysql',
                  'params'  => array(
                      'host'     => '127.0.0.1',
                      'username' => 'root',
                      'password' => '',
                      'dbname'   => 'test_replication_master1'
                  )
              ),
          ),
          'Slaves' => array(
              'slave0' => array(
                  'adapter' => 'Pdo_Mysql',
                  'params'  => array(
                      'host'     => '127.0.0.1',
                      'username' => 'root',
                      'password' => '',
                      'dbname'   => 'test_replication_slave0'
                  )
              ),
              'slave1' => array(
                  'adapter' => 'Pdo_Mysql',
                  'params'  => array(
                      'host'     => '127.0.0.1',
                      'username' => 'root',
                      'password' => '',
                      'dbname'   => 'test_replication_slave1'
                  )
              ),
          ),
        ));

        foreach ($this->db as $type=>$connections) {
            foreach ($connections as $connection) {
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
    }

    protected function tearDown()
    {
        $this->db = null;
    }

    public function testDbSelectSlave()
    {
        $dbSelect = $this->db->select()->from('users')->limit(1);
        $rowset = $dbSelect->query()->fetchAll();
        $this->assertTrue($this->db->isSlaveConnection());
        $this->assertEquals(count($rowset), 1);
    }

    public function testDbInsert()
    {
        $this->db->insert('users', array('name'=>'jo'));
        $this->assertTrue($this->db->isMasterConnection());
    }

    public function testDbUpdate()
    {
        $this->db->update('users', array('name'=>'joseph'));
        $this->assertTrue($this->db->isMasterConnection());
    }

    public function testDbDelete()
    {
        $this->db->delete('users');
        $this->assertTrue($this->db->isMasterConnection());
    }

    public function testConnectToAliveSlave()
    {
        $this->db->connectSlave('slave0');
        $this->assertTrue($this->db->isSlaveConnection());
    }

    public function testConnectToNoExistsKey()
    {
        $this->db->connectSlave('slave0-non-exists-key');
        $this->assertTrue($this->db->isSlaveConnection());
    }

    public function testReConnectSlaveMaserSlave()
    {
        $this->db->connectSlave('slave0');
        $this->assertTrue($this->db->isSlaveConnection());
        $this->db->connectMaster('master1');
        $this->assertTrue($this->db->isMasterConnection());
        $this->db->connectSlave('slave1');
        $this->assertTrue($this->db->isSlaveConnection());
    }
}

