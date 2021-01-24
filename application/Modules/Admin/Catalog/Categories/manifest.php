<?php
return [
	'vendor'      => '',
	'label'       => 'Catalog categories management',
	'description' => '',

	'ACL_actions' => [
		'get_category'    => 'View categories',
		'add_category'    => 'Add new category',
		'update_category' => 'Update category',
		'delete_category' => 'Delete category',
	],


	'pages' => [
		'admin' => [
			'categories' => [
				'title'                  => 'Categories',
				'icon'                   => 'folder-open',
				'relative_path_fragment' => 'categories',
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
				'categories' => [
					'page_id' => 'categories',
					'index'   => 1,
				],
			],
		],
	],

];