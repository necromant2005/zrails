<?php
require_once 'Zrails/Db/Adapter/Proxy/Abstract.php';

class AdapterProxy extends Zrails_Db_Adapter_Proxy_Abstract
{
    protected $_config = array();

    protected $_method = "";
    protected $_args   = array();

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function __call($method, $args)
    {
        return array($method=>$args);
    }

    public function _connect()
    {
        return ;
    }

    public function isConnected()
    {
        return true;
    }


    public function closeConnection()
    {
        return ;
    }

    public function _whereExpr($where)
    {
        return parent::_whereExpr($where);
    }

    public function _quote($value)
    {
        return parent::_quote($value);
    }

    public function _quoteIdentifierAs($ident, $alias = NULL, $auto = false, $as = ' AS ')
    {
        return parent::_quoteIdentifierAs($ident, $alias, $auto, $as);
    }

    public function _quoteIdentifier($value, $auto = false)
    {
        return parent::_quoteIdentifier($value, $auto);
    }

    public function _beginTransaction()
    {
        return parent::_beginTransaction();
    }

    public function _commit()
    {
        return parent::_commit();
    }

    public function _rollBack()
    {
        return parent::_rollBack();
    }

}

