<?php
ob_start();

set_include_path(implode(PATH_SEPARATOR, array(
    realpath('../library'),
    get_include_path(),
)));

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zrails/Db/Document/Adapter/Couchdb.php';
require_once 'Test/Zrails/Db/Document/Adapter/Couchdb.php';

class Suite extends PHPUnit_Framework_TestSuite
{
	  public function __construct()
	  {
		    $this->setName('Suite Document Adapter Couchdb');
		    $this->addTestSuite('Test_Zrails_Db_Document_Adapter_Couchdb');
    }

	  public static function suite() {
  		  return new self();
	  }
}

