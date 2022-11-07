<?php

namespace pp;

use pp\db;

class QueryHelper
{

	public array $register = [
		'@fields' => '*',
		'@where' => 'WHERE 1=1',
		'@page' => ''
	];

	function __construct(public db $db)
	{
	}

	function page(int $page, int $perPage = 20, $name = '@page')
	{
		$this->register[$name] = "LIMIT $perPage OFFSET ". (($page - 1) * $perPage);
		return $this;
	}

	function in(array $in, $name = '@in')
	{
		$this->register[$name] = sprintf('IN (%s)', implode(', ', array_map(fn($item) => $this->db->quote($item), $in)));
		return $this;
	}

	function values(array $values, $name = '@values')
	{

		$sql1 = '';
		$sql2 = '';
		foreach ($values as $key => $value) {
			if (is_numeric($key)) {
				$sql1 .= "$value, ";
				$sql2 .= ":$value, ";
			} else {
				$sql1 .= "$key, ";
				$sql2 .= $this->db->quote($value) . ', ';
			}

		}
		$sql1 = rtrim($sql1, ', ') ;
		$sql2 = rtrim($sql2, ', ');

		$this->register[$name] = "($sql1) VALUES ($sql2)";
		return $this;	
	}

	function set(array $set, $name = '@set')
	{
	
		$sql = 'SET';
		foreach ($set as $key => $value) {
			if (is_numeric($key)) {
				$sql .= " $value = :$value,";
			} else {
				$sql .= sprintf(" $key = %s,", $this->db->quote($value));				
			}

		}

		$this->register[$name] = rtrim($sql, ',');
		return $this;

	}

	function prepare($sql)
	{
		$sql = $this->getSql($sql);
		return $this->db->prepare($sql);
	}

	function execute($sql, ?array $params = null)
	{
		$stmt = $this->prepare($sql);
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
		return $this->db->lastInsertId();
	}

	function getSql($sql)
	{
		return $this->register? strtr($sql, $this->register): $sql;
	}

}