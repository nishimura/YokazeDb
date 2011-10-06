<?php
/**
 * Selector of Db_Factory Class File.
 *
 * PHP versions 5
 *
 * @author    Satoshi Nishimura <nishim314@gmail.com>
 * @copyright 2009-2011 Satoshi Nishimura
 */

/**
 * Selector of Db_Factory Class.
 *
 * @author    Satoshi Nishimura <nishim314@gmail.com>
 */
class YokazeDb_Factory
{
    protected $config = array('dsn' => '',
                              'autoConfig' => true,
                              'configFile' => 'cache/tables.ini');

    /**
     * Set dsn
     */
    public function setDsn($dsn)
    {
        $this->config['dsn'] = $dsn;
    }
    /**
     * Set auto generate config file
     */
    public function setAutoConfig($auto)
    {
        $this->config['autoConfig'] = $auto;
    }
    /**
     * Set config file path
     */
    public function setConfigFile($path)
    {
        $this->config['configFile'] = $path;
    }

    /**
     * Return factory by name
     *
     * @param string $name
     * @return Db_Factory
     */
    public function select($name)
    {
        switch (true){
        case (preg_match('/[A-Z]/', $name)):
            require_once 'Factory/View.php';
            $factory = new YokazeDb_Factory_View($this->config);
            break;
        default:
            require_once 'Factory/Orm.php';
            $factory = new YokazeDb_Factory_Orm($this->config);
            break;
        }
        return $factory;
    }

    /**
     * Select and create object for DB.
     *
     * @param string $name
     * @return Object
     */
    public function create($name)
    {
        $factory = $this->select($name);
        return $factory->create($name);
    }
}
