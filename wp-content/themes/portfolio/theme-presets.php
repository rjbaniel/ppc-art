<?php
// make sure to not include translations
$args['presets']['default'] = array(
	'title' => 'Default',
	'demo' => 'http://demo.mythemeshop.com/portfolio/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/default/thumb.jpg', // could use external url, to minimize theme zip size
	'menus' => array( 'secondary-menu' => 'Primary Menu' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'posts', 'posts_per_page' => 4 ),
);

$args['presets']['blog'] = array(
	'title' => 'Blog Layout',
	'demo' => 'http://demo.mythemeshop.com/portfolio-blog/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/blog/thumb.jpg', // could use external url, to minimize theme zip size
	'menus' => array( 'secondary-menu' => 'Primary Menu' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'page', 'page_on_front' => 4, 'posts_per_page' => 4 ),
);

global $mts_presets;
$mts_presets = $args['presets'];
