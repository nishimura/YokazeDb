<?php
/**
 * Simple Database View Class File
 *
 * PHP versions 5
 *
 * @author    Satoshi Nishimura <nishim314@gmail.com>
 * @copyright 2009-2011 Satoshi Nishimura
 */

require_once 'Vo.php';
require_once 'Driver.php';

/**
 * Simple Database View Class
 *
 * @author    Satoshi Nishimura <nishim314@gmail.com>
 */
class YokazeDb_View
{
    protected $db;
    protected $sqlFile;
    private $limit;
    private $offset;

    public function __construct(YokazeDb_Driver $db, $sqlFile)
    {
        $this->db = $db;
        $this->sqlFile = $sqlFile;
    }
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
    public function setOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Execute query and return pdo statement.
     *
     * @param array $params parameter of prepared statement.
     * @param mixed $replace arguments of sprintf for replacement sql string.
     * @return PDOStatement
     */
    public function prepareStmt($params = null, $replace = null)
    {
        $file = str_replace('_', '/', $this->sqlFile) . '.sql';

        $sql = file_get_contents($file, true);
        if ($replace !== null){
            $replace = (array)$replace;
            array_unshift($replace, $sql);
            $sql = call_user_func_array('sprintf', $replace);
            if (!$sql){
                trigger_error('sprintf');
                return false;
            }
        }
        if (is_numeric($this->limit))
            $sql .= ' limit ' . $this->limit . ' ';
        if (is_numeric($this->offset))
            $sql .= ' offset ' . $this->offset . ' ';

        if ($params !== null)
            $stmt = $this->db->query($sql, $params);
        else
            $stmt = $this->db->query($sql);
        if (!$stmt){
            trigger_error('query error! sql="' . $sql . '"', E_USER_WARNING);
            return false;
        }

        if ($stmt->errorCode() != '00000'){
            $errInfo = $stmt->errorInfo();
            trigger_error('['.$errInfo[0].':'.$errInfo[1].']'.$errInfo[2], E_USER_WARNING);
            return false;
        }

        return $stmt;
    }

    public function createVo()
    {
        $sqlFile = str_replace('/', '_', $this->sqlFile);
        $className = 'YokazeDb_Vo_' . implode('', array_map('ucfirst', explode('_', $sqlFile)));

        if (!class_exists($className, false))
            eval("class $className implements YokazeDb_Vo{}");

        $className = $className;
        return new $className();
    }

    public function bind(PDOStatement $stmt, YokazeDb_Vo $vo)
    {
        $columnCount = $stmt->columnCount();
        $columnTypes = array();
        $columnNames = array();
        $VoNames     = array();
        for ($i = 0; $i < $columnCount; $i++){
            $meta = $stmt->getColumnMeta($i);
            if (!$meta)
                break;
            //if (isset($meta['pdo_type']))
                $columnTypes[$i] = $meta['pdo_type'];
            $columnNames[$i] = $meta['name'];
            $name = implode('', array_map('ucfirst', explode('_', $meta['name'])));
            $name[0] = strtolower($name[0]);
            $voNames[$i] = $name;
        }


        for($i = 0; $i < $columnCount; $i++){
            $name = $voNames[$i];
            $vo->$name = null;
            $stmt->bindColumn($columnNames[$i], $vo->$name, $columnTypes[$i]);
        }
    }

    /**
     * Return VO by sql file, parameter, replacement string.
     *
     * @param array $params params of prepared statement
     * @param string|array $replace replacement string
     * @return YokazeDb_Vo
     */
    public function getVo($params = null, $replace = null)
    {
        $stmt = $this->prepareStmt($params, $replace);
        if (!$stmt)
            return false;

        $vo = $this->createVo();

        $columnCount = $stmt->columnCount();
        $columnData  = $stmt->fetch(PDO::FETCH_NUM);
        for ($i = 0; $i < $columnCount; $i++){
            $meta = $stmt->getColumnMeta($i);
            if (!$meta)
                break;
            $name = implode('', array_map('ucfirst', explode('_', $meta['name'])));
            $name[0] = strtolower($name[0]);
            $vo->$name = $columnData[$i];
        }

        return $vo;
    }

}
