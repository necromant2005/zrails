<?php

/**
 ** @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 ** @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 ** @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 ** @see Zend_Db_Adapter_Exception
 */
require_once 'Zend/Db/Adapter/Exception.php';

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
class Zrails_Db_Facade_Scale extends Zend_Db_Adapter_Abstract implements IteratorAggregate
{
    /**
     * Collectin of Zend_Db_Adapter_Abstract
     *
     * @var array
     */
    protected $_shards = array();

    /**
     * Count of alived shardes
     *
     * @var int
     */
    protected $_countShards = 0;

    /**
     * Collection of scale strategies for tables
     *
     * @var array
     */
    protected $_tablesScaleStrategies = array();


    /**
     * Collection of primary key generators for tables
     *
     * @var array
     */
    protected $_tablesScaleKeyProviders = array();


    /**
     * Current shard connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_shard = null;

    /**
     * Last autogenerated insert uid
     *
     * @var int|string
     */
    protected $_lastInsertId = 0;

    /**
     * Construct Db Adapter Scale Adapter
     * @param $array
     * @example new Zrails_Db_Facade_Scale(array(
     *   'tables' => array(
     *     'table_name' => array(
     *         'field'        => 'field_name',
     *         'strategy'     => 'Zrails_Db_Facade_Scale_Strategy_Crc32'
     *         'key_provider' => 'Zrails_Db_Facade_Scale_Key_Provider_Random'
     *      )
     *
     *   ),
     *   'shards' => array(
     *    'shard1' => Db::factory(...)
     *    'shard2' => Db::factory(...)
     *   )
     * )
     */
    public function __construct($options)
    {
        if (empty($options['tables'])) throw new Zend_Db_Adapter_Exception("Empty options section 'tables'");
        if (empty($options['shards'])) throw new Zend_Db_Adapter_Exception("Empty options section 'shards'");

        //configure shards
        foreach ($options['shards'] as $shard) {
            if ($shard instanceof Zend_Db_Adapter_Abstract) {
                $this->_shards[] = $shard;
                continue;
            }
            $this->_shards[] = Zend_Db::factory(new Zend_Config($shard));
        }
        $this->_countShards = count($this->_shards);

        //configure tables
        foreach ($options['tables'] as $table=>$table_options) {
            // construct scale strategy for current table
            $nameScaleStrategy = $table_options["strategy"];
            Zend_Loader::loadClass($nameScaleStrategy);
            $Strategy = new $nameScaleStrategy($this);
            $Strategy->setTable($table);
            $Strategy->setField($table_options["field"]);
            $this->_tablesScaleStrategies[$table] = $Strategy;

            // construct key provider for current table
            $nameScaleKeyProvider = $table_options["key_provider"];
            Zend_Loader::loadClass($nameScaleKeyProvider);
            $Strategy = new $nameScaleKeyProvider($this);
            $Strategy->setTable($table);
            $Strategy->setField($table_options["field"]);
            $this->_tablesScaleKeyProviders[$table] = $Strategy;
        }


    }

    public function getCountShards()
    {
        return $this->_countShards;
    }

    /**
     * Get scale strategy for table
     *
     * @param string $table
     * @throw Zend_Db_Adapter_Exception
     * @return Zrails_Db_Facade_Scale_Strategy_Abstract
     */
    public function getScaleStrategy($table)
    {
        if (!array_key_exists($table, $this->_tablesScaleStrategies)) throw new Zend_Db_Adapter_Exception("Uknow table '$table'");
        return $this->_tablesScaleStrategies[$table];
    }

    /**
     * Get autogenerate strategy for table
     *
     * @param string $table
     * @throw Zend_Db_Adapter_Exception
     * @return Zrails_Db_Adapter_Scale_Key_Provider_Abstract
     */
    public function getScaleKeyProvider($table)
    {
        if (!array_key_exists($table, $this->_tablesScaleKeyProviders)) throw new Zend_Db_Adapter_Exception("Uknow primary for table '$table'");
        return $this->_tablesScaleKeyProviders[$table];
    }

    /**
     * Get shard connection by name
     *
     * @param string $name
     * @throw Zend_Db_Adapter_Exception
     * @return Zend_Db_Adapter_Abstract
     */
    public function getShard($name)
    {
        if (!array_key_exists($name, $this->_shards)) throw new Zend_Db_Adapter_Exception("Uknknow shard name '$name'");
        return $this->_shards[$name];
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
        return call_user_func_array(array($this->_shard, $method), $args);
    }

    /**
     * Special handling for PDO query().
     * All bind parameter names must begin with ':'
     *
     * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Statement_Pdo
     * @throws Zend_Db_Adapter_Exception To re-throw PDOException.
     */
    public function query($sql, $bind = array())
    {
        $this->_setConnectionDbByQuery($sql);
        return $this->__call(__FUNCTION__, array($sql, $bind));
    }



