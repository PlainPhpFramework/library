<?php
/**
 * Some helper functions
 */

/**
 * Load the variable defined in a config file once and return it on every call
 * 
 * Usage:
 * 
 * $db = get('db');
 * 
 * // Load the db object defined in config/db.php
 */
function get($name) {
	static $loaded = [];

	// The config is already loaded
	if (isset($loaded[$name])) {
		return $loaded[$name];
	}

	// Sanitize the file name and include it
	$name = preg_replace("~[^a-z0-9-_]+~i", '', $name);
	$loaded[$name] = @include 'config/'.$name.'.php';

	// If the include file doesn't return any value, cache it anyway
	if (!isset($loaded[$name])) {
		$loaded[$name] = false;
	}

	// return the value
	return $loaded[$name];

}

function abort(int $statusCode = 404, Throwable $exception = null) 
{

		// Flush the current output buffer
		while(ob_get_level()) ob_end_clean();

		// Set the status code
		http_response_code($statusCode);

		// Search for a specific template or fallback in a generic one
		$template = sprintf('view/abort.%d.php', (int) $statusCode);
		$template = stream_resolve_include_path($template);
		if ($template === false) {
			$template = 'view/abort.php';
		}

		// Log the issue
		error_log('Abort: ' . $statusCode . '; ' . $exception);

		// Display the abort page and exit
		ob_start();
		include $template;
		die;

}

function redirect(string $location, int $statusCode = 302) {
	header('Location: '.$location, true, $statusCode);
	die;
}


function redirect_for($name, array $args = [], int $statusCode = 302) {
	redirect(url_for($name, $args), $statusCode);
}

function url($path = null, array $args = []) {
    $url = BASE_URL . ltrim($path, '/');
    if ($args) {
    	$url .= '?'.http_build_query($args); 
    }
    return $url;
}

function url_for($name, array $args = []) {
	$name = ltrim($name, '/');
	$urls = get('urls');
	if (isset($urls[$name])) {
		$url = $urls[$name]($args);
	} else {
		$url = url($name, $args);
	}
    return $url;
}

function current_url(array $args = []) {
    return url($_SERVER['PATH_INFO'], $args);
}

function asset($path) {
    $source = PUB . '/' . ltrim($path, '/');
    return url($path) . '?' . filemtime($source);
}

function slugify($text, $separator = '-') {
    return get('slugify')->slugify($text, $separator);
}

function e($string) {
	return htmlspecialchars($string, ENT_QUOTES|ENT_HTML5, 'UTF-8');
}

function render($template, array $vars = []) {
	extract($vars);
	include $template;
}

function fetch($template, array $vars = []) {
	ob_start();
	render($template, $vars);
	return ob_get_clean();
}

function get_ob() {
	$ob = ob_get_clean();
	ob_start();
	return $ob;
}