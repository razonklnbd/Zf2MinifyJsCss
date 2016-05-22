<?php
namespace MinifyJsCss\Helper;

use stdClass;
use Zend\View\Helper\HeadStyle as ZendViewHelperHeadStyle;

/**
 * Helper for setting and retrieving stylesheets
 *
 * Allows the following method calls:
 * @method HeadStyle appendStyle($content, $attributes = array())
 * @method HeadStyle offsetSetStyle($index, $content, $attributes = array())
 * @method HeadStyle prependStyle($content, $attributes = array())
 * @method HeadStyle setStyle($content, $attributes = array())
 */
class HeadStyle extends ZendViewHelperHeadStyle {

	public function createData($content, array $attributes){
		$d2r=parent::createData($content, $attributes);
		$d2r->sourceOrgi=$d2r->content;
		if(!empty($d2r->content)) $d2r->content=preg_replace('#\s+#', ' ', $d2r->sourceOrgi);
		return $d2r;
	}

}
