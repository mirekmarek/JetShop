<?php
return [
	'vendor'      => '',
	'label'       => 'Catalog brands management',
	'description' => '',

	'ACL_actions' => [
		'get_brand'    => 'View brands',
		'add_brand'    => 'Add new brand',
		'update_brand' => 'Update brand',
		'delete_brand' => 'Delete brand',
	],


	'pages' => [
		'admin' => [
			'brands' => [
				'title'                  => 'Brands',
				'icon'                   => 'copyright',
				'relative_path_fragment' => 'brands',
				'contents' => [
					[
						'controller_action' => 'default',
					]
				],
			],
		],
	],

	'menu_items' => [
		'admin' => [
			'catalog' => [
				'brands' => [
					'page_id' => 'brands',
					'index'   => 80,
				],
			],
		],
	],

];