<?php

namespace pp\Form;

class ViewHelper
{

	static $paths = [];

	static function resolve_path($file)
	{

	    foreach (static::$paths as $path) {
	        if ($fullpath = stream_resolve_include_path($path . '/' . $file)) {
	            return $fullpath;
	        }
	    }

	    return false;
	}

	static function label_for(Element $element, array $attr = [])
	{
		if (!isset($attr['for'])) {
			$attr['for'] = $element->attributes['id'];
		}
		$label = $element->getLabel();
		if (@$element->extra['label:allow_html'] !== true) {
			$label = htmlspecialchars($element->getLabel());
		}
		return sprintf('<label %s>%s</label>', static::attr($attr), $label);
	}

	static function attr_for(Element $element, array $attr = [])
	{
		return attr($element->attributes, $attr);
	}

	static function attr(...$attributes)
	{
	
		$output = [];
	
		foreach($attributes as $attr)
		{
			foreach($attr as $name => $value) {
	
				if ($value === true) {
					
					$output[] = htmlspecialchars($name);
	
				} else {
					$output[] = sprintf('%s="%s"', htmlspecialchars($name), htmlspecialchars($value));
				}
	
			}
		}
	
		return implode(' ', $output);
	}

}
