<?php /** @noinspection SpellCheckingInspection */

/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\Data_DateTime;
use Jet\Db;
use Jet\Factory_Db;

require __DIR__.'/../application/bootstrap_cli_service.php';


/*
const CZ_LANG_ID = 4;
const SK_LANG_ID = 5;

$languages = [
	CZ_LANG_ID,
	SK_LANG_ID
];

$lng_to_shop_map = [
	CZ_LANG_ID => 'cz_cs_CZ',
	SK_LANG_ID => 'sk_sk_SK',
];

$vat_rates = [
	CZ_LANG_ID => [
		3 => 0,
		5 => 10,
		4 => 15,
		2 => 21,
	],
	SK_LANG_ID => [
		3 => 0,
		5 => 10,
		2 => 20,
	]
];



$tabs = [
	'categories'                                            => 'categories',
	'categories_description'                                => 'categories_description',
	'categories_properties_groups'                          => 'categories_properties_groups',
	'categories_properties_groups_description'              => 'categories_properties_groups_description',
	'categories_properties_properties'                      => 'categories_properties_properties',
	'categories_properties_properties_description'          => 'categories_properties_properties_description',
	'categories_properties_properties_options'              => 'categories_properties_properties_options',
	'categories_properties_properties_options_description'  => 'categories_properties_properties_options_description',

	'manufacturers'                                         => 'manufacturers',

	'products'                                              => 'products',
	'products_description'                                  => 'products_description',
	'products_sets'                                         => 'products_sets',
	'products_properties'                                   => 'products_properties',
	'products_to_categories'                                => 'products_to_categories',
	
	
	'top_categories'                                        => 'top_categories',
	'top_categories_description'                            => 'top_categories_description',
	
	'products_reviews'                                      => 'products_reviews',

	'products_questions'                                    => 'products_questions',
];

$old_db_config = Factory_Db::getBackendConfigInstance([
	'driver' => 'mysql',
	'host' => 'localhost',
	'port' => 3306,
	'dbname' => 'sexshop',
	'charset' => 'utf8',
	'username' => 'root',
	'password' => 'nemeckadoga',
	'name' => 'old_shop',
]);

$product_status_columns =
"products_status,
cz_view,
sk_view,";

$productIActive = function( array $old_item ) : bool {
	return (bool)$old_item['products_status'];
};

$productShopDataIsActive = function( array $old_item, $lang_id ) {
	return match ($lang_id) {
		CZ_LANG_ID => (bool)$old_item['cz_view'],
		SK_LANG_ID => (bool)$old_item['sk_view'],
		default => false,
	};
};

$category_cactive_column = 'category_view';
*/

const CZ_LANG_ID = 5;
const SK_LANG_ID = 20;

const CZ_SHOP_ID = 1;
const SK_SHOP_ID = 2;

$languages = [
	CZ_LANG_ID,
	SK_LANG_ID
];

$old_shops = [
	CZ_SHOP_ID,
	SK_SHOP_ID
];

$lng_to_shop_map = [
	CZ_LANG_ID => 'cz_cs_CZ',
	SK_LANG_ID => 'sk_sk_SK',
];

$old_shop_id_to_shop_map = [
	CZ_SHOP_ID => 'cz_cs_CZ',
	SK_SHOP_ID => 'sk_sk_SK',
];


$vat_rates = [
	CZ_LANG_ID => [
		3 => 0,
		5 => 10,
		4 => 15,
		2 => 21,
	],
	SK_LANG_ID => [
		3 => 0,
		5 => 10,
		2 => 20,
	]
];


$tabs = [
	'categories'                                            => 'categories_sport',
	'categories_description'                                => 'categories_description_sport',
	'categories_properties_groups'                          => 'categories_properties_groups',
	'categories_properties_groups_description'              => 'categories_properties_groups_description',
	'categories_properties_properties'                      => 'categories_properties_properties',
	'categories_properties_properties_description'          => 'categories_properties_properties_description',
	'categories_properties_properties_options'              => 'categories_properties_properties_options',
	'categories_properties_properties_options_description'  => 'categories_properties_properties_options_description',
	
	'manufacturers'                                         => 'manufacturers_sport',
	
	'products'                                              => 'products_sport',
	'products_description'                                  => 'products_description_sport',
	'products_sets'                                         => 'products_sets',
	'products_properties'                                   => 'products_properties',
	'products_to_categories'                                => 'products_to_categories_sport',
	
	'top_categories'                                        => 'top_categories_sport',
	'top_categories_description'                            => 'top_categories_description_sport',
	
	'products_reviews'                                      => 'products_reviews',
	
	'products_questions'                                    => 'products_questions',
];

$product_status_columns =
"products_status,
products_status_sk,
products_status_hu,
products_status_ro,";

$productIActive = function( array $old_item ) : bool {
	return (bool)$old_item['products_status'];
};

$productShopDataIsActive = function( array $old_item, $lang_id ) {
	return match ($lang_id) {
		CZ_LANG_ID => (bool)$old_item['products_status'],
		SK_LANG_ID => (bool)$old_item['products_status_sk'],
		default => false,
	};
};

$category_cactive_column = 'visiblecat';

$old_db_config = Factory_Db::getBackendConfigInstance([
	'driver' => 'mysql',
	'host' => 'localhost',
	'port' => 3306,
	'dbname' => 'sport-cz',
	'charset' => 'utf8',
	'username' => 'root',
	'password' => 'nemeckadoga',
	'name' => 'old_shop',
]);



