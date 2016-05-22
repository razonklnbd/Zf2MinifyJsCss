<?php
namespace MinifyJsCss\Helper;

use stdClass;
use Zend\View\Helper\HeadLink as ZendViewHelperHeadLink;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\Url;

/**
 * Zend_Layout_View_Helper_HeadLink
 *
 * @see http://www.w3.org/TR/xhtml1/dtds.html
 *
 * Creates the following virtual methods:
 * @method HeadLink appendStylesheet($href, $media, $conditionalStylesheet, $extras)
 * @method HeadLink offsetSetStylesheet($index, $href, $media, $conditionalStylesheet, $extras)
 * @method HeadLink prependStylesheet($href, $media, $conditionalStylesheet, $extras)
 * @method HeadLink setStylesheet($href, $media, $conditionalStylesheet, $extras)
 * @method HeadLink appendAlternate($href, $type, $title, $extras)
 * @method HeadLink offsetSetAlternate($index, $href, $type, $title, $extras)
 * @method HeadLink prependAlternate($href, $type, $title, $extras)
 * @method HeadLink setAlternate($href, $type, $title, $extras)
 */
class HeadLink extends ZendViewHelperHeadLink implements ServiceLocatorAwareInterface {

	protected $serviceLocator;
	public function setServiceLocator(ServiceLocatorInterface $sl){
		$this->serviceLocator=$sl;
		return $this;
	}
	public function getServiceLocator(){ return $this->serviceLocator; }

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
	private function getMinifyUrlBase(){
		if(empty($this->minifyUrlBase)){
			if(isset($this->urlInstance)) return $this->urlInstance->_invoke('minify');
			return '/minify';
		}
		return $this->minifyUrlBase;
	}

/*	private $appBasePath;
	public function setAppBasePath($pAppBasePath){
		$this->appBasePath=$pAppBasePath;
		return $this;
	}
	private function getAppBasePath(){
		if(empty($this->appBasePath)){
			if(isset($this->urlInstance)) return $this->urlInstance->_invoke('home');
			return '/';
		}
		return $this->appBasePath;
	}*/

    public function toString($indent = null) {
        $indent = (null !== $indent)
                ? $this->getWhitespace($indent)
                : $this->getIndent();

        $items = array();
        $this->getContainer()->ksort();
        foreach ($this as $item) {
        	$attributes = (array) $item;
        	if($this->isInternalStylesheet($attributes)){
        		$attr_href=$attributes['href'];
        		$attributes['href']=$this->getMinifyUrlBase().'?css='.urlencode($attr_href);
        		$items[]=$this->itemToString($this->createData($attributes));
        	}else $items[] = $this->itemToString($item);
        }

        return $indent . implode($this->escape($this->getSeparator()) . $indent, $items);
    }
    public function ksort(){
    	return $this->getContainer()->ksort();
    }
    public function parseItemEntity($pItem){
    	$attributes = (array) $pItem;
    	if($this->isInternalStylesheet($attributes)){
    		$attr_href=$attributes['href'];
    		$attributes['href']=$this->getMinifyUrlBase().'?css='.urlencode($attr_href);
    		return $this->createData($attributes);
    	}
    	return $pItem; #$this->itemToString($pItem);
    }
	protected function isStylesheet(array $item){ return (isset($item['rel']) && 'stylesheet'==strtolower(trim($item['rel'])) && isset($item['href']) && strlen(trim($item['href']))>0); }
	protected function isInternalStylesheet(array $item){
		if($this->isStylesheet($item)){
			$arrLeadToExternal=array('http://', 'https://', '//');
			foreach($arrLeadToExternal as $x){
				if(strpos(trim($item['href']), $x)===0) return false;
			}
			return true;
		}
		return false;
	}


}
