<?php
namespace MinifyJsCss;

use WMS\WmsModuleBase;
use Zend\Mvc\MvcEvent;
use Zend\View\HelperPluginManager;
use Wurfl\View\Helper\WurflHelper;
use MinifyJsCss\Helper\HeadLink;
use Zend\ServiceManager\ServiceManager;
use MinifyJsCss\Helper\HeadScript;

class Module extends WmsModuleBase {
	function getNameSpace(){ return __NAMESPACE__; }
	function getCurrentModulePath(){ return __DIR__ . DS; }
	protected function isModuleUsingSSL(){ return $this->isServerUsingSSL(); }

/*
	public function preControllerExecute(MvcEvent $e){
		$rtrn=parent::preControllerExecute($e);
		if(empty($rtrn)) return ;
		
		return $this;
	}
	 'view_helpers' => array(
      'invokables' => array(
         'lowercase' => 'MyModule\View\Helper\LowerCase',
         'uppercase' => 'MyModule\View\Helper\UpperCase',
      ),
   ),
#*/

/*
	public function getServiceConfig() {
		$rtrn=parent::getServiceConfig();
		$rtrn['factories']['appHttpLocation']=function (ServiceManager $sm) {
				if(!defined('APP_ROOT_PATH')) throw new \Exception('please set APP_ROOT_PATH at your public index.php and adjust path here accordingle to get right path!');
				return realpath(APP_ROOT_PATH.'..'.DS.'..'.DS.'public_html').DS;
				#return APP_ROOT_PATH.'..'.DS.'public_html'.DS;
			};
		return $rtrn;
	}
#*/
	public function getViewHelperConfig() {
		$rtrn=parent::getViewHelperConfig();
		$rtrn['invokables']['headStyle']='MinifyJsCss\\Helper\\HeadStyle';
		$rtrn['factories']['headScript']=function (HelperPluginManager $sm) {
						$url=$sm->getRenderer()->plugin('url');
						$hdLink=new HeadScript();
						$hdLink->setViewHelperUrl($url);
						$hdLink->setMinifyUrlBase($url('minify'));
						return $hdLink;
					};
		$rtrn['factories']['headLink']=function (HelperPluginManager $sm) {
						$url=$sm->getRenderer()->plugin('url');
						$hdLink=new HeadLink();
						$hdLink->setViewHelperUrl($url);
						#die('here i am @'.__LINE__.': '.__FILE__);
						$hdLink->setMinifyUrlBase($url('minify'));
						#die('here i am - '.get_class($url).' @'.__LINE__.': '.__FILE__);
						return $hdLink;
					};
		return $rtrn;
	}

}

