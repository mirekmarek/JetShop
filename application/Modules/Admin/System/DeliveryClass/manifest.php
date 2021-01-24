<?php
return [
	'vendor'      => '',
	'label'       => 'Delivery classes administration',
	'description' => '',
	
	'ACL_actions' => [
		'get_delivery_class'    => 'Get delivery_class data',
		'add_delivery_class'    => 'Add new delivery_class',
		'update_delivery_class' => 'Update delivery_class',
		'delete_delivery_class' => 'Delete delivery_class',
	],
	

	'pages' => [
		'admin' => [
			'delivery-class' => [
				'title'                  => 'Delivery Class administration',
				'icon'                   => '',
				'relative_path_fragment' => 'delivery-class',
				'contents' => [
					[
						'controller_action' => 'default'
					]
				],
			],
		],
	],

	'menu_items' => [
		'admin' => [
			'system' => [
				'delivery-class' => [
					'separator_before' => true,
					'page_id'          => 'delivery-class',
					'index'            => 200,
				],
			],
		],
	],

];