    /**
     * Special handling for Scale functionality.
     * To select current shard db connection from query
     *
     * @param string|Zend_Db_Select $sql The SQL statement with placeholders.
     * @return Zend_Db_Adapter_Abstract
     * @throws Zend_Db_Adapter_Exception To re-throw PDOException.
     */
    protected function _setConnectionDbByQuery($sql, $table="")
    {

        if (is_array($sql)) {
            foreach ($sql as $_sql) {
                try {
                    return $this->_setConnectionDbByQuery("$_sql", $table);
                } catch (Zend_Db_Adapter_Exception $E) {}
            }
            if ($scale_field_rule_exists) return true;
            throw new Zend_Db_Adapter_Exception("Unknow shard number in query [$sql]");
        }

        if (!$table) {
            preg_match("~from\s+([^ ]+)\s+~i", "$sql", $match_table);
            $table = preg_replace("~[^a-z0-9_]+~", "", $match_table[1]);
        }
        $Strategy = $this->getScaleStrategy($table);

        $symbol = $this->getQuoteIdentifierSymbol();
        if (!preg_match("~$symbol*" . $Strategy->getField() . "$symbol*\s*=\s*([^ \)]+)~", "$sql", $match)) {
            throw new Zend_Db_Adapter_Exception("Unknow shard number in query [$sql]");
        }
        $value = preg_replace("[^a-z0-9\-]", "", $match[1]);

        return $this->_shard = $Strategy->getShard($value);
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
        $this->_lastInsertId = null;

        $Strategy = $this->getScaleStrategy($table);
        $StrategyGenerate = $this->getScaleKeyProvider($table);

        if (!array_key_exists($StrategyGenerate->getField(), $bind)) {
            $this->_lastInsertId = $StrategyGenerate->getUniqueId();
            $bind[$Strategy->getField()] = $this->_lastInsertId;
            $this->_shard = $Strategy->getShard($this->_lastInsertId);
        }

        if (!array_key_exists($Strategy->getField(), $bind)) throw new Zend_Db_Adapter_Exception('Dissable insert without scale field value');
        $value = $bind[$Strategy->getField()];
        $this->_shard = $Strategy->getShard($value);

        return $this->__call(__FUNCTION__, array($table, $bind));
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
       $Strategy = $this->getScaleStrategy($table);
       $symbol = $this->getQuoteIdentifierSymbol();
       $this->_setConnectionDbByQuery($where, $table);
       if (!array_key_exists($Strategy->getField(), $bind)) {
           return $this->__call(__FUNCTION__, array($table, $bind, $where));
       }
       $select = $this->select()->from($table);
       if (is_array($where)) {
           foreach ($where as $_where) {
               $select->where("$_where");
           }
       } else {
           $select->where("$where");
       }

       $rows = $select->query()->fetchAll();
       $count = 0;
       foreach ($rows as $row) {
           $count+=$this->insert($table, array_merge($row, $bind));
           $value = $row[$Strategy->getField()];
           $this->delete($table, $this->quoteInto($symbol.$Strategy->getField().$symbol."=?", $value));
       }
       return $count;
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
        $_where = $this->_whereExpr($where);
        if (empty($_where)) {
           /**
         	* Build the DELETE statement
         	*/
            $sql = "DELETE FROM "
             . $this->quoteIdentifier($table, true)
             . (($where) ? " WHERE $where" : '');

            $count = 0;
            foreach($this->_shards as $db) {
                $this->_shard = $db;
                $stmt = $this->__call("query", array($sql));
                $count+= $stmt->rowCount();
            }
            return $count;
        }
        return parent::delete($table, $where);
     }

    /**
     * Safely quotes a value for an SQL statement.
     *
     * If an array is passed as the value, the array values are quoted
     * and then returned as a comma-separated string.
     *
     * @param mixed $value The value to quote.
     * @param mixed $type  OPTIONAL the SQL datatype name, or constant, or null.
     * @return mixed An SQL-safe quoted value (or string of separated values).
     */
    public function quote($value, $type = null)
    {
        return $this->__call(__FUNCTION__, array($value, $type));
    }

    protected function _connect()
    {
        if ($this->_shard) return;
        $this->_shard = $this->_shards[array_rand($this->_shards)];
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        return $this->__call(__FUNCTION__, array());
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     * IDENTITY         => integer; true if column is auto-generated with unique values
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        return $this->__call(__FUNCTION__, array($tableName, $schemaName));
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param  string $sql
     * @param  integer $count
     * @param  integer $offset OPTIONAL
     * @throws Zend_Db_Adapter_Exception
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
        return $this->__call(__FUNCTION__, array($sql, $count, $offset));
    }

    /**
     * Get last insert id for autogenerated primary key field
     *
     * @param string $tableName
     * @param string $primaryKey
     * @return int
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        if ($this->_lastInsertId) {
            return $this->_lastInsertId;
        }
        return $this->__call(__FUNCTION__, array($table, $primaryKey));
    }



    /**
     * Get a custom for current connection quote symbol
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return $this->__call(__FUNCTION__, array());
    }

    public function getIterator()
    {
        return new ArrayObject($this->_shards);
    }

    /**
     * Test if a connection is active
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->__call(__FUNCTION__, array());
    }

    /**
     * Force the connection to close.
     *
     * @return void
     */
    public function closeConnection()
    {
        return $this->__call(__FUNCTION__, array());
    }

    /**
     * Prepare a statement and return a PDOStatement-like object.
     *
     * @param string|Zend_Db_Select $sql SQL query
     * @return Zend_Db_Statement|PDOStatement
     */
    public function prepare($sql)
    {
        return $this->__call(__FUNCTION__, array($sql));
    }

    /**
     * Begin a transaction.
     */
    protected function _beginTransaction() {}

    /**
     * Commit a transaction.
     */
    protected function _commit() {}

    /**
     * Roll-back a transaction.
     */
    protected function _rollBack() {}

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


    /**
     * Check if the adapter supports real SQL parameters.
     *
     * @param string $type 'positional' or 'named'
     * @return bool
     */
    public function supportsParameters($type)
    {
        return $this->__call(__FUNCTION__, array($type));
    }

    /**
     * Retrieve server version in PHP style
     *
     * @return string
     */
    public function getServerVersion()
    {
        return $this->__call(__FUNCTION__, array());
    }
}

