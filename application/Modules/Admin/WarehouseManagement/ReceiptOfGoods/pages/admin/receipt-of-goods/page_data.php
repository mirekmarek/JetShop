<?php
return [
	'name' => 'Receipt Of Goods',
	'is_active' => true,
	'title' => 'Receipt Of Goods',
	'menu_title' => 'Receipt Of Goods',
	'breadcrumb_title' => 'Receipt Of Goods',
	'contents' => [
		[
			'module_name' => 'Admin.WarehouseManagement.ReceiptOfGoods',
			'controller_name' => 'Main',
			'controller_action' => 'default',
			'output_position' => '__main__',
			'output_position_order' => 1,
		],
	],
	'id' => 'receipt-of-goods',
];
