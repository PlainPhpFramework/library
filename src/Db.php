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

	function query($sql, $params = array()) 
	{
		$stmt = $this->prepare($sql);
		$stmt->execute($params);
		return $stmt;
	}

	function exec($sql, $params = array())
	{
		$stmt = $this->query($sql, $params);
		return $stmt->rowCount();
	}

}

/**
 * Just a bit of sugar in PDOStatement
 */
class Statement extends \PDOStatement
{

	function execute($parameters = null) 
	{

		if (!is_array($parameters)) {
			$parameters = array($parameters);
		}

		return parent::execute($parameters);
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