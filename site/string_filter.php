<?php
/**
 * @package    SkroutzEasy component for Joomla 2.5.x and 3.x
 * @copyright  Copyright (c) 2012 Skroutz S.A. - http://www.skroutz.gr
 * @link       http://developers.skroutz.gr/oauth2
 * @license    MIT
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class StringFilter
{
	private $filter;

	function __construct($filter)
	{
		$this->filter = $filter;
	}

	function prefix($item)
	{
		return !strncmp($item, $this->filter, strlen($this->filter));
	}
}
