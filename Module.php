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

