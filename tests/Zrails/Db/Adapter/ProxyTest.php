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

    public function testCall()
    {
        $this->assertEquals($this->_db->__call('some', array(12, 34)), array('some'=>array(12, 34)));
    }

    public function testCloseConnection()
    {
        $this->assertNull($this->_db->closeConnection());
    }

    public function testIsConnected()
    {
        $this->assertTrue($this->_db->isConnected());
    }

    public function testConnect()
    {
        $this->assertNull($this->_db->_connect());
    }

    public function testQuery()
    {
        $this->assertEquals($this->_db->query('select * from users where id=:id', array('id'=>1)),
            array('query'=> array('select * from users where id=:id', array('id'=>1)))
        );
    }

    public function testBeginTransaction()
    {
        $this->assertEquals($this->_db->beginTransaction(), array('beginTransaction'=>array()));
    }

    public function testCommit()
    {
        $this->assertEquals($this->_db->commit(), array('commit'=>array()));
    }

    public function testRollback()
    {
        $this->assertEquals($this->_db->rollBack(), array('rollBack'=>array()));
    }

    //base insert update delete tests

    public function testInsert()
    {
        $this->assertEquals(
            $this->_db->insert('users', array('id'=>1)),
            array('insert'=>array( 'users', array('id'=>1) ) )
        );
    }

    public function testUpdate()
    {
        $this->assertEquals(
            $this->_db->update('users', array('name'=>'jo'), 'id=1'),
            array('update' => array('users', array('name'=>'jo'), 'id=1'))
        );

        $this->assertEquals(
            $this->_db->update('users', array('name'=>'jo')),
            array('update' => array('users', array('name'=>'jo'), null))
        );
    }

    public function testDelete()
    {
        $this->assertEquals($this->_db->delete('users', 'id=1'), array('delete'=>array('users', 'id=1')));
    }

    public function testSelect()
    {
        $this->assertEquals($this->_db->select(), array('select'=>array()));
    }

    public function testGetFetchMode()
    {
        $this->assertEquals($this->_db->getFetchMode(), array('getFetchMode'=>array()));
    }

    public function testSetFetchMode()
    {
        $this->assertEquals($this->_db->setFetchMode(123), array('setFetchMode'=>array(123)));
    }

    //fetch tests

    public function testFetchAll()
    {
        $this->assertEquals( $this->_db->fetchAll("1"), array( 'fetchAll'=>array( "1", array(), null ) ) );
    }

    public function testFetchRow()
    {
        $this->assertEquals( $this->_db->fetchRow("1"), array( 'fetchRow'=>array( "1", array(), null ) ) );
    }

    public function testFetchAssoc()
    {
        $this->assertEquals( $this->_db->fetchAssoc("1"), array( 'fetchAssoc'=>array( "1", array() ) ) );
    }

    public function testFetchOne()
    {
        $this->assertEquals( $this->_db->fetchOne("1"), array( 'fetchOne'=>array( "1", array() ) ) );
    }

    public function testFetchCol()
    {
        $this->assertEquals( $this->_db->fetchCol("1"), array( 'fetchCol'=>array( "1", array() ) ) );
    }

    public function testFetchPairs()
    {
        $this->assertEquals( $this->_db->fetchPairs("1"), array( 'fetchPairs'=>array( "1", array() ) ) );
    }

    //quote tests

    public function testQuote()
    {
        $this->assertEquals( $this->_db->quote("1"), array( 'quote'=>array( "1", null ) ) );
    }

    public function testQuoteInto()
    {
        $this->assertEquals( $this->_db->quoteInto("id=?", 1), array( 'quoteInto'=>array( "id=?", 1, null, null ) ) );
    }

    public function testQuoteIdentifier()
    {
        $this->assertEquals( $this->_db->quoteIdentifier("id=?"), array( 'quoteIdentifier'=>array( "id=?", null ) ) );
    }

    public function testQuoteColumntAs()
    {
        $this->assertEquals( $this->_db->quoteColumnAs("id=?", 'id'), array( 'quoteColumnAs'=>array( "id=?", 'id', false ) ) );
    }

    public function testQuoteTableAs()
    {
        $this->assertEquals( $this->_db->quoteTableAs("id=?"), array( 'quoteTableAs'=>array( "id=?", null, false ) ) );
    }

    public function testGetQuoteIdentifierSymbol()
    {
        $this->assertEquals( $this->_db->getQuoteIdentifierSymbol(), array( 'getQuoteIdentifierSymbol'=>array() ) );
    }

    //sequences tests

    public function testLastSequenceId()
    {
        $this->assertEquals( $this->_db->lastSequenceId('name'), array( 'lastSequenceId'=>array('name') ) );
    }

    public function testNextSequenceId()
    {
        $this->assertEquals( $this->_db->nextSequenceId('name'), array( 'nextSequenceId'=>array('name') ) );
    }

    public function testFoldCase()
    {
        $this->assertEquals( $this->_db->foldCase('name'), array( 'foldCase'=>array('name') ) );
    }

    public function testListTables()
    {
        $this->assertEquals( $this->_db->listTables(), array( 'listTables'=>array() ) );
    }

    public function testDescribeTable()
    {
        $this->assertEquals( $this->_db->describeTable('users'), array( 'describeTable'=>array('users', null) ) );
    }

    public function testPrepare()
    {
        $this->assertEquals( $this->_db->prepare('users'), array( 'prepare'=>array('users') ) );
    }

    public function testLastInsertId()
    {
        $this->assertEquals( $this->_db->lastInsertId('users'), array( 'lastInsertId'=>array('users', null) ) );
    }

    public function testLimit()
    {
        $this->assertEquals( $this->_db->limit('users', 1), array( 'limit'=>array('users', 1, array()) ) );
    }

    public function testSupportParamaters()
    {
        $this->assertEquals( $this->_db->supportsParameters('users'), array( 'supportsParameters'=>array('users') ) );
    }

    public function testGetServerVersion()
    {
        $this->assertEquals( $this->_db->getServerVersion(), array( 'getServerVersion'=>array() ) );
    }

    //protected tests
    public function testProtectedWhereExpr()
    {
        $this->assertEquals( $this->_db->_whereExpr(1), array( '_whereExpr'=>array(1) ) );
    }

    public function testProtectedQuote()
    {
        $this->assertEquals( $this->_db->_quote(1), array( '_quote'=>array(1) ) );
    }

    public function testProtectedQuoteIdentifierAs()
    {
        $this->assertEquals( $this->_db->_quoteIdentifierAs(1), array( '_quoteIdentifierAs'=>array(1, null, false, ' AS ') ) );
    }

    public function testProtectedQuoteIdentifier()
    {
        $this->assertEquals( $this->_db->_quoteIdentifier(1), array( '_quoteIdentifier'=>array(1, false) ) );
    }

    public function testProtectedBeginTransaction()
    {
        $this->assertEquals( $this->_db->_beginTransaction(), array( '_beginTransaction'=>array() ) );
    }

    public function testProtectedCommit()
    {
        $this->assertEquals( $this->_db->_commit(), array( '_commit'=>array() ) );
    }

    public function testProtectedRollback()
    {
        $this->assertEquals( $this->_db->_rollBack(), array( '_rollBack'=>array() ) );
    }

}

