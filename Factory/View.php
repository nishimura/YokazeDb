<?php

class YokazeDb_Factory_View
{
    private $config = array();

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Return iterator.
     *
     * @param string $sqlFile
     * @return Laiz_Db_Iterator_View
     */
    public function create($sqlFile)
    {
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
        require_once dirname(dirname(__FILE__)) . '/View.php';
        $dao = new YokazeDb_View($db, $sqlFile);

        require_once dirname(dirname(__FILE__)) . '/Iterator/View.php';
        return new YokazeDb_Iterator_View($dao);
    }
}
