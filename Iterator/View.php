<?php
/**
 * Simple O/R Mapper Iterator Class File
 *
 * PHP versions 5
 *
 * @author    Satoshi Nishimura <nishim314@gmail.com>
 * @copyright 2009-2011 Satoshi Nishimura
 */

require_once dirname(dirname(__FILE__)) . '/Vo.php';

/**
 * Simple O/R Mapper Iterator Class
 *
 * @author    Satoshi Nishimura <nishim314@gmail.com>
 */
class YokazeDb_Iterator_View implements Iterator
{
    protected $dao;
    protected $params;
    protected $replacements;
    protected $key;
    protected $vo;
    protected $isContinue;

    public function __construct(YokazeDb_View $dao)
    {
        $this->setVo($dao->createVo());
        $this->dao = $dao;
    }

    protected function setVo(YokazeDb_Vo $vo)
    {
        $this->vo = $vo;
    }
    public function getVo($params = null, $replace = null)
    {
        return $this->dao->getVo($params, $replace);
    }
    public function setLimit($limit)
    {
        $this->dao->setLimit($limit);
        return $this;
    }
    public function setOffset($offset)
    {
        $this->dao->setOffset($offset);
        return $this;
    }

    /**
     * Set arguments of prepared statement.
     *
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Format string of replacement SQL.
     *
     * @param string $file
     */
    public function setReplacements($reps)
    {
        $this->replacements = $reps;
        return $this;
    }

    protected function getStatement(){
        return $this->dao->prepareStmt($this->params, $this->replacements);
    }

    protected function bind(PDOStatement $stmt, $vo){
        $this->dao->bind($stmt, $vo);
    }

    public function rewind(){
        $this->stmt = $this->getStatement();
        if (!$this->stmt instanceof PDOStatement){
            trigger_error('Can not get PDOStatement.', E_USER_WARNING);
            return;
        }

        if (!is_object($this->vo))
            $this->vo = new StdClass;
        $this->bind($this->stmt, $this->vo);
        $this->key = 0;
        $this->isContinue = $this->stmt->fetch(PDO::FETCH_BOUND);
    }

    public function current()
    {
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

    public function count()
    {
        $stmt = $this->getStatement();
        return $stmt->rowCount();
        // Warning: This function was checked PostgreSQL only.
    }
        
}
