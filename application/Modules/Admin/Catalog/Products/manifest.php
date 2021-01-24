<?php
return [
	'vendor'      => '',
	'label'       => 'Catalog products management',
	'description' => '',

	'ACL_actions' => [
		'get_product'    => 'View products',
		'add_product'    => 'Add new product',
		'update_product' => 'Update product',
		'delete_product' => 'Delete product',
	],


	'pages' => [
		'admin' => [
			'products' => [
				'title'                  => 'Products',
				'icon'                   => 'box',
				'relative_path_fragment' => 'products',
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
				'products' => [
					'page_id' => 'products',
					'index'   => 2,
				],
			],
		],
	],

];