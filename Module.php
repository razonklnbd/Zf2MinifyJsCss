<?php
namespace MinifyJsCss;

use Zend\View\HelperPluginManager;
use Zend\ServiceManager\ServiceManager;
use MinifyJsCss\Helper\HeadLink;
use MinifyJsCss\Helper\HeadScript;

class Module {

	public function getAutoloaderConfig() {
		return array(
				'Zend\Loader\ClassMapAutoloader' => array(
						__DIR__.'/autoload_classmap.php',
				),
				'Zend\Loader\StandardAutoloader' => array(
						'namespaces' => array(
								__NAMESPACE__ => __DIR__.'/src/'.__NAMESPACE__,
						),
				),
		);
	}
	
	public function getConfig() {
		return include __DIR__.'config/module.config.php';
	}

	#/*
	public function getServiceConfig() {
		$rtrn['factories']['appHttpLocation']=function (ServiceManager $sm) {
				if(defined('APP_INIT_POINT_LOCATION')) return APP_INIT_POINT_LOCATION;
				#die('got http location? @'.__LINE__.': '.__FILE__);
				if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
				if(!defined('APP_ROOT_PATH')){
					$curroot=((@__DIR__ == '__DIR__')?((@__FILE__ == '__FILE__')?realpath('.'):dirname(__FILE__)):__DIR__).DS;
					$appRootPath=realpath($curroot.'..'.DS.'..').DS;
					#die('$appRootPath: '.$appRootPath.' @'.__LINE__.': '.__FILE__);
					#throw new \Exception('please set APP_ROOT_PATH at your public index.php and adjust path here accordingle to get right path!');
				}else $appRootPath=APP_ROOT_PATH;
				#return realpath($appRootPath.'..'.DS.'..'.DS.'public_html').DS;
				return realpath($appRootPath.'public_html').DS;
				#return APP_ROOT_PATH.'..'.DS.'public_html'.DS;
			};
		return $rtrn;
	}
	#*/


	public function getViewHelperConfig() {
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

