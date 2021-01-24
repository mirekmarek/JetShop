<?php
return [
	'vendor'      => '',
	'label'       => 'Catalog suppliers management',
	'description' => '',

	'ACL_actions' => [
		'get_supplier'    => 'View suppliers',
		'add_supplier'    => 'Add new supplier',
		'update_supplier' => 'Update supplier',
		'delete_supplier' => 'Delete supplier',
	],


	'pages' => [
		'admin' => [
			'suppliers' => [
				'title'                  => 'Suppliers',
				'icon'                   => 'truck',
				'relative_path_fragment' => 'suppliers',
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
				'suppliers' => [
					'page_id' => 'suppliers',
					'index'   => 90,
				],
			],
		],
	],

];