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
        $this->_method = $method;
        $this->_args   = $args;
    }

    protected function _connect()
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

    public function getCallMethod()
    {
        return $this->_method;
    }

    public function getCallArgs()
    {
        return $this->_args;
    }
}

