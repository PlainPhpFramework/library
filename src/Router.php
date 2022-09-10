<?php

namespace pp;

class Router
{
	public RouteMap $routes;
	public Map $map;
	public Map $controllerMap;
	public Map $namespaceMap;

	function __construct()
	{
		$this->routes = new RouteMap;
		$this->map = new Map;
		$this->map->rtrimSlash = false;
		$this->methodMap = new Map;
		$this->controllerMap = new Map;
		$this->namespaceMap = new Map;

		// Set some defaults
		$this->methodMap->add(null, 'index');
		$this->controllerMap->add(null, 'default');
	}

	function dispatch($pathInfo, array &$params = null) {

		$initialPathInfo = $pathInfo = ltrim($pathInfo, '/');

		// The request is mapped
		if (false !== ($newPathInfo = $this->map->dispatch($pathInfo) ?: $this->routes->dispatch($pathInfo, $params))) {
			$pathInfo = $newPathInfo;
		}

		$pathParts = explode('/', $pathInfo);
		$method = end($pathParts);
		$controller = count($pathParts) > 1? implode('/', array_slice($pathParts, 0, -1)): '';
		$namespace = count($pathParts) > 2? implode('/', array_slice($pathParts, 0, -2)): '';

		// No prev rules match
		if (!$newPathInfo) {

			if (false !== ($newMethod = $this->methodMap->dispatch($method))) {
				$method = $newMethod;
			} 

			if (false !== ($newController =  $this->controllerMap->dispatch($controller))) {
				
				$controller = $newController;

			} elseif (false !== ($newNamespace =  $this->namespaceMap->dispatch($namespace))) {
				
				$controller = $newNamespace . substr($controller, strlen($namespace));

			}

		}

		return [$controller, $method];

	}

	function reverse($path, array $params = null) 
	{

		$path = ltrim($path, '/');

		if (false !== ($newPath = $this->map->reverse($path) ?: $this->routes->reverse($path, $params))) {
			$path = $newPath;
		}

		// No prev rules match
		if (!$newPath) {
			
			$pathParts = explode('/', $path);
			$method = end($pathParts);
			$controller = count($pathParts) > 1? implode('/', array_slice($pathParts, 0, -1)): '';
			$namespace = count($pathParts) > 2? implode('/', array_slice($pathParts, 0, -2)): '';

			if (false !== ($newMethod = $this->methodMap->reverse($method))) {
				$method = $newMethod;
			}

			if (false !== ($newController = $this->controllerMap->reverse($controller))) {
				
				$controller = $newController;

			} elseif (false !== ($newNamespace = $this->namespaceMap->reverse($namespace))) {
				
				$controller = $newNamespace . substr($controller, strlen($namespace));

			}

			$path = ltrim($controller . '/' . $method, '/');

		}

		if ($params) {
			$path .= '?'.http_build_query($params);
		}

		return $path;

	}

}


class Map
{

	protected $from;
	protected $to;
	public $rtrimSlash = true;

	function add($from, $to)
	{
		if ($this->rtrimSlash) {
			$from = trim($from, '/');
			$to = trim($to, '/');		
		} else {
			$from = ltrim($from, '/');
			$to = ltrim($to, '/');			
		}

		$this->from[$from] = $to;
		$this->to[$to] = $from;
	}

	function dispatch($pathInfo)
	{
		return isset($this->from[$pathInfo])? $this->from[$pathInfo]: false;
	}

	function reverse($uri)
	{
		return isset($this->to[$uri])? $this->to[$uri]: false;
	}

}

class RouteMap
{

	protected array $rules = [];

	function add($from, $to)
	{

		$from = ltrim($from, '/');
		$to = ltrim($to, '/');

		// Extract meta information from the pattern parameters
		preg_match_all('#{(?<name>[^:>]+):?(?<regex>[^>]+)?}#', $from, $vars);

		$placeholders = $vars[0];
		$varNames = $vars['name'];
		// Convert the pattern to a named regex
		$regexParts = [];
		for ($i = 0, $j = count($vars[0]); $i < $j; $i++) {
			$regex = $vars['regex'][$i] ?: '[^/]+';
			$namedRegex = '(?<'.$varNames[$i].'>'.$regex.')';
			$regexParts[] = $namedRegex;
		}
		$regex = str_replace(
			$placeholders,
			$regexParts,
			'#^'.$from.'$#'
		);

		$this->rules[$to] = [
			'placeholders' => $placeholders,
			'varNames' => $varNames,
			'pattern' => $from,
			'regex' => $regex,
		];

	}

	function dispatch($pathInfo, array &$params =  null)
	{
		// Apply rewrites and update the $params
		foreach ($this->rules as $internalUri => $rule) {

			if (preg_match($rule['regex'], $pathInfo, $matches)) {
				foreach ($matches as $key => $value) {
					if (!is_numeric($key)) {
						$params[$key] = $value;
					}
				}
				return $internalUri;
			}

		}

		return false;
	}

	function reverse($uri, array &$params = null)
	{

		if (isset($this->rules[$uri])) {

			$rule = $this->rules[$uri];

			$pattern = $rule['pattern'];
			$varNames = $rule['varNames'];
			$placeholders = $rule['placeholders'];

			$replaces = [];
			foreach ($varNames as $name) {
				$replaces[] = $params[$name];
				unset($params[$name]);
			}

			return str_replace($placeholders, $replaces, $pattern);
		}

		return false;

	}

}