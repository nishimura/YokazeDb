<?php
/**
 * Iterator with Orm.
 *
 * PHP versions 5
 *
 * @author    Satoshi Nishimura <nishim314@gmail.com>
 * @copyright 2010-2011 Satoshi Nishimura
 */

require_once dirname(dirname(__FILE__)) . '/Orm.php';

/**
 * Iterator with Orm.
 *
 * @author    Satoshi Nishimura <nishim314@gmail.com>
 */
class YokazeDb_Iterator_Orm implements Iterator
{
    protected $orm;
    protected $options;
    protected $params= array();
    protected $stmt;
    protected $columns;
    protected $key;
    protected $vo;
    protected $isContinue;
    private $limit;
    private $offset;
    public function __construct(YokazeDb_Orm $orm)
    {
        $this->orm = $orm;
        $this->setVo($orm->createVo());
    }
    public function setLimit($limit)
    {
        $this->options['limit'] = $limit;
        return $this;
    }
    public function setOffset($offset)
    {
        $this->options['offset'] = $offset;
        return $this;
    }

    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    protected function setVo(YokazeDb_Vo $vo)
    {
        $this->vo = $vo;
    }

    protected function getStatement()
    {
        return $this->orm->getVosStatement($this->getOptions(), $this->getParams());
    }

    protected function bindArray(PDOStatement $stmt, YokazeDb_Vo $vo)
    {
        return $this->orm->bindArray($stmt, $vo, $this->columns);
    }

    public function rewind(){
        $this->stmt = $this->getStatement();
        if (!$this->stmt instanceof PDOStatement){
            trigger_error('Can not get PDOStatement.', E_USER_WARNING);
            return;
        }

        if (!is_object($this->vo))
            $this->vo = new StdClass;
        $this->bindArray($this->stmt, $this->vo);
        $this->key = 0;
        $this->isContinue = $this->stmt->fetch(PDO::FETCH_BOUND);
    }

    public function current(){
        return $this->vo;
    }

    public function key(){
        return $this->key;
    }

    public function next(){
        $this->isContinue = $this->stmt->fetch(PDO::FETCH_BOUND);
    }

    public function valid(){
        return $this->isContinue;;
    }

    public function count(){
        $options = $this->getOptions();
        if (isset($options['where']))
            $where = $options['where'];
        else
            $where = array();

        return $this->orm->count($where);
    }
}
