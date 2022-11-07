<?php
/**
 * (c) 2020 Francesco Terenzani
 */
namespace pp;

/**
 * Just a bit of sugar in PDO
 */
class Db extends \PDO
{

	function __construct($dsn, $username = '', $password = '', array $driver_options = array())
	{

		parent::__construct(
			$dsn, $username, $password, 
			$driver_options + array(
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
					\PDO::ATTR_STATEMENT_CLASS => array(Statement::class),
				)
		
		);

	}

	function execute($query, ?array $params = null)
	{
		$stmt = $this->prepare($query);
		$stmt->execute($params);
		return $stmt;
	}

	function rowCount($sql, ?array $params = null)
	{
		$stmt = $this->execute($sql, $params);
		return $stmt->rowCount();
	}

	function insertId($sql, ?array $params = null)
	{
		$this->execute($sql, $params);
		return $this->lastInsertId();
	}

	function getHelper()
	{
		return new QueryHelper($this);
	}

}

/**
 * Just a bit of sugar in PDOStatement
 */
class Statement extends \PDOStatement
{

	function execute($params = null) 
	{
		if (!is_null($params) && !is_array($params)) {
			$params = array($params);
		}

		return parent::execute($params);
	}

	function fetchObjects($className = 'stdClass', array $constructorArgs = array())
	{
		return $this->fetchAll(\PDO::FETCH_CLASS, $className, $constructorArgs);
	}

	function fetchAllColumn($column_number = 0)
	{
		return $this->fetchAll(\PDO::FETCH_COLUMN, $column_number);
	}

}