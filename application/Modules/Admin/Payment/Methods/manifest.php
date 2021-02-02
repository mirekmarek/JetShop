<?php return [
	'vendor' => '',
	'version' => '',
	'label' => 'Admin.System.PaymentMethod',
	'description' => '',
	'is_mandatory' => false,
	'ACL_actions' => [
		'get_payment_method' => 'Get payment_method data',
		'add_payment_method' => 'Add new payment_method',
		'update_payment_method' => 'Update payment_method',
		'delete_payment_method' => 'Delete payment_method',
	],
	'pages' => [
		'admin' => [
			'payment-method' => [
				'name' => 'Payment Method administration',
				'is_active' => true,
				'title' => 'Payment Method administration',
				'icon' => 'money-check-alt',
				'menu_title' => 'Payment Method administration',
				'breadcrumb_title' => 'Payment Method administration',
				'contents' => [
					[
						'controller_name' => 'Main',
						'controller_action' => 'default',
					],
				],
				'relative_path_fragment' => 'payment-method',
			],
		],
	],
	'menu_items' => [
		'admin' => [
			'system' => [
				'payment-method' => [
					'index' => 60,
					'separator_before' => true,
					'page_id' => 'payment-method',
				],
			],
		],
	],
];
