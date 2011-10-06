<?php

class YokazeDb_Factory_Orm
{
    private $config = array();

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function create($tableName)
    {
        if (!$this->config){
            trigger_error('Not configured', E_USER_WARNING);
            return;
        }

        try {
            require_once dirname(dirname(__FILE__)) . '/Driver/Factory.php';
            $db = YokazeDb_Driver_Factory::factory($this->config['dsn']);
        }catch (PDOException $e){
            // PDO error
            trigger_error($e->getMessage(), E_USER_ERROR);
        }catch (Exception $e){
            // framework error
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
        require_once dirname(dirname(__FILE__)) . '/Orm.php';
        $dao = new YokazeDb_Orm($db, $tableName);
        $dao->autoCreateConfig($this->config['autoConfig']);
        $dao->setTableConfigs($this->config['configFile']);
        if (!$dao->existsTable()){
            require_once dirname(dirname(__FILE__)) . '/Inflector.php';
            if($dao->existsTable(YokazeDb_Inflector::singularize($tableName))){
                $dao->setTableName(YokazeDb_Inflector::singularize($tableName));
                require_once dirname(dirname(__FILE__)) . '/Iterator/Orm.php';
                $dao = new YokazeDb_Iterator_Orm($dao);
            }
        }
        return $dao;
    }
}