foreach($languages as $lang_id) {
	$shops[$lang_id] = Shops::get( $lng_to_shop_map[$lang_id] );
}

$shops_by_id = [];
foreach($old_shops as $shop_id) {
	$shops_by_id[$shop_id] = Shops::get( $old_shop_id_to_shop_map[$shop_id] );
}



$old_db = Factory_Db::getBackendInstance( $old_db_config );

$new_db = Db::get();

function getKindOfProductId( $old_category_id ) {
	global $new_db, $old_db, $shops, $languages, $tabs;
	
	$old_item = $old_db->fetchRow("SELECT
			categories_id as id,
			parent_id,
			sort_order,
			category_view as is_active,
			symlink_target_filter,
			symlink_target_category_id,
			is_symlink,
			symlink_strategy,
			properties_strategy,
			properties_inherited_category_id
		FROM
			{$tabs['categories']}
		WHERE
			categories_id=$old_category_id
		");
	
	if(!$old_item) {
		return 0;
	}
	
	if($old_item['is_symlink']) {
		if($old_item['symlink_strategy']=='is_virtual_category') {
			return $old_item['symlink_target_category_id'];
		} else {
			return 0;
		}
	} else {
		$target = null;
		switch($old_item['properties_strategy']) {
			case 'defines':
			case 'inherited_from_other_category':
				return (int)$old_item['id'];
				break;
			case 'takes_over_from_other_category':
				
				$target = $old_db->fetchRow("SELECT
						categories_id as id,
						parent_id,
						sort_order,
						category_view as is_active,
						symlink_target_filter,
						symlink_target_category_id,
						is_symlink,
						symlink_strategy,
						properties_strategy,
						properties_inherited_category_id
					FROM
						{$tabs['categories']}
					WHERE
						categories_id={$old_item['properties_inherited_category_id']}");
				break;
			case 'takes_over_from_parent':
				$target = $old_db->fetchRow("SELECT
						categories_id as id,
						parent_id,
						sort_order,
						category_view as is_active,
						symlink_target_filter,
						symlink_target_category_id,
						is_symlink,
						symlink_strategy,
						properties_strategy,
						properties_inherited_category_id
					FROM
						{$tabs['categories']}
					WHERE
						categories_id={$old_item['parent_id']}");
				break;
		}
		
		if($target) {
			return getKindOfProductId( $target['id'] );
		}
		
	}
	
	return 0;
}

$properties_map = [];
$options_map = [];
$property_groups_map = [];


$kindOfProducts = function() use ($new_db, $old_db, $shops, $languages, $tabs ) {
	$new_db->execute("TRUNCATE `kind_of_product`;");
	$new_db->execute("TRUNCATE `kind_of_product_shop_data`;");
	
	
	$main_category_ids =
		$old_db->fetchCol("SELECT
			categories_id
		FROM
		    {$tabs['categories']}
		WHERE
			categories_id IN (SELECT DISTINCT `categories_id` FROM `{$tabs['categories_properties_properties']}` WHERE 1)");
	
	$_main_category_descriptions = $old_db->fetchAll(
		"SELECT
			categories_id as id,
			categories_name as name,
			language_id
		FROM
		    {$tabs['categories_description']}
		WHERE
		    categories_id IN (".implode(', ', $main_category_ids).")"
	);
	
	$main_category_descriptions = [];
	foreach($_main_category_descriptions as $mcd) {
		$category_id = (int)$mcd['id'];
		$language_id = (int)$mcd['language_id'];
		
		$main_category_descriptions[$category_id][$language_id]['name'] = $mcd['name'];
		
	}
	
	
	
	
	foreach( $main_category_ids as $id ) {
		$kind_of_product = new KindOfProduct();
		
		$kind_of_product->setId( $id );
		$kind_of_product->setInternalName( $main_category_descriptions[$id][CZ_LANG_ID]['name'] );
		
		foreach($languages as $lang_id) {
			$kind_of_product->getShopData( $shops[$lang_id] )->setName( $main_category_descriptions[$id][$lang_id]['name'] );
		}
		
		$kind_of_product->save();
		
		$kind_of_product->activate();
		foreach($languages as $lang_id) {
			$kind_of_product->getShopData( $shops[$lang_id] )->_activate();
		}
		
	}
};

$propertyGroups = function() use ($new_db, $old_db, $shops, $languages, &$property_groups_map, $tabs) {
	$new_db->execute("TRUNCATE property_groups;");
	$new_db->execute("TRUNCATE property_groups_shop_data;");
	$new_db->execute('TRUNCATE kind_of_product_property_group');
	
	$kf_ids = KindOfProduct::dataFetchCol(select:['id']);
	
	
	$old_items = $old_db->fetchAll("SELECT group_id, priority, categories_id FROM {$tabs['categories_properties_groups']} WHERE categories_id in (".implode(', ', $kf_ids).")");
	
	$_descriptions = $old_db->fetchAll(
		"SELECT
			group_id as id,
			label as name,
			language_id
		FROM
			{$tabs['categories_properties_groups_description']}");
	
	$descriptions = [];
	foreach($_descriptions as $d) {
		$id = (int)$d['id'];
		$language_id = (int)$d['language_id'];
		
		$descriptions[$id][$language_id]['name'] = $d['name'];
		
	}
	
	
	foreach($old_items as $old_item) {
		$id = (int)$old_item['group_id'];
		$priority = (int)$old_item['priority'];
		$kind_of_product_id = getKindOfProductId((int)$old_item['categories_id']);
		
		$search_internal_name = $descriptions[$id][CZ_LANG_ID]['name'];
		
		
		$exists_id = PropertyGroup::dataFetchOne(['id'], [
			'internal_name'=>$search_internal_name
		]);
		if($exists_id) {
			$property_groups_map[$id] = $exists_id;
			$item = PropertyGroup::load($exists_id);
		} else {
			$item = new PropertyGroup();
			
			$item->setId( $id );
			$item->setInternalName( $search_internal_name );
			
			foreach($languages as $lang_id) {
				if(isset($descriptions[$id][$lang_id])) {
					$item->getShopData( $shops[$lang_id] )->setLabel( $descriptions[$id][$lang_id]['name'] );
				}
			}
			
			$item->save();
			
			$item->activate();
			foreach($languages as $lang_id) {
				$item->getShopData( $shops[$lang_id] )->_activate();
			}
		}
		
		if(!KindOfProduct_PropertyGroup::dataFetchCol(['kind_of_product_id'],
			[
				'kind_of_product_id'=>$kind_of_product_id,
				'AND',
				'group_id' => $item->getId()
			]
		)) {
			$assoc = new KindOfProduct_PropertyGroup();
			$assoc->setKindOfProductId( $kind_of_product_id );
			$assoc->setGroupId( $item->getId() );
			$assoc->setPriority( $priority );
			$assoc->save();
		}
		
		
		
		
	}
};


$properties = function() use ($new_db, $old_db, $shops, $languages, &$properties_map, &$property_groups_map, $tabs ) {
	$new_db->execute("TRUNCATE properties;");
	$new_db->execute("TRUNCATE properties_shop_data;");
	$new_db->execute('TRUNCATE kind_of_product_property');
	
	$kf_ids = KindOfProduct::dataFetchCol(select:['id']);
	
	$old_items = $old_db->fetchAll("SELECT
			property_id,
			priority,
			categories_id,
			group_id,
			type,
			is_active,
			allow_display,
			allow_filter
		FROM
			{$tabs['categories_properties_properties']}
		WHERE
			categories_id in (".implode(', ', $kf_ids).")");
	
	$_descriptions = $old_db->fetchAll(
		"SELECT
			property_id as id,
			label as name,
			language_id,
			bool_yes_description,
			url_param,
			units,
			description
		FROM
			{$tabs['categories_properties_properties_description']}");
	
	$descriptions = [];
	foreach($_descriptions as $d) {
		$id = (int)$d['id'];
		$language_id = (int)$d['language_id'];
		
		$descriptions[$id][$language_id] = $d;
	}
	
	
	
	foreach($old_items as $old_item) {
		$id = (int)$old_item['property_id'];
		$search_internal_name = $descriptions[$id][CZ_LANG_ID]['name'];
		
		switch($old_item['type']) {
			case 'multiSelect':
			case 'select':
				$type = Property::PROPERTY_TYPE_OPTIONS;
				break;
			case 'int':
			case 'float':
				$type = Property::PROPERTY_TYPE_NUMBER;
				break;
			case 'bool':
				$type = Property::PROPERTY_TYPE_BOOL;
				break;
			case 'string':
				$type = Property::PROPERTY_TYPE_TEXT;
				break;
			default: die($old_item['type'].' ???');
		}
		
		$priority = (int)$old_item['priority'];
		$kind_of_product_id = getKindOfProductId( (int)$old_item['categories_id'] );
		$group_id = (int)$old_item['group_id'];
		
		if(isset($property_groups_map[$group_id])) {
			$group_id = $property_groups_map[$group_id];
		}
		
		$is_active = (bool)$old_item['is_active'];
		$allow_display = (bool)$old_item['allow_display'];
		$allow_filter = (bool)$old_item['allow_filter'];
		
		
		$exists_id = Property::dataFetchOne(['id'], [
			'internal_name'=>$search_internal_name,
			'AND',
			'type' => $type
		]);
		if($exists_id) {
			$item = Property::load($exists_id);
			
			$properties_map[ $id ] = $exists_id;
		} else {
			$item = new Property();
			$item->setType( $type );
			if($type==Property::PROPERTY_TYPE_NUMBER) {
				$item->setDecimalPlaces(2);
			}
			
			
			$internal_name = $search_internal_name;
			
			$item->setId( $id );
			
			$item->setInternalName( $internal_name );
			$item->setIsFilter( $allow_filter );
			$item->setIsDefaultFilter( $allow_filter );
			
			foreach($languages as $lang_id) {
				
				$sd = $item->getShopData( $shops[$lang_id] );
				
				if(isset($descriptions[$id][$lang_id])) {
					$old_d = $descriptions[$id][$lang_id];
					$sd->setLabel( $old_d['name'] );
					$sd->setBoolYesDescription( $old_d['bool_yes_description'] );
					$sd->setUnits( $old_d['units'] );
				}
			}
			
			
			$item->save();
			
			if($old_item['is_active']) {
				$item->activate();
				foreach($languages as $lang_id) {
					$item->getShopData( $shops[$lang_id] )->_activate();
				}
			}
			
		}
		
		
		
		if(!KindOfProduct_Property::dataFetchCol(['kind_of_product_id'],
			[
				'kind_of_product_id'=>$kind_of_product_id,
				'AND',
				'property_id' => $item->getId()
			]
		)) {
			$assoc = new KindOfProduct_Property();
			$assoc->setKindOfProductId( $kind_of_product_id );
			$assoc->setPropertyType( $item->getType() );
			$assoc->setCanBeVariantSelector( $item->canBeVariantSelector() );
			$assoc->setCanBeFilter( $item->canBeFilter() );
			$assoc->setPropertyId( $item->getId() );
			$assoc->setPriority( $priority );
			$assoc->setGroupId( $group_id );
			$assoc->setShowOnProductDetail( $allow_display );
			$assoc->save();
		}

	}
	
	
};

$propertyOptions = function() use ($new_db, $old_db, $shops, $languages, &$properties_map, &$options_map, $tabs ) {
	$new_db->execute("TRUNCATE properties_options;");
	$new_db->execute("TRUNCATE properties_options_shop_data;");
	
	$p_ids = Property::dataFetchCol(select:['id']);
	
	$old_items = $old_db->fetchAll("SELECT
			option_id,
			property_id,
			priority
		FROM
			{$tabs['categories_properties_properties_options']}
		WHERE
			property_id in (".implode(', ', $p_ids).")");
	
	$_descriptions = $old_db->fetchAll(
		"SELECT
			property_id,
			option_id as id,
			label as name,
			language_id,
			url_param
		FROM
			{$tabs['categories_properties_properties_options_description']}");
	
	$descriptions = [];
	foreach($_descriptions as $d) {
		$id = (int)$d['id'];
		$language_id = (int)$d['language_id'];
		
		$descriptions[$id][$language_id] = $d;
	}
	
	foreach($old_items as $old_item) {
		$id = (int)$old_item['option_id'];
		$priority = (int)$old_item['priority'];
		$property_id = (int)$old_item['property_id'];
		if(isset($properties_map[$property_id])) {
			$property_id = $properties_map[$property_id];
		}
		
		$internal_name = $descriptions[$id][CZ_LANG_ID]['name'];
		
		$exists_id = Property_Options_Option::dataFetchOne(['id'], [
			'property_id' => $property_id,
			'AND',
			'internal_name' => $internal_name
		]);
		
		if($exists_id) {
			$options_map[$id] = $exists_id;
		}
		
		$item = new Property_Options_Option();
		
		$item->setId( $id );
		$item->setInternalName( $internal_name );
		$item->setPriority( $priority );
		$item->setPropertyId( $property_id );
		
		foreach($languages as $lang_id) {
			if(isset($descriptions[$id][$lang_id])) {
				$item->getShopData( $shops[$lang_id] )->setFilterLabel( $descriptions[$id][$lang_id]['name'] );
				$item->getShopData( $shops[$lang_id] )->setProductDetailLabel( $descriptions[$id][$lang_id]['name'] );
			}
		}
		
		$item->save();
		
		$item->activate();
		foreach($languages as $lang_id) {
			$item->getShopData( $shops[$lang_id] )->_activate();
		}
	}
	
};


$categories = function() use ($new_db, $old_db, $shops, $languages, $tabs, $category_cactive_column ) {

	$new_db->execute("TRUNCATE categories;");
	$new_db->execute("TRUNCATE categories_shop_data;");

	
	$old_items = $old_db->fetchAll("SELECT
			categories_id as id,
			parent_id,
			sort_order,
			$category_cactive_column as is_active,
			symlink_target_filter,
			symlink_target_category_id,
			is_symlink,
			symlink_strategy,
			properties_strategy,
			properties_inherited_category_id
		FROM
			{$tabs['categories']}");
	
	$_descriptions = $old_db->fetchAll(
		"SELECT
			categories_id as id,
			language_id,
			categories_name,
			categories_description
		FROM
			{$tabs['categories_description']}");
	
	
	$descriptions = [];
	foreach($_descriptions as $d) {
		$id = (int)$d['id'];
		$language_id = (int)$d['language_id'];
		
		$descriptions[$id][$language_id] = $d;
	}
	
	$activate_items = [];
	
	
	foreach($old_items as $old_item) {
		$id = (int)$old_item['id'];
		$parent_id = (int)$old_item['parent_id'];
		$priority = (int)$old_item['sort_order'];
		$is_active = (bool)$old_item['is_active'];
		
		$item = new class extends Category {
			public function setParentId( int $parent_id, bool $update_priority = true, bool $save = true ): void
			{
				$this->parent_id = $parent_id;
			}
			
			public function afterAdd(): void
			{
			
			}
		};

		$item->setId( $id );
		$item->setInternalName( $descriptions[$id][CZ_LANG_ID]['categories_name'] );
		$item->setPriority( $priority, false );
		$item->setParentId( $parent_id );
		$item->setKindOfProductId( getKindOfProductId( $id ) );
		
		
		foreach($languages as $lang_id) {
			$sd = $item->getShopData( $shops[$lang_id] );
			if(isset($descriptions[$id][$lang_id])) {
				$sd->setName( $descriptions[$id][$lang_id]['categories_name'] );
				$sd->setDescription( $descriptions[$id][$lang_id]['categories_description'] );
			}
		}
		
		
		$item->save();

		foreach($languages as $lang_id) {
			$sd = $item->getShopData( $shops[$lang_id] );
			$sd->generateURLPathPart();
		}
		
		if($is_active) {
			$activate_items[] = $item;
		}
	}
	
	
	foreach($activate_items as $item) {
		$item->activate();
		foreach($languages as $lang_id) {
			$item->getShopData( $shops[$lang_id] )->_activate();
		}
	}
	
	Category::actualizeTreeData();
};

$brands = function() use ($new_db, $old_db, $shops, $languages, $tabs) {
	$new_db->execute("TRUNCATE brands;");
	$new_db->execute("TRUNCATE brands_shop_data;");
	
	$old_items = $old_db->fetchAll("SELECT
			manufacturers_id as id,
			manufacturers_name as name
		FROM
			{$tabs['manufacturers']}
		");
	
	foreach($old_items as $old_tem) {
		$item = new Brand();
		
		$item->setId( $old_tem['id'] );
		$item->setInternalName( $old_tem['name'] );
		
		foreach($languages as $lang_id) {
			$item->getShopData( $shops[$lang_id] )->setName( $old_tem['name'] );
		}
		
		$item->save();
		
		$item->activate();
		foreach($languages as $lang_id) {
			$item->getShopData( $shops[$lang_id] )->_activate();
		}
		
		
	}
	
};


$products = function() use ($new_db, $old_db, $shops, $languages, $vat_rates, $tabs, $product_status_columns, $productIActive, $productShopDataIsActive ) {
	$new_db->execute("TRUNCATE products;");
	$new_db->execute("TRUNCATE products_shop_data;");
	$new_db->execute("TRUNCATE products_set_items;");
	
	
	$old_items = $old_db->fetchAll("SELECT
			products_id as id,
			main_category_id,
			products_quantity,
			products_model,
			products_price,
			products_price_sk,
			final_price,
			final_price_sk,
			products_date_available,
			products_date_available_sk,
			products_tax_class_id,
			products_tax_class_id_sk,
			manufacturers_id,
			
			$product_status_columns
			
			_HelCis,
			is_set,
			has_variants,
			is_variant,
			variant_master_product_id
		FROM
			{$tabs['products']}
		");
	
	$_descriptions = $old_db->fetchAll(
		"SELECT
			products_id as id,
			language_id,
			products_name,
			products_description,
			products_info
		FROM
			{$tabs['products_description']}");
	
	$descriptions = [];
	foreach($_descriptions as $d) {
		$id = (int)$d['id'];
		$language_id = (int)$d['language_id'];
		
		$descriptions[$id][$language_id] = $d;
	}
	
	$activate_items = [];


	foreach($old_items as $old_item) {
		$id = (int)$old_item['id'];
		
		if(!isset($descriptions[$id])) {
			continue;
		}
		
		$item = new class extends Product {
			public function actualizeVariantMaster(): void
			{
			}
			
			public function actualizeSet(): void
			{
			}
			
		};
		
		$item->setDeliveryClassId( Delivery_Class::getDefault()->getId() );
		$item->setId( $id );
		$item->setInternalName( $descriptions[$id][CZ_LANG_ID]['products_name']??'' );
		$item->setType( Product::PRODUCT_TYPE_REGULAR );
		$item->setKindId( getKindOfProductId( $old_item['main_category_id']??0 ) );
		$item->setInternalCode( $old_item['products_model']??'' );
		$item->setBrandId( $old_item['manufacturers_id']??0 );
		$item->setErpId( $old_item['_HelCis']??'' );
		
		if($old_item['is_set']) {
			$item->setType( Product::PRODUCT_TYPE_SET );
			
			$old_set_items = $old_db->fetchAll("SELECT
					related_product_id,
					sort_order,
					default_items_count
				FROM {$tabs['products_sets']} WHERE
					products_id=$id");
			
				foreach($old_set_items as $osi) {
					$set_item = new Product_SetItem();
					$set_item->setProductId( $id );
					$set_item->setItemProductId( $osi['related_product_id'] );
					$set_item->setCount( $osi['default_items_count'] );
					$set_item->setSortOrder( $osi['sort_order'] );
					$set_item->save();
				}
		}
		
		if($old_item['is_variant']) {
			$item->setType( Product::PRODUCT_TYPE_VARIANT );
			$master_id = $old_item['variant_master_product_id'];
			$item->setVariantMasterProductId( $master_id );
			
			$variant_descriptions = $descriptions[$id];
			$master_descriptions = $descriptions[$master_id]??[];
			
			$variant_names = [];
			foreach($variant_descriptions as $language_id=>$d) {
				if(!isset($master_descriptions[$language_id])) {
					continue;
				}
				
				$variant_names[$language_id] = trim(str_replace(
					$master_descriptions[$language_id]['products_name'],
					'',
					$d['products_name']
				));
				
				$variant_names[$language_id] = trim($variant_names[$language_id], ' -');
				
			}
			
			foreach($variant_names as $language_id=>$variant_name) {
				if(!isset($shops[$language_id])) {
					continue;
				}
				
				$sd = $item->getShopData( $shops[$language_id] );
				$sd->setVariantName(
					$variant_name
				);
				
				/*
				var_dump(
					'm:'.$master_descriptions[$language_id]['products_name'],
					'v:'.$variant_descriptions[$language_id]['products_name'],
					'r:'.$variant_name
				);
				echo PHP_EOL.PHP_EOL;
				*/
				
			}
			
		}
		
		if($old_item['has_variants']) {
			$item->setType( Product::PRODUCT_TYPE_VARIANT_MASTER );
		}
		
		foreach($languages as $lang_id) {
			$sd = $item->getShopData( $shops[$lang_id] );
			if(isset($descriptions[$id][$lang_id])) {
				$sd->setName( $descriptions[$id][$lang_id]['products_name'] );
				$sd->setDescription( $descriptions[$id][$lang_id]['products_description'] );
				$sd->setShortDescription( $descriptions[$id][$lang_id]['products_info']??'' );
			}
			
			$avl = Product_Availability::get( $sd->getShop()->getDefaultAvailability(), $sd->getId() );
			$avl->setNumberOfAvailable( $old_item['products_quantity'] );
			$avl->save();
			
		}
		
		$item->save();
		
		foreach($languages as $lang_id) {
			$sd = $item->getShopData( $shops[$lang_id] );
			
			
			switch($lang_id) {
				case SK_LANG_ID:
					$vat_rate = $vat_rates[$lang_id][$old_item['products_tax_class_id_sk']]??20;
					$final_price = $old_item['final_price_sk'];
					$round = 1;
					break;
				default:
					$vat_rate = $vat_rates[$lang_id][$old_item['products_tax_class_id']]??21;
					$final_price = $old_item['final_price'];
					$round = 0;
					break;
			}
			
			$vat_mtp = 1+($vat_rate/100);
			
			$final_price = round($final_price*$vat_mtp, $round);
			
			$pricelist = $sd->getShop()->getDefaultPricelist();
			
			$pp = Product_Price::get( $pricelist, $id );
			$pp->setVatRate( $vat_rate );
			$pp->setPrice( $final_price );
			$pp->save();
			
		}
		

		
		if($productIActive($old_item)) {
			Product::updateData(['is_active'=>true], ['id'=>$item->getId()]);
			Product_ShopData::updateData(['entity_is_active'=>true], ['entity_id'=>$item->getId()]);
		}
		
		foreach($languages as $lang_id) {
			$sd = $item->getShopData( $shops[$lang_id] );
			
			$where = $sd->getShop()->getWhere();
			$where[] = 'AND';
			$where['entity_id'] = $item->getId();
			
			if($productShopDataIsActive($old_item, $lang_id)) {
				Product_ShopData::updateData(['is_active_for_shop'=>true], $where);
			}
		}
		
		
	}
};

$productProperties = function() use ($new_db, $old_db, $shops, $languages, $vat_rates, &$properties_map, &$options_map, $tabs ) {
	$new_db->execute("TRUNCATE TABLE products_parameters");
	$new_db->execute("TRUNCATE TABLE products_text_parameters");
	
	$types = Property::dataFetchPairs([
		'id',
		'type'
	]);
	
	$property_ids = implode(', ', Property::dataFetchCol(select:['id']));
	
	$old_properties = $old_db->fetchAll("SELECT
		products_id,
		property_id,
		value
	FROM
		{$tabs['products_properties']}
	WHERE
		property_id IN ($property_ids) AND
		information_is_not_available=0 AND
		value<>'' AND
		value<>'a:0:{}'
	");
	
	foreach( $old_properties as $item ) {
		//var_dump( $item );
		
		$product_id = (int)$item['products_id'];
		$property_id = (int)$item['property_id'];
		if(isset($properties_map[$property_id])) {
			$property_id = $properties_map[$property_id];
		}
		
		$item['value'] = trim($item['value']);
		
		if( str_starts_with($item['value'], '[') ) {
			$texts = explode("\n", $item['value']);
			
			foreach($texts as $text) {
				$text = trim($text, '[]');
				
				$lang_id = CZ_LANG_ID;
				if(
					str_starts_with($text,'CZ:') ||
					str_starts_with($text,'cz:')
				) {
					$text = substr($text, 3);
				}
				
				if(
					str_starts_with($text,'SK:') ||
					str_starts_with($text,'sk:')
				) {
					$text = substr($text, 3);
					$lang_id = SK_LANG_ID;
				}

				$item = new Product_Parameter_TextValue();
				$item->setShop( $shops[$lang_id] );
				$item->setProductId( $product_id );
				$item->setPropertyId( $property_id );
				$item->setText( $text );
				$item->save();
				
			}

			continue;
		}
		
		if( str_starts_with($item['value'], 'a:') ) {
			$values = unserialize( $item['value'] );
		} else {
			$values = [ $item['value'] ];
		}
		

		foreach($values as $value) {
			$item = new Product_Parameter_Value();
			$item->setProductId( $product_id );
			$item->setPropertyId( $property_id );
			
			if($types[$property_id]==Property::PROPERTY_TYPE_NUMBER){
				$value = $value*1000;
			}
			
			if($types[$property_id]==Property::PROPERTY_TYPE_OPTIONS){
				if(isset($options_map[$value])) {
					$value = $options_map[$value];
				}
			}

			$item->setValue( (int)$value );
			$item->save();
		}

	}
	
};

$actualizeSet = function() {
	$set_ids = Product::dataFetchCol(['id'], ['type'=>Product::PRODUCT_TYPE_SET]);
	foreach($set_ids as $id) {
		Product::load($id)->actualizeSet();
	}
};

$actualizeVariantMasters = function() {
	$set_ids = Product::dataFetchCol(['id'], ['type'=>Product::PRODUCT_TYPE_VARIANT_MASTER]);
	foreach($set_ids as $id) {
		Product::load($id)->actualizeVariantMaster();
	}
};

$categoriesAssoc = function() use ($new_db, $old_db, $shops, $languages, $vat_rates, $tabs) {

	$new_db->execute("TRUNCATE TABLE categories_products");
	
	$categories = Category::dataFetchCol(['id']);
	
	$variants = Product::dataFetchCol(['id'], ['type'=>Product::PRODUCT_TYPE_VARIANT]);

	$items = $old_db->fetchAll("SELECT
		products_id,
		categories_id
	FROM
		{$tabs['products_to_categories']}
	WHERE
		categories_id IN (".implode(', ', $categories).")");
	
	foreach($items as $c) {
		$product_id = (int)$c['products_id'];
		$category_id = (int)$c['categories_id'];
		if(in_array($product_id, $variants)) {
			continue;
		}
		
		$assoc = new Category_Product();
		$assoc->setCategoryId( $category_id );
		$assoc->setProductId( $product_id );
		$assoc->save();
	}

	$roots = array_unique(Category::dataFetchCol(['root_id']));
	
	foreach($roots as $root_id) {
		Category::actualizeBranchProductAssoc( $root_id );
	}
};

$VK = function() use ($new_db, $old_db, $shops, $languages, $vat_rates, &$properties_map, &$options_map, $tabs ) {
	$vks = $old_db->fetchAll("SELECT
		categories_id,
		symlink_target_filter,
		symlink_target_category_id
	FROM
		{$tabs['categories']}
	WHERE
		is_symlink=1 and
		symlink_strategy='is_virtual_category'");
	
	foreach($vks as $vk) {
		$c_id = $vk['categories_id'];
		
		$category = Category::load( $c_id );
		$category->setAutoAppendProducts( true );
		$category->save();
		
		$filter_settings = unserialize($vk['symlink_target_filter']);

		$filter = $category->getAutoAppendProductsFilter();
		
		
		$filter->getBasicFilter()->setKindOfProductId( $vk['symlink_target_category_id'] );
		
		if($filter_settings['properties']) {
			foreach($filter_settings['properties'] as $property_id=>$selected_options) {
				if(isset($properties_map[$property_id])) {
					$property_id = $properties_map[$property_id];
				}
				
				if(is_array($selected_options)) {
					if($selected_options) {
						foreach($selected_options as $i=>$option_id) {
							if(isset($options_map[$option_id])) {
								$selected_options[$i] = $options_map[$option_id];
							}
						}
						
						$selected_options = array_unique( $selected_options );
						
						$filter->getPropertyOptionsFilter()->setSelectedOptions( $property_id, $selected_options );
					}
				}
				
				if(is_bool($selected_options)) {
					$filter->getPropertyBoolFilter()->addPropertyRule( $property_id, $selected_options );
				}
			}
		}
		
		if($filter_settings['manufacturers']) {
			$filter->getBrandsFilter()->setSelectedBrands( $filter_settings['manufacturers'] );
		}
		
		$filter->save();
		
		
		
	}
	
	Category::actualizeAllAutoAppendCategories();
	
};

$fulltext = function() use ($new_db) {

	$new_db->execute("TRUNCATE `fulltext_internal`;");
	$new_db->execute("TRUNCATE `fulltext_internal_word`;");
	$new_db->execute("TRUNCATE `fulltext_shop`;");
	$new_db->execute("TRUNCATE `fulltext_shop_word`;");
	
	$updateIndex = function( string $class ) {
		/**
		 * @var Entity_WithShopData $class
		 */
		$et = $class::getEntityType();
		
		$page = 0;
		$limit = 1000;
		$i = 0;
		do {
			$i++;
			$offset = $page * $limit;
			$items = $class::fetchInstances();
			$items->getQuery()->setLimit( $limit, $offset );
			
			$end = true;
			foreach( $items as $c ) {
				
				$index = ($page*$limit)+$i;
				
				$i++;
				echo "$et: [{$index}] {$c->getId()}\n";
				$c->updateFulltextSearchIndex();
				$end = false;
			}
			
			if($end) {
				break;
			}
			$page++;
			
		} while(true);
		
		
	};
	
	
	$updateIndex( Category::class );
	$updateIndex( Brand::class );
	$updateIndex( KindOfProduct::class );
	$updateIndex( Property::class );
	$updateIndex( PropertyGroup::class );
	$updateIndex( Product::class );
	
};

$Signposts = function() use ($old_db, $new_db, $tabs, $shops, $languages) {
	$new_db->execute('TRUNCATE TABLE signposts');
	$new_db->execute('TRUNCATE TABLE signposts_categories');
	$new_db->execute('TRUNCATE TABLE signposts_shop_data');
	
	$old_items = $old_db->fetchAll("SELECT
		categories_id as id,
		sort_order,
		sub_categories,
		status
	FROM {$tabs['top_categories']}");
	
	$_descriptions = $old_db->fetchAll(
		"SELECT
			categories_id as id,
			categories_name as name,
			categories_description as description,
			language_id
		FROM
			{$tabs['top_categories_description']}");
	
	$descriptions = [];
	foreach($_descriptions as $d) {
		$id = (int)$d['id'];
		$language_id = (int)$d['language_id'];
		
		$descriptions[$id][$language_id] = $d;
	}
	
	foreach($old_items as $old_item) {
		$id = (int)$old_item['id'];
		$priority = (int)$old_item['sort_order'];
		$is_active = (bool)$old_item['status'];
		
		$item = new Signpost();
		
		$item->setId( $id );
		$item->setInternalName( $descriptions[$id][CZ_LANG_ID]['name'] );
		$item->setPriority( $priority );
		
		
		foreach($languages as $lang_id) {
			$sd = $item->getShopData( $shops[$lang_id] );
			if(isset($descriptions[$id][$lang_id])) {
				$sd->setName( $descriptions[$id][$lang_id]['name']??'' );
				$sd->setDescription( $descriptions[$id][$lang_id]['description']??'' );
			}
		}
		
		
		$item->save();
		
		$sc = explode(',', $old_item['sub_categories']);
		foreach($sc as $c_id) {
			if($c_id) {
				$item->addCategory( $c_id );
			}
		}
		
		foreach($languages as $lang_id) {
			$sd = $item->getShopData( $shops[$lang_id] );
			$sd->generateURLPathPart();
		}
		
		if($is_active) {
			$item->activateCompletely();
		}
		
	}
	
};

$Reviews = function() use ($old_db, $new_db, $tabs, $shops, $shops_by_id, $languages) {
	$new_db->execute("TRUNCATE TABLE product_reviews");
	
	$items = $old_db->fetchAll("SELECT
					id,
					eshop_id,
					product_id,
					author_name,
					author_email,
					rank,
					positive,
					negative,
					summary,
					added_date_time,
					assessed,
					assessed_date_time,
					approved,
					approved_date_time,
					images,
					comments,
					heureka_review_id,
					customer_id,
					customer_bonus_added,
					customer_bonus_added_date_time
				FROM {$tabs['products_reviews']}");
	
	foreach($items as $item) {
		$review = new class($item) extends ProductReview {
			public function __construct( array $item )
			{
				global $shops_by_id;
				
				$this->id = $item['id'];
				$this->created = Data_DateTime::catchDateTime( $item['added_date_time'] );
				$this->setShop( $shops_by_id[$item['eshop_id']] );
				$this->setProductId( (int)$item['product_id'] );
				$this->author_name = $item['author_name'];
				$this->author_email = $item['author_email'];
				$this->rank = $item['rank'];
				$this->positive_characteristics = $item['positive'];
				$this->negative_characteristics = $item['negative'];
				$this->summary = $item['summary'];
				$this->assessed = (bool)$item['assessed'];
				$this->assessed_date_time = Data_DateTime::catchDateTime( $item['assessed_date_time'] );
				$this->approved = (bool)$item['approved'];
				$this->approved_date_time = Data_DateTime::catchDateTime( $item['approved_date_time'] );
				$this->our_comments = $item['comments'];
				$this->customer_id = (int)$item['customer_id'];
				
				if($item['heureka_review_id']) {
					$this->source = 'heureka';
					$this->source_id = $item['heureka_review_id'];
				}

			}
		};
		
		$review->save();
		$review->actualizeProduct();
	}
	
};

$Questions = function() use ($old_db, $new_db, $tabs, $shops, $shops_by_id, $languages) {
	$new_db->execute("TRUNCATE TABLE product_questions");
	
	$items = $old_db->fetchAll("SELECT
					id,
					eshop_id,
					state,
					product_id,
					author_name,
					author_email,
					question,
					answer,
					added_date_time,
					assessed,
					assessed_date_time,
					approved,
					approved_date_time
				FROM {$tabs['products_questions']}");
	
	foreach($items as $item) {
		$review = new class($item) extends ProductQuestion {
			public function __construct( array $item )
			{
				global $shops_by_id;
				
				$this->id = $item['id'];
				$this->created = Data_DateTime::catchDateTime( $item['added_date_time'] );
				$this->setShop( $shops_by_id[$item['eshop_id']] );
				$this->setProductId( (int)$item['product_id'] );
				$this->author_name = $item['author_name'];
				$this->author_email = $item['author_email'];
				
				$this->question = $item['question'];
				$this->answer = $item['answer'];
				
				$this->answered = (bool)$item['assessed'];
				$this->answered_date_time = Data_DateTime::catchDateTime( $item['assessed_date_time'] );
				
				$this->display = (bool)$item['approved'];
				
				//$this->customer_id = (int)$item['customer_id'];
				
			}
		};
		
		$review->save();
		$review->actualizeProduct();
	}
	
};


Shops::setCurrent( Shops::getDefault() );

/*
$propertyGroups();
$properties();
$propertyOptions();

$kindOfProducts();
$categories();
$brands();
$products();


$productProperties();

$actualizeSet();
$actualizeVariantMasters();

$categoriesAssoc();

$VK();

$fulltext();

$Signposts();

$Reviews();

$Questions();
*/


/*
$prices = Product_ShopData::dataFetchAll([
	'shop_code',
	'entity_id',
	'vat_rate',
	'price',
]);

foreach($prices as $p) {
	$pricelist_code = $p['shop_code']=='sk' ? 'default_eur' : 'default_czk';
	
	$pricelist = Pricelists::get( $pricelist_code );
	
	$price = Product_Price::get( $pricelist , $p['entity_id'] );
	
	$price->setVatRate( $p['vat_rate'] );
	$price->setPrice( $p['price'] );
	$price->save();
	
	var_dump( $p['shop_code'].':'.$p['entity_id'] );
}

*/

/*
$prices = Product_ShopData::dataFetchAll([
	'shop_code',
	'entity_id',
	'length_of_delivery',
	'available_from',
	'in_stock_qty',
]);

foreach($prices as $p) {
	if($p['shop_code']=='sk') {
		continue;
	}
	
	$availability_code = 'default';
	
	$availability = Availabilities::get( $availability_code );
	
	$avl = Product_Availability::get( $availability , $p['entity_id'] );
	
	$avl->setLengthOfDelivery( $p['length_of_delivery'] );
	$avl->setInStockQty( $p['in_stock_qty'] );
	$avl->setAvailableFrom( $p['available_from'] );
	$avl->save();
	
	var_dump( $p['entity_id'] );
}
*/