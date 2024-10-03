<?php
return [
	'name' => 'Stock verification',
	'icon' => 'list-check',
	'is_active' => true,
	'title' => 'Stock verification',
	'contents' => [
		[
			'controller_name' => 'Main',
			'controller_action' => 'default',
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
	'id' => 'stock-verification',
	'order' => 9999,
];
