<?php
namespace MinifyJsCss\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Response;
class MinifyJsCssController extends AbstractActionController {
	private $appHttpLocation;
	private function getAppHttpLocation(){
		if(!isset($this->appHttpLocation)) $this->appHttpLocation=$this->getServiceLocator()->get('appHttpLocation');
		return $this->appHttpLocation;
	}
	public function indexAction(){
		#die('@'.__LINE__.': '.__FILE__.'<pre>'.print_r($this->getRequest()->getHeaders()->toArray(), true));
		#die('$basePath = $this->getRequest()->getBasePath();: '.$this->getRequest()->getBasePath());
		$basePath=$this->getRequest()->getBasePath();
		if(empty($basePath)) $basePath='/';
		#die('$basePath: '.$basePath);
		$response=$this->getResponse();
		$getQuery=$this->getRequest()->getUri()->getQueryAsArray();
		$appHttpLctn=$this->getAppHttpLocation();
		#die('$appHttpLctn: '.$appHttpLctn);
		$getResponseByReadingFile=function($pQryVar, $pContentType) use ($response, $getQuery, $appHttpLctn, $basePath){
			$errCssFiles=$cssFiles=array();
			$cssFileCsv=explode(',', $getQuery[$pQryVar]);
			$maxFlTime=0;
			$appHttpLctnX=rtrim($appHttpLctn, DS);
			foreach($cssFileCsv as $css){
				#echo 'ltrim(trim($css), $basePath): '.str_replace(array('/', '\\'), DS, ltrim(trim($css), $basePath)).'<br />';
				$css=trim(str_replace(array('/', '\\'), DS, $css), DS);
				$flLoc=$appHttpLctnX.DS.str_replace(array('/', '\\'), DS, $css);
				if($basePath!='/') $flLoc=$appHttpLctnX.DS.str_replace(array('/', '\\'), DS, substr($css, strlen($basePath)));
				#$flLoc=$appHttpLctn.str_replace(array('/', '\\'), DS, ltrim(trim($css), $basePath));
				if(file_exists($flLoc)){
					$flTime=filemtime($flLoc);
					$cssFiles[$flLoc]=array('location'=>str_replace(array('/', '\\'), DS, $flLoc), 'mtime'=>$flTime);
					if($maxFlTime<$flTime) $maxFlTime=$flTime;
				}else $errCssFiles[]=$flLoc;
			}
			#die('$$appHttpLctn: '.$appHttpLctn.'<pre>'.print_r($cssFiles, true));
			if(!empty($cssFiles)){
				ksort($cssFiles);
				$eTag=md5(implode(',', array_keys($cssFiles)));
				if(!$this->handleCache($eTag)){
					$flContents='';
					foreach($cssFiles as $cssFl) $flContents.='/* location: '.$cssFl['location'].' */'.file_get_contents($cssFl['location']);
					$response->getHeaders()
					->addHeaderLine('Content-Transfer-Encoding', 'binary')
					->addHeaderLine('Content-Type', $pContentType)
					;
					$response->setContent('/* '.$pQryVar.' file from minify. time: '.time().' */'.preg_replace('#\s+#', ' ', $flContents));
				}
			}else{
				$response->getHeaders()
				->addHeaderLine('Content-Transfer-Encoding', 'binary')
				->addHeaderLine('Content-Type', $pContentType)
				->addHeaderLine('Expires', '')
				->addHeaderLine('Cache-Control', 'public')
				->addHeaderLine('Cache-Control', 'max-age=1800', true)
				->addHeaderLine('Pragma', '')
				;
				$response->setContent('/* blank '.$pQryVar.' file from minify! time: '.time().' ['.$getQuery[$pQryVar].'] locations - '.implode(' | ', $errCssFiles).' */');
			}
			return $response;
		};
		if(array_key_exists('css', $getQuery)) return $getResponseByReadingFile('css', 'text/css');
		if(array_key_exists('js', $getQuery)) return $getResponseByReadingFile('js', 'text/javascript');
		if($this->handleCache(md5(__FILE__))){
			#die('sent 304');
			return $response;
		}
		$response->setContent('zf2 js and css minify project. cur time: '.time());
		return $response;
	}
	private function workingIndexCode(){

		if(array_key_exists('css', $getQuery)){
			$errCssFiles=$cssFiles=array();
			$cssFileCsv=explode(',', $getQuery['css']);
			$maxFlTime=0;
			foreach($cssFileCsv as $css){
				$css=rtrim($css, '/');
				$css=rtrim($css, '\\');
				$flLoc=$appHttpLctn.ltrim($css, '/');
				if($basePath!='/') $flLoc=$appHttpLctn.substr($css, strlen($basePath)+1);
				if(file_exists($flLoc)){
					$flTime=filemtime($flLoc);
					$cssFiles[$flLoc]=array('location'=>str_replace(array('/', '\\'), DS, $flLoc), 'mtime'=>$flTime);
					if($maxFlTime<$flTime) $maxFlTime=$flTime;
				}else $errCssFiles[]=$flLoc;
			}
			#die('$$appHttpLctn: '.$appHttpLctn.'<pre>'.print_r(array($basePath, $cssFiles, $errCssFiles), true));
			if(!empty($cssFiles)){
				ksort($cssFiles);
				$eTag=md5(implode(',', array_keys($cssFiles)));
				if(!$this->handleCache($eTag)){
					$flContents='';
					foreach($cssFiles as $cssFl) $flContents.='/* location: '.$cssFl['location'].' */'.file_get_contents($cssFl['location']);
					$response->getHeaders()
					->addHeaderLine('Content-Transfer-Encoding', 'binary')
					->addHeaderLine('Content-Type', 'text/css')
					;
					$response->setContent('/* css file from minify. time: '.time().' */'.preg_replace('#\s+#', ' ', $flContents));
				}
			}else{
				$response->getHeaders()
				->addHeaderLine('Content-Transfer-Encoding', 'binary')
				->addHeaderLine('Content-Type', 'text/css')
				->addHeaderLine('Expires', '')
				->addHeaderLine('Cache-Control', 'public')
				->addHeaderLine('Cache-Control', 'max-age=1800', true)
				->addHeaderLine('Pragma', '')
				;
				$response->setContent('/* blank css file from minify! time: '.time().' ['.$getQuery['css'].'] locations - '.implode(' | ', $errCssFiles).' */');
			}
			return $response;
		}
		if(array_key_exists('js', $getQuery)){
			$jsFiles=array();
			$cssFileCsv=explode(',', $getQuery['js']);
			$maxFlTime=0;
			foreach($cssFileCsv as $css){
				$css=rtrim($css, '/');
				$css=rtrim($css, '\\');
				$flLoc=$appHttpLctn.ltrim($css, '/');
				if($basePath!='/') $flLoc=$appHttpLctn.substr($css, strlen($basePath)+1);
				#$flLoc=$appHttpLctn.ltrim(trim($css), $basePath);
				if(file_exists($flLoc)){
					$flTime=filemtime($flLoc);
					$jsFiles[$flLoc]=array('location'=>str_replace(array('/', '\\'), DS, $flLoc), 'mtime'=>$flTime);
					if($maxFlTime<$flTime) $maxFlTime=$flTime;
				}
			}
			#die('$$appHttpLctn: '.$appHttpLctn.'<pre>'.print_r($cssFiles, true));
			if(!empty($jsFiles)){
				ksort($jsFiles);
				$eTag=md5(implode(',', array_keys($jsFiles)));
				if(!$this->handleCache($eTag)){
					$flContents='';
					foreach($jsFiles as $cssFl) $flContents.='/* location: '.$cssFl['location'].' */'.file_get_contents($cssFl['location']);
					$response->getHeaders()
					->addHeaderLine('Content-Transfer-Encoding', 'binary')
					->addHeaderLine('Content-Type', 'text/javascript')
					;
					$response->setContent('/* js file from minify. time: '.time().' */'.preg_replace('#\s+#', ' ', $flContents));
				}
			}else{
				$response->getHeaders()
				->addHeaderLine('Content-Transfer-Encoding', 'binary')
				->addHeaderLine('Content-Type', 'text/javascript')
				->addHeaderLine('Expires', '')
				->addHeaderLine('Cache-Control', 'public')
				->addHeaderLine('Cache-Control', 'max-age=1800', true)
				->addHeaderLine('Pragma', '')
				;
				$response->setContent('/* blank js file from minify. time: '.time().' ['.$getQuery['js'].'] */');
			}
			return $response;
		}
	}
	private function isCacheExists($last_modified_time, $etag=NULL){
		$reqHeaders=$this->getRequest()->getHeaders()->toArray();
		#$srvrIfModSince=$this->getRequest()->getHeaders()->get('HTTP_IF_MODIFIED_SINCE');
		$srvrIfModSince=(array_key_exists('If-Modified-Since', $reqHeaders)?$reqHeaders['If-Modified-Since']:'');
		#$srvrIfModSince=$this->getRequest()->getHeaders()->get('If-Modified-Since');
		#$srvrIfModSince=(array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER)?trim($_SERVER['HTTP_IF_MODIFIED_SINCE']):'');
		#$srvrEtag=$this->getRequest()->getHeaders()->get('HTTP_IF_NONE_MATCH');
		$srvrEtag=(array_key_exists('If-None-Match', $reqHeaders)?$reqHeaders['If-None-Match']:'');
		#$srvrEtag=$this->getRequest()->getHeaders()->get('If-None-Match');
		#$srvrEtag=(array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER)?trim($_SERVER['HTTP_IF_NONE_MATCH']):'');
		#die('$srvrIfModSince: '.$srvrIfModSince.' | $srvrEtag: '.$srvrEtag);
		if ((!empty($srvrIfModSince) && @strtotime($srvrIfModSince) == $last_modified_time) &&
				(!empty($etag) && $srvrEtag == $etag)) {
					return true;
				}
		return false;
	}
	private function handleCache($etag=NULL, $pMaxSecToCacheFor=300){
		$curTime=time();
		$modCurTime=$curTime%$pMaxSecToCacheFor;
		$last_modified_time=($curTime-$modCurTime);
		if(empty($etag)) $etag=md5($last_modified_time);
		if ($this->isCacheExists($last_modified_time, $etag)) {
			$this->getResponse()->getHeaders()->addHeaderLine('Last-Modified', gmdate('D, d M Y H:i:s', $last_modified_time).' GMT');
			#header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
			if(!empty($etag)) $this->getResponse()->getHeaders()->addHeaderLine('Etag', $etag);
			$this->getResponse()->getHeaders()->addHeaderLine('Pragma', 'public');
			$this->getResponse()->getHeaders()->addHeaderLine('Vary', 'Accept-Encoding');
			$this->getResponse()->getHeaders()->addHeaderLine('Cache-control', 'public');
			$this->getResponse()->getHeaders()->addHeaderLine('Cache-control', 'max-age=' . (60*60*24*14), true);
			$this->getResponse()->getHeaders()->addHeaderLine('Connection', 'Keep-Alive');
			$this->getResponse()->setStatusCode(Response::STATUS_CODE_304);
			#echo '<pre>'; var_dump($this->getRequest()->getHeaders()->toArray());
			#die("HTTP/1.1 304 Not Modified");
			return true;
		}
		$this->getResponse()->getHeaders()->addHeaderLine('Last-Modified', gmdate('D, d M Y H:i:s', $last_modified_time).' GMT');
		#header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
		if(!empty($etag)) $this->getResponse()->getHeaders()->addHeaderLine('Etag', $etag);
		#if(!empty($etag)) header('Etag: '.$etag);
		$this->getResponse()->getHeaders()->addHeaderLine('Pragma', 'public');
		$this->getResponse()->getHeaders()->addHeaderLine('Vary', 'Accept-Encoding');
		$this->getResponse()->getHeaders()->addHeaderLine('Cache-control', 'public');
		$this->getResponse()->getHeaders()->addHeaderLine('Cache-control', 'max-age=' . (60*60*24*14), true); # 'public', true); # 'max-age=' . (60*60*24*14), true);
		$this->getResponse()->getHeaders()->addHeaderLine('Expires', gmdate('D, d M Y H:i:s', ($curTime+$modCurTime)).' GMT');
		$this->getResponse()->getHeaders()->addHeaderLine('Connection', 'Keep-Alive');
		#$this->getResponse()->getHeaders()->addHeaderLine('Cache-Control', 'public');
		#header('Cache-Control: public');
		#echo '<pre>'; var_dump($this->getResponse()->getHeaders());
		#die('cache not exists... @'.__LINE__.': '.__FILE__);
		return false;
	}
}

