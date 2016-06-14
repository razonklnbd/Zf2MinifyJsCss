<?php
namespace MinifyJsCss\Helper;

use stdClass;
use Zend\View\Helper\HeadScript as ZendViewHelperHeadScript;
use Zend\View\Helper\Url;

/**
 * Helper for setting and retrieving script elements for HTML head section
 *
 * Allows the following method calls:
 * @method HeadScript appendFile($src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript offsetSetFile($index, $src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript prependFile($src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript setFile($src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript appendScript($script, $type = 'text/javascript', $attrs = array())
 * @method HeadScript offsetSetScript($index, $src, $type = 'text/javascript', $attrs = array())
 * @method HeadScript prependScript($script, $type = 'text/javascript', $attrs = array())
 * @method HeadScript setScript($script, $type = 'text/javascript', $attrs = array())
 */
class HeadScript extends ZendViewHelperHeadScript {

	private $urlInstance;
	public function setViewHelperUrl(Url $pUrl){
		$this->urlInstance=$pUrl;
		return $this;
	}

	protected $minifyUrlBase;
	public function setMinifyUrlBase($pUrlBase){
		$this->minifyUrlBase=$pUrlBase;
		return $this;
	}
	protected function getMinifyUrlBase(){
		if(empty($this->minifyUrlBase)){
			if(isset($this->urlInstance)) return $this->urlInstance->_invoke('minify');
			return '/minify';
		}
		return $this->minifyUrlBase;
	}

	protected function isInternalScripts($item){
		$arrLeadToExternal=array('http://', 'https://', '//');
		foreach($arrLeadToExternal as $x){
			if(strpos(trim($item), $x)===0) return false;
		}
		return true;
	}

	/**
     * Create data item containing all necessary components of script
     *
     * @param  string $type       Type of data
     * @param  array  $attributes Attributes of data
     * @param  string $content    Content of data
     * @return stdClass
     */
    public function createData($type, array $attributes, $content = null) {
    	$rtrn=parent::createData($type, $attributes, $content);
    	$rtrn->sourceOrgi=$rtrn->source;
    	if(!empty($rtrn->source)) $rtrn->source=preg_replace('#\s+#', ' ', $rtrn->sourceOrgi);
        return $rtrn;
    }

    /**
     * Create script HTML
     *
     * @param  mixed  $item        Item to convert
     * @param  string $indent      String to add before the item
     * @param  string $escapeStart Starting sequence
     * @param  string $escapeEnd   Ending sequence
     * @return string
     */
    public function itemToString($item, $indent, $escapeStart, $escapeEnd) {
    	#die('<pre>'.print_r($item, true));
    	if(!empty($item->type) && !empty($item->attributes)){
    		$itmAttribs=$item->attributes;
    		$getValueFromAttribs=function($pKey) use ($itmAttribs) {
    			$src='';
    			foreach ($itmAttribs as $key => $value){
    				if($key==$pKey && !empty($value)){
    					$src=$value;
    					break;
    				}
    			}
    			return $src;
    		};
    		$doMinify=true;
    		$minify=$getValueFromAttribs('minify');
    		if(!empty($minify)){
    			unset($item->attributes['minify']);
    			$item->cmdMinify=$minify;
    			if(preg_match('#\bignore\b#', $minify)) $doMinify=false; # die('ignoring request found! @'.__LINE__.' ['.time().']: '.__FILE__);
    			#die('got minify attribs: '.$minify.' @'.__LINE__.': '.__FILE__);
    		}
    		$src=$getValueFromAttribs('src');
    		if(true==$doMinify && !empty($src) && $this->isInternalScripts($src)){
    			# $attributes['href']=$this->getMinifyUrlBase().'?css='.urlencode($attributes['href']);
    			$item->attributes['src']=$this->getMinifyUrlBase().'?js='.urlencode($src);
    			#$item->attributes['defer']=true;
    		}
    	}
    	#$this->setAllowArbitraryAttributes(true);
    	/**
    	 * ignore $escapeStart and $escapeEnd
    	 * because we minify the script
    	 */
    	return parent::itemToString($item, $indent, '', '');
    }

}
