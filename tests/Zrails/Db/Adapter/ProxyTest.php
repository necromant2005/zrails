<?php
//require_once 'Zrails/Db/Adapter/Proxy/Abstract.php';
require_once 'Zrails/Db/Adapter/_files/AdapterProxy.php';

class Zrails_Db_Adapter_ProxyTest extends PHPUnit_Framework_TestCase
{
    private $db = null;

    protected function setUp()
    {
        $this->_db = new AdapterProxy(array('name'=>'value'));
    }

    public function testConfig()
    {
        $this->assertEquals($this->_db->getConfig(), array('name'=>'value'));
    }
}

