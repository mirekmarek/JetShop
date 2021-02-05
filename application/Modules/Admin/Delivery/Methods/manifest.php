<?php return [
	'vendor' => '',
	'version' => '',
	'label' => 'Admin.System.DeliveryMethod',
	'description' => '',
	'is_mandatory' => false,
	'ACL_actions' => [
		'get_delivery_method' => 'Get delivery_method data',
		'add_delivery_method' => 'Add new delivery_method',
		'update_delivery_method' => 'Update delivery_method',
		'delete_delivery_method' => 'Delete delivery_method',
	],
	'pages' => [
		'admin' => [
			'delivery-method' => [
				'name' => 'Delivery Method administration',
				'is_active' => true,
				'title' => 'Delivery Method administration',
				'icon' => 'truck-moving',
				'menu_title' => 'Delivery Method administration',
				'breadcrumb_title' => 'Delivery Method administration',
				'contents' => [
					[
						'controller_name' => 'Main',
						'controller_action' => 'default',
					],
				],
				'relative_path_fragment' => 'delivery-method',
			],
		],
	],
	'menu_items' => [
		'admin' => [
			'system' => [
				'delivery-method' => [
					'index' => 51,
					'page_id' => 'delivery-method',
				],
			],
		],
	],
];
