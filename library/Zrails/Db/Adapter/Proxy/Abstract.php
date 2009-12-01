<?php

/**
 ** @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 ** @see Zend_Db_Adapter_Exception
 */
require_once 'Zend/Db/Adapter/Exception.php';

/**
 * Class for provide proxy functionality with catch all methods call in methods __call
 *
 * @category   Zrails
 * @package    Zrails_Db
 * @subpackage Adapter
 * @author     necromant2005@gmail.com
 * @copyright  necromant2005 (http://necromant2005.blogspot.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zrails_Db_Adapter_Proxy_Abstract extends Zend_Db_Adapter_Abstract
{

    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws Zend_Db_Adapter_Exception
     */
    protected function _checkRequiredOptions(array $config)
    {
        throw new Zend_Db_Adapter_Exception('illegal call ' . __FUNCTION__);
    }


    /**
     * Returns the underlying database connection object or resource.
     * If not presently connected, this initiates the connection.
     *
     * @return object|resource|null
     */
    public function getConnection()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Returns the configuration variables in this adapter.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Set the adapter's profiler object.
     *
     * The argument may be a boolean, an associative array, an instance of
     * Zend_Db_Profiler, or an instance of Zend_Config.
     *
     * A boolean argument sets the profiler to enabled if true, or disabled if
     * false.  The profiler class is the adapter's default profiler class,
     * Zend_Db_Profiler.
     *
     * An instance of Zend_Db_Profiler sets the adapter's instance to that
     * object.  The profiler is enabled and disabled separately.
     *
     * An associative array argument may contain any of the keys 'enabled',
     * 'class', and 'instance'. The 'enabled' and 'instance' keys correspond to the
     * boolean and object types documented above. The 'class' key is used to name a
     * class to use for a custom profiler. The class must be Zend_Db_Profiler or a
     * subclass. The class is instantiated with no constructor arguments. The 'class'
     * option is ignored when the 'instance' option is supplied.
     *
     * An object of type Zend_Config may contain the properties 'enabled', 'class', and
     * 'instance', just as if an associative array had been passed instead.
     *
     * @param  Zend_Db_Profiler|Zend_Config|array|boolean $profiler
     * @return Zend_Db_Adapter_Abstract Provides a fluent interface
     * @throws Zend_Db_Profiler_Exception if the object instance or class specified
     *         is not Zend_Db_Profiler or an extension of that class.
     */
    public function setProfiler($profiler)
    {
        return $this->__call(__FUNCTION__, array($profiler));
    }


    /**
     * Returns the profiler for this adapter.
     *
     * @return Zend_Db_Profiler
     */
    public function getProfiler()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Get the default statement class.
     *
     * @return string
     */
    public function getStatementClass()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Set the default statement class.
     *
     * @return Zend_Db_Adapter_Abstract Fluent interface
     */
    public function setStatementClass($class)
    {
        return $this->__call(__FUNCTION__, array($class));
    }


    /**
     * Prepares and executes an SQL statement with bound data.
     *
     * @param  mixed  $sql  The SQL statement with placeholders.
     *                      May be a string or Zend_Db_Select.
     * @param  mixed  $bind An array of data to bind to the placeholders.
     * @return Zend_Db_Statement_Interface
     */
    public function query($sql, $bind = array())
    {
        return $this->__call(__FUNCTION__, array($sql, $bind));
    }


    /**
     * Leave autocommit mode and begin a transaction.
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function beginTransaction()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Commit a transaction and return to autocommit mode.
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function commit()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Roll back a transaction and return to autocommit mode.
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function rollBack()
    {
        return $this->__call(__FUNCTION__, array());
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
        return $this->__call(__FUNCTION__, array($table, $bind, $where));
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
        return $this->__call(__FUNCTION__, array($table, $where));
    }


    /**
     * Convert an array, string, or Zend_Db_Expr object
     * into a string to put in a WHERE clause.
     *
     * @param mixed $where
     * @return string
     */
    protected function _whereExpr($where)
    {
        return $this->__call(__FUNCTION__, array($where));
    }


    /**
     * Creates and returns a new Zend_Db_Select object for this adapter.
     *
     * @return Zend_Db_Select
     */
    public function select()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Get the fetch mode.
     *
     * @return int
     */
    public function getFetchMode()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Fetches all SQL result rows as a sequential array.
     * Uses the current fetchMode for the adapter.
     *
     * @param string|Zend_Db_Select $sql  An SQL SELECT statement.
     * @param mixed                 $bind Data to bind into SELECT placeholders.
     * @param mixed                 $fetchMode Override current fetch mode.
     * @return array
     */
    public function fetchAll($sql, $bind = array(), $fetchMode = null)
    {
        return $this->__call(__FUNCTION__, array($sql, $bind, $fetchMode));
    }


    /**
     * Fetches the first row of the SQL result.
     * Uses the current fetchMode for the adapter.
     *
     * @param string|Zend_Db_Select $sql An SQL SELECT statement.
     * @param mixed $bind Data to bind into SELECT placeholders.
     * @param mixed                 $fetchMode Override current fetch mode.
     * @return array
     */
    public function fetchRow($sql, $bind = array(), $fetchMode = null)
    {
        return $this->__call(__FUNCTION__, array($sql, $bind, $fetchMode));
    }


    /**
     * Fetches all SQL result rows as an associative array.
     *
     * The first column is the key, the entire row array is the
     * value.  You should construct the query to be sure that
     * the first column contains unique values, or else
     * rows with duplicate values in the first column will
     * overwrite previous data.
     *
     * @param string|Zend_Db_Select $sql An SQL SELECT statement.
     * @param mixed $bind Data to bind into SELECT placeholders.
     * @return array
     */
    public function fetchAssoc($sql, $bind = array())
    {
        return $this->__call(__FUNCTION__, array($sql, $bind));
    }


    /**
     * Fetches the first column of all SQL result rows as an array.
     *
     * The first column in each row is used as the array key.
     *
     * @param string|Zend_Db_Select $sql An SQL SELECT statement.
     * @param mixed $bind Data to bind into SELECT placeholders.
     * @return array
     */
    public function fetchCol($sql, $bind = array())
    {
        return $this->__call(__FUNCTION__, array($sql, $bind));
    }


    /**
     * Fetches all SQL result rows as an array of key-value pairs.
     *
     * The first column is the key, the second column is the
     * value.
     *
     * @param string|Zend_Db_Select $sql An SQL SELECT statement.
     * @param mixed $bind Data to bind into SELECT placeholders.
     * @return array
     */
    public function fetchPairs($sql, $bind = array())
    {
        return $this->__call(__FUNCTION__, array($sql, $bind));
    }


    /**
     * Fetches the first column of the first row of the SQL result.
     *
     * @param string|Zend_Db_Select $sql An SQL SELECT statement.
     * @param mixed $bind Data to bind into SELECT placeholders.
     * @return string
     */
    public function fetchOne($sql, $bind = array())
    {
        return $this->__call(__FUNCTION__, array($sql, $bind));
    }


    /**
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    protected function _quote($value)
    {
        return $this->__call(__FUNCTION__, array($value));
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


    /**
     * Quotes a value and places into a piece of text at a placeholder.
     *
     * The placeholder is a question-mark; all placeholders will be replaced
     * with the quoted value.   For example:
     *
     * <code>
     * $text = "WHERE date < ?";
     * $date = "2005-01-02";
     * $safe = $sql->quoteInto($text, $date);
     * // $safe = "WHERE date < '2005-01-02'"
     * </code>
     *
     * @param string  $text  The text with a placeholder.
     * @param mixed   $value The value to quote.
     * @param string  $type  OPTIONAL SQL datatype
     * @param integer $count OPTIONAL count of placeholders to replace
     * @return string An SQL-safe quoted value placed into the original text.
     */
    public function quoteInto($text, $value, $type = null, $count = null)
    {
        return $this->__call(__FUNCTION__, array($text, $value, $type, $count));
    }


    /**
     * Quotes an identifier.
     *
     * Accepts a string representing a qualified indentifier. For Example:
     * <code>
     * $adapter->quoteIdentifier('myschema.mytable')
     * </code>
     * Returns: "myschema"."mytable"
     *
     * Or, an array of one or more identifiers that may form a qualified identifier:
     * <code>
     * $adapter->quoteIdentifier(array('myschema','my.table'))
     * </code>
     * Returns: "myschema"."my.table"
     *
     * The actual quote character surrounding the identifiers may vary depending on
     * the adapter.
     *
     * @param string|array|Zend_Db_Expr $ident The identifier.
     * @param boolean $auto If true, heed the AUTO_QUOTE_IDENTIFIERS config option.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident, $auto = false)
    {
        return $this->__call(__FUNCTION__, array($ident, $auto));
    }


    /**
     * Quote a column identifier and alias.
     *
     * @param string|array|Zend_Db_Expr $ident The identifier or expression.
     * @param string $alias An alias for the column.
     * @param boolean $auto If true, heed the AUTO_QUOTE_IDENTIFIERS config option.
     * @return string The quoted identifier and alias.
     */
    public function quoteColumnAs($ident, $alias, $auto = false)
    {
        return $this->__call(__FUNCTION__, array($ident, $alias, $auto));
    }


    /**
     * Quote a table identifier and alias.
     *
     * @param string|array|Zend_Db_Expr $ident The identifier or expression.
     * @param string $alias An alias for the table.
     * @param boolean $auto If true, heed the AUTO_QUOTE_IDENTIFIERS config option.
     * @return string The quoted identifier and alias.
     */
    public function quoteTableAs($ident, $alias = null, $auto = false)
    {
        return $this->__call(__FUNCTION__, array($ident, $alias, $auto));
    }


    /**
     * Quote an identifier and an optional alias.
     *
     * @param string|array|Zend_Db_Expr $ident The identifier or expression.
     * @param string $alias An optional alias.
     * @param boolean $auto If true, heed the AUTO_QUOTE_IDENTIFIERS config option.
     * @param string $as The string to add between the identifier/expression and the alias.
     * @return string The quoted identifier and alias.
     */
    protected function _quoteIdentifierAs($ident, $alias = null, $auto = false, $as = ' AS ')
    {
        return $this->__call(__FUNCTION__, array($ident, $alias, $auto, $as));
    }


    /**
     * Quote an identifier.
     *
     * @param  string $value The identifier or expression.
     * @param boolean $auto If true, heed the AUTO_QUOTE_IDENTIFIERS config option.
     * @return string        The quoted identifier and alias.
     */
    protected function _quoteIdentifier($value, $auto = false)
    {
        return $this->__call(__FUNCTION__, array($value, $auto));
    }


    /**
     * Returns the symbol the adapter uses for delimited identifiers.
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Return the most recent value from the specified sequence in the database.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return string
     */
    public function lastSequenceId($sequenceName)
    {
        return $this->__call(__FUNCTION__, array($sequenceName));
    }


    /**
     * Generate a new value from the specified sequence in the database, and return it.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return string
     */
    public function nextSequenceId($sequenceName)
    {
        return $this->__call(__FUNCTION__, array($sequenceName));
    }


    /**
     * Helper method to change the case of the strings used
     * when returning result sets in FETCH_ASSOC and FETCH_BOTH
     * modes.
     *
     * This is not intended to be used by application code,
     * but the method must be public so the Statement class
     * can invoke it.
     *
     * @param string $key
     * @return string
     */
    public function foldCase($key)
    {
        return $this->__call(__FUNCTION__, array($key));
    }


    /**
     * called when object is getting serialized
     * This disconnects the DB object that cant be serialized
     *
     * @throws Zend_Db_Adapter_Exception
     * @return array
     */
    public function __sleep()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * called when object is getting unserialized
     *
     * @return void
     */
    public function __wakeup()
    {
        return $this->__call(__FUNCTION__, array());
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
     * SCHEMA_NAME => string; name of database or schema
     * TABLE_NAME  => string;
     * COLUMN_NAME => string; column name
     * COLUMN_POSITION => number; ordinal position of column in table
     * DATA_TYPE   => string; SQL datatype name of column
     * DEFAULT     => string; default expression of column, null if none
     * NULLABLE    => boolean; true if column can have nulls
     * LENGTH      => number; length of CHAR/VARCHAR
     * SCALE       => number; scale of NUMERIC/DECIMAL
     * PRECISION   => number; precision of NUMERIC/DECIMAL
     * UNSIGNED    => boolean; unsigned property of an integer type
     * PRIMARY     => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
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
     * Creates a connection to the database.
     *
     * @return void
     */
    protected function _connect()
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
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return string
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        return $this->__call(__FUNCTION__, array($tableName, $primaryKey));
    }


    /**
     * Begin a transaction.
     */
    protected function _beginTransaction()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Commit a transaction.
     */
    protected function _commit()
    {
        return $this->__call(__FUNCTION__, array());
    }


    /**
     * Roll-back a transaction.
     */
    protected function _rollBack()
    {
        return $this->__call(__FUNCTION__, array());
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
        return $this->__call(__FUNCTION__, array($mode));
    }


    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param mixed $sql
     * @param integer $count
     * @param integer $offset
     * @return string
     */
    public function limit($sql, $count, $offset = array())
    {
        return $this->__call(__FUNCTION__, array($sql, $count, $offset));
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
    abstract public function __construct($config);


    /**
     * Special method for catch all non exist method
     * Work as 1 enter point for all methods call
     *
     * @param $method string; call method name
     * @param $args array; input arguments array
     * @return mixed
     */
    abstract public function __call($method, $args);
}

