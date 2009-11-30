<?php
ob_start();

set_include_path(implode(PATH_SEPARATOR, array(
    realpath('../library'),
    get_include_path(),
)));

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zrails/Db/Facade/Cluster.php';
require_once 'Test/Zrails/Db/Facade/Cluster.php';

class Suite extends PHPUnit_Framework_TestSuite
{
	  public function __construct()
	  {
		    $this->setName('Suite Facade Cluster');
		    $this->addTestSuite('Test_Zrails_Db_Facade_Cluster');
    }

	  public static function suite() {
  		  return new self();
	  }
}

