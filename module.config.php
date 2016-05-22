<?php
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

return array(
		'controllers' => array(
				'invokables' => array(
						'MinifyJsCss\\Controller\\MinifyJsCss' => 'MinifyJsCss\\Controller\\MinifyJsCssController',
						#'Wurfl\\Controller\\' => 'Wurfl\\Controller\\Controller',
				),
		),
		'router' => array(
				'routes' => array(
						'minify' => array(
								'type'    => 'segment',
								'options' => array(
										'route'    => '/minify[/:action[/:files]]',
										'defaults' => array(
												'controller' => 'MinifyJsCss\\Controller\\MinifyJsCss',
												'action'     => 'index',
										),
								),
						),
				),
		),
		'view_manager' => array(
				'template_path_stack' => array(
						'minify' => realpath(__DIR__ . DS.'..'.DS.'view').DS,
				),
		),
);