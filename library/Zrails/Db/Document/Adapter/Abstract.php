<?php


/**
 ** @see Zrails_Db_Document_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Exception.php';

/**
 ** @see Zrails_Db_Document_Adapter_Abstract
 */
require_once 'Zrails/Db/Document/Adapter/Abstract.php';



/**
 * Class for connecting to Couchdb databases and performing common operations.
 *
 * @category   Zrails
 * @package    Zrails_Db
 * @subpackage Adapter
 * @author     necromant2005@gmail.com
 * @copyright  necromant2005 (http://necromant2005.blogspot.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zrails_Db_Document_Adapter_Abstract
{

    /**
     * User-provided configuration
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Query profiler object, of type Zend_Db_Profiler
     * or a subclass of that.
     *
     * @var Zend_Db_Profiler
     */
    protected $_profiler;

    /**
     * Database connection
     *
     * @var object|resource|null
     */
    protected $_connection = null;

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

        $this->_checkRequiredOptions($config);

        $driverOptions = array();

        /*
         * normalize the config and merge it with the defaults
         */
        if (array_key_exists('options', $config)) {
            // can't use array_merge() because keys might be integers
            foreach ((array) $config['options'] as $key => $value) {
                $options[$key] = $value;
            }
        }
        if (array_key_exists('driver_options', $config)) {
            if (!empty($config['driver_options'])) {
                // can't use array_merge() because keys might be integers
                foreach ((array) $config['driver_options'] as $key => $value) {
                    $driverOptions[$key] = $value;
                }
            }
        }

        if (!isset($config['persistent'])) {
            $config['persistent'] = false;
        }

        $this->_config = array_merge($this->_config, $config);
        $this->_config['options'] = $options;
        $this->_config['driver_options'] = $driverOptions;
    }

    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _checkRequiredOptions(array $config)
    {

        // we need at least a host
        if (! array_key_exists('host', $config)) {
            require_once 'Zend/Db/Adapter/Exception.php';
            throw new Zend_Db_Adapter_Exception("Configuration array must have a key for 'dbname' that names the database instance");
        }

        // we need at least a port
        if (! array_key_exists('port', $config)) {
            require_once 'Zend/Db/Adapter/Exception.php';
            throw new Zend_Db_Adapter_Exception("Configuration array must have a key for 'dbname' that names the database instance");
        }

        // we need at least a dbname
        if (! array_key_exists('dbname', $config)) {
            require_once 'Zend/Db/Adapter/Exception.php';
            throw new Zend_Db_Adapter_Exception("Configuration array must have a key for 'dbname' that names the database instance");
        }
        $this->_dbname = $config['dbname'];
    }

    /**
     * Abstract get document by id
     *
     * @param string $id unique document identificator
     * @param string @rev revision number
     * @return array
     */
    abstract public function getDocument($id, $rev="");

    /**
     * Abstract post document with autogenerated id
     *
     * @param array @data data for post
     * @return array
     */
    abstract public function postDocument(array $data=array());

    /**
     * Abstract put document with id
     *
     * @param string $id unique document identificator
     * @param array  @data for put
     * @return array
     */
    abstract public function putDocument($id, array $data=array());

    /**
     * Abstract delete document with id
     *
     * @param string $id unique document identificator
     * @param string @rev revision number
     * @param array  @data for put
     * @return array
     */
    abstract public function deleteDocument($id, $rev="", array $data=array());

    /**
     * Prepares and executes an SQL statement with bound data.
     *
     * @param  mixed  $query  The query (usualy map-reduce)
     * @return Zend_Db_Statement_Interface
     */
    abstract public function query($query);

    /**
     * Abatract init the connection.
     *
     * @return void
     */
    abstract protected function _connect();

    /**
     * Abatract get current connection.
     *
     * @return mixed
     */
    abstract protected function getConnection();

    /**
     * Abstract force the connection to close.
     *
     * @return void
     */
    abstract public function closeConnection();

    /**
     * Check connection
     *
     * @return bool
     */
    abstract public function isConnected();


    /**
     * Abstract get current database server version
     *
     * @return string
     */
    abstract public function getServerVersion();

}

