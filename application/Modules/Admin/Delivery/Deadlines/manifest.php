<?php
return [
	'vendor'      => '',
	'label'       => 'Catalog delivery deadlines management',
	'description' => '',

	'ACL_actions' => [
		'get_delivery_deadline'    => 'View delivery deadlines',
		'add_delivery_deadline'    => 'Add new delivery deadline',
		'update_delivery_deadline' => 'Update delivery deadline',
		'delete_delivery_deadline' => 'Delete delivery deadline',
	],


	'pages' => [
		'admin' => [
			'delivery_deadlines' => [
				'title'                  => 'Delivery Deadlines administration',
				'icon'                   => 'calendar-alt',
				'relative_path_fragment' => 'delivery-deadlines',
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
			'system' => [
				'delivery_deadlines' => [
					'page_id' => 'delivery_deadlines',
					'index'   => 52,
				],
			],
		],
	],

];