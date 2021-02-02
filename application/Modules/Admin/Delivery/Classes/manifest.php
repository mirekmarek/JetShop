<?php return [
	'vendor' => '',
	'version' => '',
	'label' => 'Delivery classes administration',
	'description' => '',
	'is_mandatory' => false,
	'ACL_actions' => [
		'get_delivery_class' => 'Get delivery_class data',
		'add_delivery_class' => 'Add new delivery_class',
		'update_delivery_class' => 'Update delivery_class',
		'delete_delivery_class' => 'Delete delivery_class',
	],
	'pages' => [
		'admin' => [
			'delivery-class' => [
				'name' => 'Delivery Class administration',
				'is_active' => true,
				'title' => 'Delivery Class administration',
				'icon' => 'box',
				'menu_title' => 'Delivery Class administration',
				'breadcrumb_title' => 'Delivery Class administration',
				'contents' => [
					[
						'controller_name' => 'Main',
						'controller_action' => 'default',
					],
				],
				'relative_path_fragment' => 'delivery-class',
			],
		],
	],
	'menu_items' => [
		'admin' => [
			'system' => [
				'delivery-class' => [
					'index' => 50,
					'page_id' => 'delivery-class',
				],
			],
		],
	],
];
