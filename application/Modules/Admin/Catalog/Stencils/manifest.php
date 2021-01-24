<?php
return [
	'vendor'      => '',
	'label'       => 'Catalog stencils management',
	'description' => '',

	'ACL_actions' => [
		'get_stencil'    => 'View stencils',
		'add_stencil'    => 'Add new stencil',
		'update_stencil' => 'Update stencil',
		'delete_stencil' => 'Delete stencil',
	],


	'pages' => [
		'admin' => [
			'stencils' => [
				'title'                  => 'Stencils',
				'icon'                   => 'swatchbook',
				'relative_path_fragment' => 'stencils',
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
				'stencils' => [
					'page_id' => 'stencils',
					'index'   => 70,
				],
			],
		],
	],

];