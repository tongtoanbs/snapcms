<?php
return array(
	'homepage' => array (
		'id' => 'homepage',
		'name' => 'Homepage',
		'description' => '',
		'showInCMS' => false,
		'autoAddToMenu' => array(),
		'fields' => array(
			'content' => 'text',
			'content_2' => 'text',
			'meta_keywords' => 'string',
			'meta_description' => 'string',
		),
		'rules' => array (
			array('content, content_2', 'safe'),
		),
		'groups' => array (
			'Content' => array('content', 'content_2'),
			'SEO' => array('meta_keywords','meta_description'),
		),
		'inputTypes' => array (
			'meta_description' => 'textAreaControlGroup',
		)
	),
	'page' => array (
		'id' => 'page',
		'name' => 'Page',
		'description' => 'A standard page',
		'showInCMS' => true,
		'autoAddToMenu' => array('main_menu'),
		'fields' => array(
			'content' => 'text',
			'meta_keywords' => 'string',
			'meta_description' => 'string',
		),
		'rules' => array (
			array('content', 'length', 'max'=>255),
		),
		'groups' => array (
			'Content' => array('content'),
			'SEO' => array('meta_keywords','meta_description'),
		),
		'inputTypes' => array (
			'meta_description' => 'textAreaControlGroup',
		)
	),
	'news_list' => array (
		'id' => 'news_list',
		'name' => 'News List',
		'description' => '',
		'showInCMS' => false,
		'autoAddToMenu' => array(),
		'fields' => array(
			'content' => 'text',
			'meta_keywords' => 'string',
			'meta_description' => 'string',
		),
		'rules' => array (
			array('content', 'length', 'max'=>255),
		),
		'groups' => array (
			'Content' => array('content'),
			'SEO' => array('meta_keywords','meta_description'),
		),
		'inputTypes' => array (
			'meta_description' => 'textAreaControlGroup',
		)
	),
	'news' => array (
		'id' => 'news',
		'name' => 'News',
		'description' => 'News items will appear in the news section of your website.',
		'showInCMS' => true,
		'autoAddToMenu' => array(),
		'fields' => array(
			'content' => 'text',
			'intro' => 'text',
			'meta_keywords' => 'string',
			'meta_description' => 'string',
			'image' => 'string',
			'file' => 'string',
		),
		'rules' => array (
			array('file, meta_keywords, meta_description', 'length', 'max'=>255),
			array('file', 'file'),
			array('image', 'file', 'types'=>'jpg, jpeg, gif, png'),
			array('content, intro', 'safe'),
		),
		'groups' => array (
			'Content' => array('intro','content','image','file'),
			'SEO' => array('meta_keywords','meta_description'),
		),
		'inputTypes' => array (
			'image' => 'imageField',
			'file' => 'fileField',
			'intro' => array(
				'widget'=> array(
					'class' => 'vendor.ckeditorwidget.TheCKEditorWidget',
					'settings' => SnapUtil::config('content.ckeditor/plain'),
				),
			),
			'meta_description' => 'textAreaControlGroup',
		)
	),
);