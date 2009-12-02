<?php

/**
 ** @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 ** @see Zrails_Db_Adapter_Proxy_Abstract
 */
require_once 'Zrails/Db/Adapter/Proxy/Abstract.php';



/**
 * Class for connecting to cluster databases and performing common operations.
 *
 * @category   Zrails
 * @package    Zrails_Db
 * @subpackage Adapter
 * @author     necromant2005@gmail.com
 * @copyright  necromant2005 (http://necromant2005.blogspot.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zrails_Db_Facade_Cluster extends Zrails_Db_Adapter_Proxy_Abstract implements IteratorAggregate
{
    private $_connections = array();

    /**
     * Constructor.
     *
     * $config is an array of key/value pairs or an instance of Zend_Config
     * containing configuration options.  These options are common to most adapters:
     *
     * dbname         => (string) The name of the database to user
     * username       => (string) Connect to the database as this username.
     * password       => (string) Password associated with the username.
     * host           => (string) What host to connect to, defaults to localhost
     *
     * Some options are used on a case-by-case basis by adapters:
     *
     * port           => (string) The port of the database
     * persistent     => (boolean) Whether to use a persistent connection or not, defaults to false
     * protocol       => (string) The network protocol, defaults to TCPIP
     * caseFolding    => (int) style of case-alteration used for identifiers
     *
     * @param  array|Zend_Config $config An array or instance of Zend_Config having configuration data
     * @throws Zend_Db_Adapter_Exception
     */
    public function __construct($config)
    {
        /*
         * Verify that adapter parameters are in an array.
         */
        if (!is_array($config)) {
            /*
             * Convert Zend_Config argument to a plain array.
             */
            if ($config instanceof Zend_Config) {
                $config = $config->toArray();
            } else {
                /**
                 * @see Zend_Db_Adapter_Exception
                 */
                require_once 'Zend/Db/Adapter/Exception.php';
                throw new Zend_Db_Adapter_Exception('Adapter parameters must be in an array or a Zend_Config object');
            }
        }

        $this->_config = $config;

        foreach ($this->_config as $name=>$config) {
            $this->_connections[$name] = Zend_Db::factory(new Zend_Config($config));
        }
    }

    /**
     * Special method for catch all non exist method
     * Work as 1 enter point for all methods call
     *
     * @param $method string; call method name
     * @param $args array; input arguments array
     * @return mixed
     */
    public function __call($method, $args)
    {
        $this->_connect();
        return call_user_func_array(array($this->_connection, $method), $args);
    }

    /**
     * Creates a connection to the database.
     *
     * @return void
     */
    protected function _connect()
    {
        // if we already have a PDO object, no need to re-connect.
        if ($this->_connection) {
            return;
        }
        $this->connectNode();
    }

    public function connectNode($name=null)
    {
        while (count($this->_connections)>0) {
            if (is_null($name) || !array_key_exists($name, $this->_connections)) {
                $name = array_rand($this->_connections);
            }
            $this->_connection = $this->_connections[$name];
            try {
                $this->_connection->getConnection();
                return ;
            } catch (Zend_Db_Adapter_Exception $e) {
                unset($this->_connections[$name]);
                $name = null;
                continue;
            }
        }
        throw new Zend_Db_Adapter_Exception('Cant connect to any node');
    }


    /**
     * Close current opened connection to master or slave
     *
     */
    public function closeConnection()
    {
        return $this->__call(__FUNCTION__, array());
    }

    /**
     * Test if a connection is active
     *
     * @return boolean
     */
    public function isConnected()
    {
        return is_object($this->_connection);
    }


    /**
     * Set the fetch mode.
     *
     * @param integer $mode
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    public function setFetchMode($mode)
    {
        foreach ($this as $connection) {
          $connection->setFetchMode($mode);
        }
    }

    public function getIterator()
    {
        return new ArrayObject($this->_connections);
    }
}

