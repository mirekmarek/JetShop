<?php return [
	'vendor' => '',
	'version' => '',
	'label' => 'Admin.Services.Services',
	'description' => '',
	'is_mandatory' => false,
	'ACL_actions' => [
		'get_service' => 'Get service data',
		'add_service' => 'Add new service',
		'update_service' => 'Update service',
		'delete_service' => 'Delete service',
	],
	'pages' => [
		'admin' => [
			'service' => [
				'name' => 'Service administration',
				'is_active' => true,
				'title' => 'Service administration',
				'icon' => 'plus',
				'menu_title' => 'Service administration',
				'breadcrumb_title' => 'Service administration',
				'contents' => [
					[
						'controller_name' => 'Main',
						'controller_action' => 'default',
					],
				],
				'relative_path_fragment' => 'service',
			],
		],
	],
	'menu_items' => [
		'admin' => [
			'system' => [
				'service' => [
					'index' => 70,
					'separator_before' => true,
					'page_id' => 'service',
				],
			],
		],
	],
];
