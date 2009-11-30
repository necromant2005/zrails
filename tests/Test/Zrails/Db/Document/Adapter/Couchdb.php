<?php
class Test_Zrails_Db_Document_Adapter_Couchdb extends PHPUnit_Framework_TestCase
{
    private $db = null;

    protected function setUp()
    {
        $this->db = new Zrails_Db_Document_Adapter_Couchdb(array(
            'host'   => '127.0.0.1',
            'port'   => '5984',
            'dbname' => 'test'
        ));

        try {
            $this->db->dropDatabase("test");
        } catch (Exception $e) {}
        $this->db->createDatabase("test");
        $this->db->putDocument("user:peter", array(
            "name" => "jo",
            "age"  => 34,
            "money" => 22,
        ));
    }

    protected function tearDown()
    {
        $this->db = null;
    }



    public function testConnection()
    {
        $db = new Zrails_Db_Document_Adapter_Couchdb(array("host"=>"127.0.0.1", "port"=>"5984", "dbname"=>"test"));
        $this->assertEquals(get_class($db->getConnection()), 'Zrails_Rest_Client');
    }

    public function testGetServerVersion()
    {
        $this->assertEquals($this->db->getServerVersion(), "0.10.0");
    }

    public function testCreateDb()
    {
        $result = $this->db->createDatabase("test2");
    }

    public function testDropDb()
    {
        $result = $this->db->dropDatabase("test2");
    }


    public function testCreatePostDocument()
    {
        $result = $this->db->postDocument(array(
                "name" => "jo",
                "age"  => 34
            ));
        $this->assertTrue($result["ok"]);
        $this->db->deleteDocument($result["id"]);
    }

    public function testCreatePutDocument()
    {
        $result = $this->db->putDocument("user:jo", array(
                "name" => "jo",
                "age"  => 34
            ));
        $this->assertTrue($result["ok"]);
    }

    public function testUpdateDocument()
    {
        $doc = $this->db->getDocument("user:peter");
        $doc["age"] = 21;
        $result = $this->db->putDocument($doc);
        $this->assertTrue($result["ok"]);
    }

    public function testDeleteDocument()
    {
        $doc = $this->db->getDocument("user:peter");
        $result = $this->db->deleteDocument($doc);
        $this->assertTrue($result["ok"]);
    }

    public function testDeleteDocumentByKey()
    {
        $result = $this->db->deleteDocument("user:peter");
        $this->assertTrue($result["ok"]);
    }

    public function testGetDocumentRevisions()
    {
        $revisions = array();
        $doc = $this->db->getDocument("user:peter");
        $doc["age"] = 21;
        $result = $this->db->putDocument($doc);

        list($temp, $rev) = explode("-", $result["rev"]);
        $revisions[] = $rev;
        list($temp, $rev) = explode("-", $doc["_rev"]);
        $revisions[] = $rev;

        $this->assertEquals($this->db->getDocumentRevisions("user:peter"), $revisions);
    }

    public function testGetDocumentRevision()
    {
        $revisions = array();
        $doc = $this->db->getDocument("user:peter");
        $result = $this->db->putDocument(array_merge($doc, array("age"=>21)));

        $this->assertEquals($this->db->getDocument("user:peter",  $doc["_rev"]), $doc);
    }

    public function testgetAllDocuments()
    {
        $docs = $this->db->getAllDocuments();
        $this->assertEquals(count($docs), 1);
        $doc = current($docs);
        $this->assertEquals($doc["id"], "user:peter");
    }

    public function testgetAllDocumentsBySeq()
    {
        $result = $this->db->postDocument(array(
                "name" => "jo",
                "age"  => 34
            ));
        $this->db->deleteDocument($result["id"]);
        $docs = $this->db->getAllDocumentsBySeq();
        $this->assertEquals(count($docs), 2);
    }

    public function testQuery()
    {
        $this->db->postDocument(array(
                "name" => "jo",
                "age"  => 21
        ));
        $docs = $this->db->query("
            function(doc) {
                if (doc.age<30) {
                    emit(null, doc);
                }
            }");
        $this->assertEquals(count($docs), 1);
        $doc = current($docs);

        $this->assertEquals($doc["value"]["age"], 21);
        $this->assertEquals($doc["value"]["name"], "jo");
    }

    public function testQueryMap()
    {
        $this->db->postDocument(array(
                "name" => "jo",
                "age"  => 21,
                "money"=> 55,
        ));
        $docs = $this->db->query(array(
            "map" => "
                    function(doc) {
                            emit(null, doc.money);
                    }",
            "reduce" => "
                    function(keys, values) {
                        return sum(values)
                    }"
            ));
        $this->assertEquals($docs[0]["value"], "77");
    }

    public function testCreateQueryView()
    {
        $this->db->createView("testing", "all", "
            function(doc) {
                    emit(null, doc);
            }");

        $docs = $this->db->queryView("testing", "all");
        $doc = current($docs);
        $this->assertEquals($doc["id"], "user:peter");
    }


    public function testViews()
    {
        $this->db->postDocument(array(
                "name" => "jeck",
                "age"  => 21,
                "money"=> 55,
        ));

        $this->db->createView("testing", "all", "
            function(doc) {
                    emit(null, doc);
            }");
        $this->db->createView("testing", "age_more_30", "
            function(doc) {
                if (doc.age>30) {
                    emit(null, doc);
                }
            }");
        $this->db->createView("testing", "total_age", array(
                "map" => "
                    function(doc) {
                            emit(null, doc.money);
                    }",
                "reduce" => "
                    function(keys, values) {
                        return sum(values)
                    }"
            ));

        $docs = $this->db->queryView("testing", "all");
        $doc = current($docs);
        $this->assertEquals($doc["value"]["name"], "jeck");

        $docs = $this->db->queryView("testing", "age_more_30");
        $doc = current($docs);
        $this->assertEquals($doc["value"]["name"], "jo");

        $docs = $this->db->queryView("testing", "total_age");
        $doc = current($docs);
        $this->assertEquals($doc["value"], 77);
    }
}

