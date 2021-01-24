<?php
return [
	'vendor'      => '',
	'label'       => 'Catalog delivery terms management',
	'description' => '',

	'ACL_actions' => [
		'get_delivery_term'    => 'View delivery terms',
		'add_delivery_term'    => 'Add new delivery term',
		'update_delivery_term' => 'Update delivery term',
		'delete_delivery_term' => 'Delete delivery term',
	],


	'pages' => [
		'admin' => [
			'delivery_terms' => [
				'title'                  => 'Delivery terms',
				'icon'                   => 'calendar-alt',
				'relative_path_fragment' => 'delivery-terms',
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
				'delivery_terms' => [
					'page_id' => 'delivery_terms',
					'index'   => 100,
				],
			],
		],
	],

];