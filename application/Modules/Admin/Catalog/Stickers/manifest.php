<?php
return [
	'vendor'      => '',
	'label'       => 'Catalog stickers management',
	'description' => '',

	'ACL_actions' => [
		'get_sticker'    => 'View stickers',
		'add_sticker'    => 'Add new sticker',
		'update_sticker' => 'Update sticker',
		'delete_sticker' => 'Delete sticker',
	],


	'pages' => [
		'admin' => [
			'stickers' => [
				'title'                  => 'Stickers',
				'icon'                   => 'sticky-note',
				'relative_path_fragment' => 'stickers',
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
				'stickers' => [
					'page_id' => 'stickers',
					'index'   => 80,
				],
			],
		],
	],

];