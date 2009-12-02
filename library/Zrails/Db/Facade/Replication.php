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
 * Class for connecting to master-slave databases and performing common operations.
 *
 * @category   Zrails
 * @package    Zrails_Db
 * @subpackage Adapter
 * @author     necromant2005@gmail.com
 * @copyright  necromant2005 (http://necromant2005.blogspot.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zrails_Db_Facade_Replication extends Zrails_Db_Adapter_Proxy_Abstract implements IteratorAggregate
{
    const CONNECTION_MASTERS = "Masters";
    const CONNECTION_SLAVES  = "Slaves";

    private $_connections = array(self::CONNECTION_MASTERS=>array(), self::CONNECTION_SLAVES=>array());

    private $_connection_master = false;


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

        $this->_config = $config;

        foreach ($this->_config[self::CONNECTION_MASTERS] as $name=>$config) {
            $this->_connections[self::CONNECTION_MASTERS][$name] = Zend_Db::factory(new Zend_Config($config));
        }
        foreach ($this->_config[self::CONNECTION_SLAVES] as $name=>$config) {
            $this->_connections[self::CONNECTION_SLAVES][$name] = Zend_Db::factory(new Zend_Config($config));
        }
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
        // we need at least a dbname
        if (! array_key_exists(self::CONNECTION_MASTERS, $config)) {
            /** @see Zend_Db_Adapter_Exception */
            require_once 'Zend/Db/Adapter/Exception.php';
            throw new Zend_Db_Adapter_Exception("Configuration array must have a key for '".self::CONNECTION_MASTERS."' that names the database instance");
        }

        if (! array_key_exists(self::CONNECTION_SLAVES, $config)) {
            /**
             * @see Zend_Db_Adapter_Exception
             */
            require_once 'Zend/Db/Adapter/Exception.php';
            throw new Zend_Db_Adapter_Exception("Configuration array must have a key for '".self::CONNECTION_SLAVES."' for login credentials");
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

        try {
            return $this->connectSlave();
        } catch (Zend_Db_Adapter_Exception $e) {}

        $this->connectMaster();
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
     * Connect to master server
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function connectMaster($name=null)
    {
        if ($this->isMasterConnection() && $this->isConnected() && is_null($name)) return ;
        while (count($this->_connections[self::CONNECTION_MASTERS])>0) {
            if (is_null($name) || !array_key_exists($name, $this->_connections[self::CONNECTION_MASTERS])) {
                $name = array_rand($this->_connections[self::CONNECTION_MASTERS]);
            }
            $this->_connection = $this->_connections[self::CONNECTION_MASTERS][$name];
            try {
                $this->_connection->getConnection();
                $this->_connection_master = true;
                return ;
            } catch (Zend_Db_Adapter_Exception $e) {
                unset($this->_connections[self::CONNECTION_MASTERS][$name]);
                $name = null;
                continue;
            }
        }
        throw new Zend_Db_Adapter_Exception('Cant connect to any Master servers');
    }

    /**
     * Connect to slave server. If some slave's down, auto reconnect to next slave server
     *
     * @throws Zend_Db_Adapter_Exception
     */
    public function connectSlave($name=null)
    {
        if ($this->isSlaveConnection() && $this->isConnected() && is_null($name)) return ;
        while (count($this->_connections[self::CONNECTION_SLAVES])>0) {
            if (is_null($name) || !array_key_exists($name, $this->_connections[self::CONNECTION_SLAVES])) {
                $name = array_rand($this->_connections[self::CONNECTION_SLAVES]);
            }
            $this->_connection = $this->_connections[self::CONNECTION_SLAVES][$name];
            try {
                $this->_connection->getConnection();
                $this->_connection_master = false;
                return ;
            } catch (Zend_Db_Adapter_Exception $e) {
                unset($this->_connections[self::CONNECTION_SLAVES][$name]);
                $name = null;
                continue;
            }
        }
       throw new Zend_Db_Adapter_Exception('Cant connect to any Slave servers');
    }

    /**
     * Test if current connection to master
     *
     */
    public function isMasterConnection()
    {
        return $this->_connection_master;
    }

    /**
     * Test if current connection to one of slaves
     *
     */
    public function isSlaveConnection()
    {
        return (!$this->isMasterConnection());
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
     * Prepares and executes an SQL statement with bound data.
     * Auto execute Insert/Update query at master
     *
     * @param  mixed  $sql  The SQL statement with placeholders.
     *                      May be a string or Zend_Db_Select.
     * @param  mixed  $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Statement_Interface
     */
    public function query($sql, $bind = array())
    {
        $this->_connect();
        $sqlString  = (is_string($sql)) ? $sql : $sql->__toString();
        if (!preg_match('/^\s*(\w+)\s/', $sqlString, $sqlParsedCommand))
            throw new Zend_Db_Adapter_Exception('Cant parse sql [' . $sqlString . ']');
        $sqlCommand = strtolower($sqlParsedCommand[1]);
        if (in_array($sqlCommand, array("insert", "update", "delete"))) {
            $this->connectMaster();
        }
        return $this->_connection->query($sql, $bind);
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
        foreach ($this as $type=>$connections) {
            foreach ($connections as $connection) {
                $connection->setFetchMode($mode);
            }
        }
    }

    public function getIterator()
    {
        return new ArrayObject($this->_connections);
    }

    /**
     * Inserts a table row with specified data.
     *
     * @param mixed $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insert($table, array $bind)
    {
        // extract and quote col names from the array keys
        $cols = array();
        $vals = array();
        foreach ($bind as $col => $val) {
            $cols[] = $this->quoteIdentifier($col, true);
            if ($val instanceof Zend_Db_Expr) {
                $vals[] = $val->__toString();
                unset($bind[$col]);
            } else {
                $vals[] = '?';
            }
        }

        // build the statement
        $sql = "INSERT INTO "
             . $this->quoteIdentifier($table, true)
             . ' (' . implode(', ', $cols) . ') '
             . 'VALUES (' . implode(', ', $vals) . ')';

        // execute the statement and return the number of affected rows
        $stmt = $this->query($sql, array_values($bind));
        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Updates table rows with specified data based on a WHERE clause.
     *
     * @param  mixed        $table The table to update.
     * @param  array        $bind  Column-value pairs.
     * @param  mixed        $where UPDATE WHERE clause(s).
     * @return int          The number of affected rows.
     */
    public function update($table, array $bind, $where = '')
    {
        /**
         * Build "col = ?" pairs for the statement,
         * except for Zend_Db_Expr which is treated literally.
         */
        $set = array();
        $i = 0;
        foreach ($bind as $col => $val) {
            if ($val instanceof Zend_Db_Expr) {
                $val = $val->__toString();
                unset($bind[$col]);
            } else {
                if ($this->supportsParameters('positional')) {
                    $val = '?';
                } else {
                    if ($this->supportsParameters('named')) {
                        unset($bind[$col]);
                        $bind[':'.$col.$i] = $val;
                        $val = ':'.$col.$i;
                        $i++;
                    } else {
                        /** @see Zend_Db_Adapter_Exception */
                        require_once 'Zend/Db/Adapter/Exception.php';
                        throw new Zend_Db_Adapter_Exception(get_class($this) ." doesn't support positional or named binding");
                    }
                }
            }
            $set[] = $this->quoteIdentifier($col, true) . ' = ' . $val;
        }

        $where = $this->_whereExpr($where);

        /**
         * Build the UPDATE statement
         */
        $sql = "UPDATE "
             . $this->quoteIdentifier($table, true)
             . ' SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '');

        /**
         * Execute the statement and return the number of affected rows
         */
        if ($this->supportsParameters('positional')) {
            $stmt = $this->query($sql, array_values($bind));
        } else {
            $stmt = $this->query($sql, $bind);
        }
        $result = $stmt->rowCount();
        return $result;
    }

    /**
     * Deletes table rows based on a WHERE clause.
     *
     * @param  mixed        $table The table to update.
     * @param  mixed        $where DELETE WHERE clause(s).
     * @return int          The number of affected rows.
     */
    public function delete($table, $where = '')
    {
        $where = $this->_whereExpr($where);

        /**
         * Build the DELETE statement
         */
        $sql = "DELETE FROM "
             . $this->quoteIdentifier($table, true)
             . (($where) ? " WHERE $where" : '');

        /**
         * Execute the statement and return the number of affected rows
         */
        $stmt = $this->query($sql);
        $result = $stmt->rowCount();
        return $result;
    }
}

