<?php /** @noinspection SpellCheckingInspection */

/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Closure;
use Jet\Data_DateTime;
use Jet\Db;
use Jet\Db_Backend_Interface;
use Jet\Factory_Db;
use Jet\Locale;

require __DIR__.'/../application/bootstrap_cli_service.php';


/*
const CZ_LANG_ID = 4;
const SK_LANG_ID = 5;

$languages = [
	CZ_LANG_ID,
	SK_LANG_ID
];

$lng_to_eshop_map = [
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
	'name' => 'old_eshop',
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


class Migration {
	public const CZ_ESHOP_ID = 1;
	public const SK_ESHOP_ID = 2;
	public const HU_ESHOP_ID = 3;
	public const RO_ESHOP_ID = 4;
	public const B2B_ESHOP_ID = 99;
	
	
	public const CZ_LANG_ID = 5;
	public const EN_LANG_ID = 11;
	public const DE_LANG_ID = 12;
	public const SK_LANG_ID = 20;
	public const HU_LANG_ID = 40;
	public const RO_LANG_ID = 41;
	
	public const B2B_GROUP_CZ = 2;
	public const B2B_GROUP_EU = 3;
	public const B2B_GROUP_SK = 4;
	
	protected int $default_language;
	
	protected array $languages_to_locales = [];
	/**
	 * @var EShop[]
	 */
	protected array $eshop_map = [];
	protected array $eshops_by_id = [];
	protected array $db_tables = [];
	
	protected Db_Backend_Interface $old_db;
	protected Db_Backend_Interface $new_db;

	protected array $properties_map = [];
	protected array $options_map = [];
	protected array $property_groups_map = [];

	protected string $category_cactive_column = 'visiblecat';
	
	
	public function __construct()
	{
		$this->default_language = static::CZ_LANG_ID;
		
		$this->languages_to_locales = [
			static::CZ_LANG_ID  => new Locale('cs_CZ'),
			static::EN_LANG_ID  => new Locale('en_GB'),
			static::DE_LANG_ID  => new Locale('de_DE'),
			static::SK_LANG_ID  => new Locale('sk_SK'),
			static::HU_LANG_ID  => new Locale('hu_HU'),
			static::RO_LANG_ID  => new Locale('ro_RO'),
		];
		
		$this->eshop_map = [
			static::CZ_ESHOP_ID.':'.static::CZ_LANG_ID => EShops::get(EShop::generateKey('b2c', new Locale('cs_CZ'))),
			static::SK_ESHOP_ID.':'.static::SK_LANG_ID => EShops::get(EShop::generateKey('b2c', new Locale('sk_SK'))),
			//static::HU_ESHOP_ID.':'.static::HU_LANG_ID => EShops::get(EShop::generateKey('b2c', new Locale('hu_HU'))),
			//static::RO_ESHOP_ID.':'.static::RO_LANG_ID => EShops::get(EShop::generateKey('b2c', new Locale('ro_RO'))),
			
			static::B2B_ESHOP_ID.':'.static::CZ_LANG_ID => EShops::get(EShop::generateKey('b2b', new Locale('cs_CZ'))),
			static::B2B_ESHOP_ID.':'.static::SK_LANG_ID => EShops::get(EShop::generateKey('b2b', new Locale('sk_SK'))),
			static::B2B_ESHOP_ID.':'.static::EN_LANG_ID => EShops::get(EShop::generateKey('b2b', new Locale('en_GB'))),
		];
		
		$this->eshops_by_id = [
			static::CZ_ESHOP_ID => $this->eshop_map[static::CZ_ESHOP_ID.':'.static::CZ_LANG_ID],
			static::SK_ESHOP_ID => $this->eshop_map[static::SK_ESHOP_ID.':'.static::SK_LANG_ID],
			static::B2B_ESHOP_ID => $this->eshop_map[static::B2B_ESHOP_ID.':'.static::SK_LANG_ID],
		];
		
		$this->db_tables = [
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
		
		$old_db_config = Factory_Db::getBackendConfigInstance([
			'name' => 'old_eshop',
			'driver' => 'mysql',
			'host' => 'localhost',
			'port' => 3306,
			'dbname' => 'sport-cz',
			'charset' => 'utf8',
			'username' => 'root',
			'password' => 'nemeckadoga',
		]);
		$this->old_db = Factory_Db::getBackendInstance( $old_db_config );
		
		$this->new_db = Db::get();
		
	}
	
	protected function getKindOfProductId( int $old_category_id ) : int
	{
		
		$old_item = $this->old_db->fetchRow("SELECT
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
			{$this->db_tables['categories']}
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
					
					$target = $this->old_db->fetchRow("SELECT
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
						{$this->db_tables['categories']}
					WHERE
						categories_id={$old_item['properties_inherited_category_id']}");
					break;
				case 'takes_over_from_parent':
					$target = $this->old_db->fetchRow("SELECT
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
						{$this->db_tables['categories']}
					WHERE
						categories_id={$old_item['parent_id']}");
					break;
			}
			
			if($target) {
				return $this->getKindOfProductId( $target['id'] );
			}
			
		}
		
		return 0;
	}
	
	public function foreEchShop( Closure $do ) : void
	{
		foreach($this->eshop_map as $old_key=>$eshop) {
			[$old_eshop_id, $lang_id] = explode(':', $old_key);
			
			$do->call( $this, $eshop, $lang_id, $old_eshop_id );
		}
	}
	
	protected string $counter_title = '';
	protected int $counter_count = 0;
	protected int $counter_c = 0;
	
	protected function initCounter( string $title, int $count ) : void
	{
		$this->counter_title = $title;
		$this->counter_count = $count;
		$this->counter_c = 0;
		
	}
	
	protected function counter( string $id='' ) : void
	{
		$this->counter_c++;
		
		if($id) {
			$id = ' '.$id;
		}
		
		echo "[{$this->counter_c}/{$this->counter_count}] {$this->counter_title}{$id}\n";
	}
	
	public function doKindOfProducts() : void
	{
		$this->new_db->execute("TRUNCATE `kind_of_product`;");
		$this->new_db->execute("TRUNCATE `kind_of_product_eshop_data`;");
		
		
		$main_category_ids =
			$this->old_db->fetchCol("SELECT
			categories_id
		FROM
		    {$this->db_tables['categories']}
		WHERE
			categories_id IN (SELECT DISTINCT `categories_id` FROM `{$this->db_tables['categories_properties_properties']}` WHERE 1)");
		
		$_main_category_descriptions = $this->old_db->fetchAll(
			"SELECT
			categories_id as id,
			categories_name as name,
			language_id
		FROM
		    {$this->db_tables['categories_description']}
		WHERE
		    categories_id IN (".implode(', ', $main_category_ids).")"
		);
		
		$main_category_descriptions = [];
		foreach($_main_category_descriptions as $mcd) {
			$category_id = (int)$mcd['id'];
			$language_id = (int)$mcd['language_id'];
			
			$main_category_descriptions[$category_id][$language_id]['name'] = $mcd['name'];
			
		}
		
		$this->initCounter( 'Kind od product', count( $main_category_ids ) );
		
		foreach( $main_category_ids as $id ) {
			$this->counter();
			
			$kind_of_product = new KindOfProduct();
			
			$kind_of_product->setId( $id );
			$kind_of_product->setInternalName( $main_category_descriptions[$id][$this->default_language]['name'] );
			
			$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id ) use
				($id, $kind_of_product, $main_category_descriptions) : void {
				$kind_of_product->getEshopData( $eshop )->setName( $main_category_descriptions[$id][$lang_id]['name'] );
			} );

			$kind_of_product->save();
			$kind_of_product->activateCompletely();
			
		}
		
	}
	
	public function doPropertyGroups() : void
	{
		$this->new_db->execute("TRUNCATE property_groups;");
		$this->new_db->execute("TRUNCATE property_groups_eshop_data;");
		$this->new_db->execute('TRUNCATE kind_of_product_property_group');
		
		$kf_ids = KindOfProduct::dataFetchCol(select:['id']);
		
		
		$old_items = $this->old_db->fetchAll("SELECT group_id, priority, categories_id FROM {$this->db_tables['categories_properties_groups']} WHERE categories_id in (".implode(', ', $kf_ids).")");
		
		$_descriptions = $this->old_db->fetchAll(
			"SELECT
			group_id as id,
			label as name,
			language_id
		FROM
			{$this->db_tables['categories_properties_groups_description']}");
		
		$descriptions = [];
		foreach($_descriptions as $d) {
			$id = (int)$d['id'];
			$language_id = (int)$d['language_id'];
			
			$descriptions[$id][$language_id]['name'] = $d['name'];
			
		}
		
		
		$this->initCounter( 'Property group', count( $old_items ) );
		foreach($old_items as $old_item) {
			$this->counter();
			
			$id = (int)$old_item['group_id'];
			$priority = (int)$old_item['priority'];
			$kind_of_product_id = $this->getKindOfProductId((int)$old_item['categories_id']);
			
			$search_internal_name = $descriptions[$id][$this->default_language]['name'];
			
			
			$exists_id = PropertyGroup::dataFetchOne(['id'], [
				'internal_name'=>$search_internal_name
			]);
			if($exists_id) {
				$this->property_groups_map[$id] = $exists_id;
				$item = PropertyGroup::load($exists_id);
			} else {
				$item = new PropertyGroup();
				
				$item->setId( $id );
				$item->setInternalName( $search_internal_name );
				
				$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id ) use ($descriptions, $id, $item) {
					if(isset($descriptions[$id][$lang_id])) {
						$item->getEshopData( $eshop )->setLabel( $descriptions[$id][$lang_id]['name'] );
					}
				});
				
				$item->save();
				
				$item->activateCompletely();

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
		
	}
	
	public function doProperties() : void
	{
		$this->new_db->execute("TRUNCATE properties;");
		$this->new_db->execute("TRUNCATE properties_eshop_data;");
		$this->new_db->execute('TRUNCATE kind_of_product_property');
		
		$kf_ids = KindOfProduct::dataFetchCol(select:['id']);
		
		$old_items = $this->old_db->fetchAll("SELECT
			property_id,
			priority,
			categories_id,
			group_id,
			type,
			is_active,
			allow_display,
			allow_filter
		FROM
			{$this->db_tables['categories_properties_properties']}
		WHERE
			categories_id in (".implode(', ', $kf_ids).")");
		
		$_descriptions = $this->old_db->fetchAll(
			"SELECT
			property_id as id,
			label as name,
			language_id,
			bool_yes_description,
			url_param,
			units,
			description
		FROM
			{$this->db_tables['categories_properties_properties_description']}");
		
		$descriptions = [];
		foreach($_descriptions as $d) {
			$id = (int)$d['id'];
			$language_id = (int)$d['language_id'];
			
			$descriptions[$id][$language_id] = $d;
		}
		
		
		$this->initCounter( 'Properties', count( $old_items ) );
		foreach($old_items as $old_item) {
			$this->counter();
			
			$id = (int)$old_item['property_id'];
			$search_internal_name = $descriptions[$id][$this->default_language]['name'];
			
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
			$kind_of_product_id = $this->getKindOfProductId( (int)$old_item['categories_id'] );
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
				
				$this->properties_map[ $id ] = $exists_id;
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
				
				$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id ) use ($descriptions, $id, $item) {
					$sd = $item->getEshopData( $eshop );
					
					if(isset($descriptions[$id][$lang_id])) {
						$old_d = $descriptions[$id][$lang_id];
						$sd->setLabel( $old_d['name'] );
						$sd->setBoolYesDescription( $old_d['bool_yes_description'] );
						$sd->setUnits( $old_d['units'] );
					}
				});
				
				
				$item->save();
				
				if($old_item['is_active']) {
					$item->activateCompletely();
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
	}
	
	public function doPropertyOptions() : void
	{
		$this->new_db->execute("TRUNCATE properties_options;");
		$this->new_db->execute("TRUNCATE properties_options_eshop_data;");
		
		$p_ids = Property::dataFetchCol(select:['id']);
		
		$old_items = $this->old_db->fetchAll("SELECT
			option_id,
			property_id,
			priority
		FROM
			{$this->db_tables['categories_properties_properties_options']}
		WHERE
			property_id in (".implode(', ', $p_ids).")");
		
		$_descriptions = $this->old_db->fetchAll(
			"SELECT
				property_id,
				option_id as id,
				label as name,
				language_id,
				url_param
			FROM
				{$this->db_tables['categories_properties_properties_options_description']}");
		
		$descriptions = [];
		foreach($_descriptions as $d) {
			$id = (int)$d['id'];
			$language_id = (int)$d['language_id'];
			
			$descriptions[$id][$language_id] = $d;
		}
		
		$this->initCounter( 'Property options', count( $old_items ) );
		foreach($old_items as $old_item) {
			$this->counter();
			
			$id = (int)$old_item['option_id'];
			$priority = (int)$old_item['priority'];
			$property_id = (int)$old_item['property_id'];
			if(isset($this->properties_map[$property_id])) {
				$property_id = $this->properties_map[$property_id];
			}
			
			$internal_name = $descriptions[$id][$this->default_language]['name'];
			
			$exists_id = Property_Options_Option::dataFetchOne(['id'], [
				'property_id' => $property_id,
				'AND',
				'internal_name' => $internal_name
			]);
			
			if($exists_id) {
				$this->options_map[$id] = $exists_id;
			}
			
			$item = new Property_Options_Option();
			
			$item->setId( $id );
			$item->setInternalName( $internal_name );
			$item->setPriority( $priority );
			$item->setPropertyId( $property_id );
			
			$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id ) use ($descriptions, $id, $item) {
				$sd = $item->getEshopData( $eshop );
				
				if(isset($descriptions[$id][$lang_id])) {
					$sd->setFilterLabel( $descriptions[$id][$lang_id]['name'] );
					$sd->setProductDetailLabel( $descriptions[$id][$lang_id]['name'] );
				}
			});
			
			$item->save();
			
			$item->activateCompletely();
		}
		
	}
	
	public function doCategories() : void
	{
		$this->new_db->execute("TRUNCATE categories;");
		$this->new_db->execute("TRUNCATE categories_eshop_data;");
		
		
		$old_items = $this->old_db->fetchAll("SELECT
			categories_id as id,
			parent_id,
			sort_order,
			$this->category_cactive_column as is_active,
			symlink_target_filter,
			symlink_target_category_id,
			is_symlink,
			symlink_strategy,
			properties_strategy,
			properties_inherited_category_id
		FROM
			{$this->db_tables['categories']}");
		
		$_descriptions = $this->old_db->fetchAll(
			"SELECT
			categories_id as id,
			language_id,
			categories_name,
			categories_description
		FROM
			{$this->db_tables['categories_description']}");
		
		
		$descriptions = [];
		foreach($_descriptions as $d) {
			$id = (int)$d['id'];
			$language_id = (int)$d['language_id'];
			
			$descriptions[$id][$language_id] = $d;
		}
		
		foreach($old_items as $old_item) {
			
			if(!$old_item['symlink_target_category_id']) {
				continue;
			}
			
			$id = $old_item['id'];
			$tg_id = $old_item['symlink_target_category_id'];
			
			$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id ) use ( &$descriptions, $id, $tg_id ) {
				
				if(empty($descriptions[$id][$lang_id]['categories_name'])) {
					$descriptions[$id][$lang_id]['categories_name'] = $descriptions[$tg_id][$lang_id]['categories_name']??'';
				}
				
				if(empty($descriptions[$id][$lang_id]['categories_description'])) {
					$descriptions[$id][$lang_id]['categories_description'] = $descriptions[$tg_id][$lang_id]['categories_description']??'';
				}
			});
			
		}
		
		$activate_items = [];
		
		
		$this->initCounter( 'Categories', count( $old_items ) );
		foreach($old_items as $old_item) {
			$this->counter();
			
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
			$item->setInternalName( $descriptions[$id][$this->default_language]['categories_name'] );
			$item->setPriority( $priority, false );
			$item->setParentId( $parent_id );
			$item->setKindOfProductId( $this->getKindOfProductId( $id ) );
			
			
			$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id ) use ($descriptions, $id, $item) {
				$sd = $item->getEshopData( $eshop );
				if(isset($descriptions[$id][$lang_id])) {
					$sd->setName( $descriptions[$id][$lang_id]['categories_name'] );
					$sd->setDescription( $descriptions[$id][$lang_id]['categories_description'] );
				}
			});
			
			
			
			$item->save();
			
			foreach($this->eshop_map as $old_key=>$eshop) {
				$sd = $item->getEshopData( $eshop );
				$sd->generateURLPathPart();
			}
			
			if($is_active) {
				$activate_items[] = $item;
			}
		}
		
		
		foreach($activate_items as $item) {
			$item->activateCompletely();
		}
		
		Category::actualizeTreeData();
		
	}
	
	
	public function doBrands() : void
	{
		$this->new_db->execute("TRUNCATE brands;");
		$this->new_db->execute("TRUNCATE brands_eshop_data;");
		
		$old_items = $this->old_db->fetchAll("SELECT
			manufacturers_id as id,
			manufacturers_name as name
		FROM
			{$this->db_tables['manufacturers']}
		");
		
		$this->initCounter( 'Brands', count( $old_items ) );
		foreach($old_items as $old_tem) {
			$this->counter();
			
			$item = new Brand();
			
			$item->setId( $old_tem['id'] );
			$item->setInternalName( $old_tem['name'] );
			
			$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id ) use ($item,$old_tem) {
				$sd = $item->getEshopData( $eshop );
				$sd->setName( $old_tem['name'] );
			});
			
			$item->save();
			
			$item->activateCompletely();
		}
	}
	
	
	public function doProducts() : void
	{
		$this->new_db->execute("TRUNCATE products;");
		$this->new_db->execute("TRUNCATE products_eshop_data;");
		$this->new_db->execute("TRUNCATE products_set_items;");
		
		$product_status_columns =
			"products_status,
			products_status_sk,
			products_status_hu,
			products_status_ro,
			
			products_view_vel,
			
			";
		
		$productIActive = function( array $old_item ) : bool {
			return (bool)$old_item['products_status'];
		};
		
		$productIsAcriveForEShop = function( array $old_item, EShop $eshop ) {
			
			if($eshop->getCode()=='b2b') {
				if(!$old_item['products_view_vel']) {
					return false;
				}
				
				$disabled_groups = $this->old_db->fetchCol("SELECT group_id FROM porducts_velkosport_groups_disallow WHERE product_id=".$old_item['id']);
				
				$group = match ($eshop->getLocale()->toString()) {
					'cs_CZ' => static::B2B_GROUP_CZ,
					'sk_SK' => static::B2B_GROUP_SK,
					default => static::B2B_GROUP_EU,
				};
				
				if(in_array( $group, $disabled_groups )) {
					return false;
				}
				
				return true;
			}
			
			return match ($eshop->getLocale()->toString()) {
				'cs_CZ' => (bool)$old_item['products_status'],
				'sk_SK' => (bool)$old_item['products_status_sk'],
				default => false,
			};
		};
		
		
		$old_items = $this->old_db->fetchAll("SELECT
			products_id as id,
			main_category_id,
			products_quantity,
			products_model,
			manufacturers_id,
			
			$product_status_columns
			
			_HelCis,
			is_set,
			has_variants,
			is_variant,
			variant_master_product_id
		FROM
			{$this->db_tables['products']}
		");
		
		$_descriptions = $this->old_db->fetchAll(
			"SELECT
			products_id as id,
			language_id,
			products_name,
			products_description,
			products_info
		FROM
			{$this->db_tables['products_description']}");
		
		$descriptions = [];
		foreach($_descriptions as $d) {
			$id = (int)$d['id'];
			$language_id = (int)$d['language_id'];
			
			if($d['products_name']=='(kopie)') {
				$d['products_name'] = '';
			}
			
			$descriptions[$id][$language_id] = $d;
		}
		
		$activate_items = [];
		
		$this->initCounter( 'Products', count( $old_items ) );
		foreach($old_items as $old_item) {
			$id = (int)$old_item['id'];
			
			$this->counter( $id );
			
			
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
			$item->setInternalName( $descriptions[$id][$this->default_language]['products_name']??'' );
			$item->setType( Product::PRODUCT_TYPE_REGULAR );
			$item->setKindId( $this->getKindOfProductId( $old_item['main_category_id']??0 ) );
			$item->setInternalCode( $old_item['products_model']??'' );
			$item->setBrandId( $old_item['manufacturers_id']??0 );
			$item->setErpId( $old_item['_HelCis']??'' );
			
			
			if($old_item['is_set']) {
				$item->setType( Product::PRODUCT_TYPE_SET );
				
				$old_set_items = $this->old_db->fetchAll("SELECT
					related_product_id,
					sort_order,
					default_items_count
				FROM {$this->db_tables['products_sets']} WHERE
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
				
				
				$master_names = [];
				foreach( $master_descriptions as $language_id=>$d ) {
					$master_names[$language_id] = $d['products_name'];
				}
				$old_variant_names = [];
				foreach($variant_descriptions as $language_id=>$d) {
					$old_variant_names[$language_id] = $d['products_name'];
				}
				
				$variant_names = [];
				foreach($variant_descriptions as $language_id=>$d) {
					if(!isset($master_names[$language_id])) {
						continue;
					}
					
					$variant_name = substr( $old_variant_names[$language_id], strlen(  $master_names[$language_id]) );
					$variant_name = trim($variant_name);
					$variant_name = ltrim($variant_name, " -\\/");
					$variant_name = trim($variant_name);
					
					$variant_names[$language_id] = $variant_name;
					
				}
				
				$this->foreEchShop(
					function( EShop $eshop, int $lang_id, int $old_eshop_id )
					use ($variant_names, $master_names, $item, $descriptions, $id)
					{
						$sd = $item->getEshopData( $eshop );
						
						$sd->setName( $descriptions[$id][$lang_id]['products_name']??$descriptions[$id][$this->default_language]['products_name']??'' );
						$sd->setDescription( $descriptions[$id][$lang_id]['products_description']??$descriptions[$id][$this->default_language]['products_description']??'' );
						$sd->setShortDescription( $descriptions[$id][$lang_id]['products_info']??$descriptions[$id][$this->default_language]['products_info']??'' );
						
						if(isset($variant_names[$lang_id])) {
							$sd->setName( $master_names[$lang_id] );
							$sd->setVariantName( $variant_names[$lang_id] );
						}
					}
				);
				
				if(isset($variant_names[$this->default_language])) {
					$item->setInternalName( $master_names[$this->default_language] );
					$item->setInternalNameOfVariant( $variant_names[$this->default_language] );
				}
				
				
				
				foreach($variant_names as $language_id=>$variant_name) {
					if(!isset( $eshops[$language_id])) {
						continue;
					}
					
					$sd = $item->getEshopData( $eshops[$language_id] );
					$sd->setVariantName(
						$variant_name
					);
					
				}
				
			} else {
				if($old_item['has_variants']) {
					$item->setType( Product::PRODUCT_TYPE_VARIANT_MASTER );
				}
				
				$this->foreEchShop(function( EShop $eshop, int $lang_id, int $old_eshop_id ) use ($item, $id, $descriptions) {
					$sd = $item->getEshopData( $eshop );
					if(isset($descriptions[$id][$lang_id])) {
						$sd->setName( $descriptions[$id][$lang_id]['products_name'] );
						$sd->setDescription( $descriptions[$id][$lang_id]['products_description'] );
						$sd->setShortDescription( $descriptions[$id][$lang_id]['products_info']??'' );
					}
				});
				
			}
			
			
			$item->save();
			
			
			if($productIActive($old_item)) {
				Product::updateData(['is_active'=>true], ['id'=>$item->getId()]);
				Product_EShopData::updateData(['entity_is_active'=>true], ['entity_id'=>$item->getId()]);
			}
			
			$this->foreEchShop(function( EShop $eshop, int $lang_id, int $old_eshop_id ) use ($item, $id, $productIsAcriveForEShop, $old_item) {
				$sd = $item->getEshopData( $eshop );
				
				
				if($productIsAcriveForEShop($old_item, $eshop)) {
					$where = $sd->getEshop()->getWhere();
					$where[] = 'AND';
					$where['entity_id'] = $item->getId();
					
					Product_EShopData::updateData(['is_active_for_eshop'=>true], $where);
				}
			});
			
			
		}
	}
	
	public function doProductProperties() : void
	{
		$this->new_db->execute("TRUNCATE TABLE products_parameters");
		$this->new_db->execute("TRUNCATE TABLE products_text_parameters");
		
		$types = Property::dataFetchPairs([
			'id',
			'type'
		]);
		
		$property_ids = implode(', ', Property::dataFetchCol(select:['id']));
		
		$old_properties = $this->old_db->fetchAll("SELECT
		products_id,
		property_id,
		value
	FROM
		{$this->db_tables['products_properties']}
	WHERE
		property_id IN ($property_ids) AND
		information_is_not_available=0 AND
		value<>'' AND
		value<>'a:0:{}'
	");
		
		$this->initCounter( 'Product properties', count( $old_properties ) );
		foreach( $old_properties as $item ) {
			$this->counter();
			//var_dump( $item );
			
			$product_id = (int)$item['products_id'];
			$property_id = (int)$item['property_id'];
			if(isset($this->properties_map[$property_id])) {
				$property_id = $this->properties_map[$property_id];
			}
			
			$item['value'] = trim($item['value']);
			
			if( str_starts_with($item['value'], '[') ) {
				$texts = explode("\n", $item['value']);
				
				foreach($texts as $text) {
					$text = trim($text, '[]');
					$texts = [];
					
					if(
						str_starts_with($text,'CZ:') ||
						str_starts_with($text,'cz:')
					) {
						$text = substr($text, 3);
						$texts[static::CZ_LANG_ID] = $text;
					}
					
					if(
						str_starts_with($text,'SK:') ||
						str_starts_with($text,'sk:')
					) {
						$text = substr($text, 3);
						$texts[static::SK_LANG_ID] = $text;
					}
					
					$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id )
						use( $product_id, $property_id, $texts)
					{
						$item = new Product_Parameter_TextValue();
						$item->setEshop( $eshop );
						$item->setProductId( $product_id );
						$item->setPropertyId( $property_id );
						$item->setText( $texts[$lang_id]??'' );
						$item->save();
						
					} );
					
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
					if(isset($this->options_map[$value])) {
						$value = $this->options_map[$value];
					}
				}
				
				$item->setValue( (int)$value );
				$item->save();
			}
			
		}
		
	}
	
	public function doActualizeSet() : void
	{
		$set_ids = Product::dataFetchCol(['id'], ['type'=>Product::PRODUCT_TYPE_SET]);
		
		$this->initCounter( 'Product set', count( $set_ids ) );
		foreach($set_ids as $id) {
			$this->counter();
			
			Product::load($id)->actualizeSet();
		}
	}
	
	public function doActualizeVariantMasters() : void
	{
		$set_ids = Product::dataFetchCol(['id'], ['type'=>Product::PRODUCT_TYPE_VARIANT_MASTER]);
		
		$this->initCounter( 'Product variants', count( $set_ids ) );
		foreach($set_ids as $id) {
			$this->counter();
			
			Product::load($id)->actualizeVariantMaster();
		}
	}
	
	public function doCategoriesAssoc() : void
	{
		
		$this->new_db->execute("TRUNCATE TABLE categories_products");
		
		$categories = Category::dataFetchCol(['id']);
		
		$variants = Product::dataFetchCol(['id'], ['type'=>Product::PRODUCT_TYPE_VARIANT]);
		
		$items = $this->old_db->fetchAll("SELECT
		products_id,
		categories_id
	FROM
		{$this->db_tables['products_to_categories']}
	WHERE
		categories_id IN (".implode(', ', $categories).")");
		
		$this->initCounter( 'Categories - assoc - save', count( $items ) );
		foreach($items as $c) {
			$this->counter();
			
			$product_id = (int)$c['products_id'];
			$category_id = (int)$c['categories_id'];
			if(in_array($product_id, $variants)) {
				continue;
			}
			
			$product = Product::dataFetchRow(select: ['type', 'variant_master_product_id'], where: ['id'=>$product_id]);
			if(!$product) {
				continue;
			}
			if($product['type']==Product::PRODUCT_TYPE_VARIANT) {
				$product_id = $product['variant_master_product_id'];
			}
			
			$assoc = new Category_Product();
			$assoc->setCategoryId( $category_id );
			$assoc->setProductId( $product_id );
			$assoc->save();
		}
		
		$roots = array_unique(Category::dataFetchCol(['root_id']));
		
		$this->initCounter( 'Categories - assoc - actualize', count( $roots ) );
		foreach($roots as $root_id) {
			$this->counter();
			
			Category::actualizeBranchProductAssoc( $root_id );
		}
	}
	
	public function doVK() : void
	{
		$vks = $this->old_db->fetchAll("SELECT
		categories_id,
		symlink_target_filter,
		symlink_target_category_id
	FROM
		{$this->db_tables['categories']}
	WHERE
		is_symlink=1 and
		symlink_strategy='is_virtual_category'");
		
		$this->initCounter( 'VKs', count( $vks ) );
		foreach($vks as $vk) {
			$this->counter();
			
			$c_id = $vk['categories_id'];
			
			$category = Category::load( $c_id );
			$category->setAutoAppendProducts( true );
			$category->save();
			
			$filter_settings = unserialize($vk['symlink_target_filter']);
			
			$filter = $category->getAutoAppendProductsFilter();
			
			
			$filter->getBasicFilter()->setKindOfProductId( $vk['symlink_target_category_id'] );
			
			if($filter_settings['properties']) {
				foreach($filter_settings['properties'] as $property_id=>$selected_options) {
					if(isset($this->properties_map[$property_id])) {
						$property_id = $this->properties_map[$property_id];
					}
					
					if(is_array($selected_options)) {
						if($selected_options) {
							foreach($selected_options as $i=>$option_id) {
								if(isset($this->options_map[$option_id])) {
									$selected_options[$i] = $this->options_map[$option_id];
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
		
	}
	
	public function doFulltext() : void
	{
		
		$this->new_db->execute("TRUNCATE `fulltext_internal`;");
		$this->new_db->execute("TRUNCATE `fulltext_internal_word`;");
		$this->new_db->execute("TRUNCATE `fulltext_eshop`;");
		$this->new_db->execute("TRUNCATE `fulltext_eshop_word`;");
		
		$updateIndex = function( string $class ) {
			/**
			 * @var Entity_WithEShopData $class
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
					echo "$et: [$index] {$c->getId()}\n";
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
		
	}
	
	public function doSignposts() : void
	{
		$this->new_db->execute('TRUNCATE TABLE signposts');
		$this->new_db->execute('TRUNCATE TABLE signposts_categories');
		$this->new_db->execute('TRUNCATE TABLE signposts_eshop_data');
		
		$old_items = $this->old_db->fetchAll("SELECT
		categories_id as id,
		sort_order,
		sub_categories,
		status
	FROM {$this->db_tables['top_categories']}");
		
		$_descriptions = $this->old_db->fetchAll(
			"SELECT
			categories_id as id,
			categories_name as name,
			categories_description as description,
			language_id
		FROM
			{$this->db_tables['top_categories_description']}");
		
		$descriptions = [];
		foreach($_descriptions as $d) {
			$id = (int)$d['id'];
			$language_id = (int)$d['language_id'];
			
			$descriptions[$id][$language_id] = $d;
		}
		
		$this->initCounter( 'Signposts ', count( $old_items ) );
		foreach($old_items as $old_item) {
			$this->counter();
			
			$id = (int)$old_item['id'];
			$priority = (int)$old_item['sort_order'];
			$is_active = (bool)$old_item['status'];
			
			$item = new Signpost();
			
			$item->setId( $id );
			$item->setInternalName( $descriptions[$id][$this->default_language]['name'] );
			$item->setPriority( $priority );
			
			
			$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id ) use
					($item, $descriptions, $id) : void {
				
				$sd = $item->getEshopData( $eshop );
				if(isset($descriptions[$id][$lang_id])) {
					$sd->setName( $descriptions[$id][$lang_id]['name']??'' );
					$sd->setDescription( $descriptions[$id][$lang_id]['description']??'' );
				}

			} );
			
			
			$item->save();
			
			$sc = explode(',', $old_item['sub_categories']);
			foreach($sc as $c_id) {
				if($c_id) {
					$item->addCategory( $c_id );
				}
			}
			
			$this->foreEchShop( function( EShop $eshop, int $lang_id, int $old_eshop_id ) use ($item) : void {
				
				$sd = $item->getEshopData( $eshop );
				$sd->generateURLPathPart();
			} );
			
			if($is_active) {
				$item->activateCompletely();
			}
			
		}
		
	}
	
	public function doReviews() : void
	{
		$this->new_db->execute("TRUNCATE TABLE product_reviews");
		
		$items = $this->old_db->fetchAll("SELECT
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
				FROM {$this->db_tables['products_reviews']}");
		
		$this->initCounter( 'Reviews ', count( $items ) );
		foreach($items as $item) {
			$this->counter();
			
			$review = new class($item, $this->eshops_by_id) extends ProductReview {
				public function __construct( array $item, $eshops_by_id )
				{
					$this->id = $item['id'];
					$this->created = Data_DateTime::catchDateTime( $item['added_date_time'] );
					$this->setEshop( $eshops_by_id[$item['eshop_id']] );
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
		
	}
	
	public function doQuestions() : void
	{
		$this->new_db->execute("TRUNCATE TABLE product_questions");
		
		$items = $this->old_db->fetchAll("SELECT
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
				FROM {$this->db_tables['products_questions']}");
		
		$this->initCounter( 'Questions ', count( $items ) );
		foreach($items as $item) {
			$this->counter();
			
			$review = new class($item, $this->eshops_by_id) extends ProductQuestion {
				public function __construct( array $item, $eshops_by_id )
				{
					$this->id = $item['id'];
					$this->created = Data_DateTime::catchDateTime( $item['added_date_time'] );
					$this->setEshop( $eshops_by_id[$item['eshop_id']] );
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
	}
	
	public function doPrices() : void
	{
		$this->new_db->execute("TRUNCATE TABLE products_price");
		
		$data = $this->old_db->fetchAll("SELECT
			products_id as id,
			
			products_tax_class_id,
			products_tax_class_id_sk,
	
			products_price,
			products_price_sk,
			
			final_price,
			final_price_sk,
			
			products_price_3
			
		FROM {$this->db_tables['products']}");
		
		$vat_rates_cz = [
		
		];
		
		$vat_rates_cz = [
			4 => 0,
			5 => 15,
			3 => 21,
		];
		
		$vat_rates_sk = [
			3 => 20,
		];
		
		
		$this->initCounter( 'Prices ', count( $data ) );
		foreach( $data as $d ) {
			$this->counter();
			
			$b2c_cz_pricelist = Pricelists::get('b2c_cz');
			$b2c_cz_price = Product_Price::get( $b2c_cz_pricelist, $d['id'] );
			$b2c_cz_price->setVatRate( $vat_rates_cz[$d['products_tax_class_id']]??21 );
			$b2c_cz_price->setPriceWithoutVAT( $d['final_price'] );
			$b2c_cz_price->save();
			
			
			$b2c_sk_pricelist = Pricelists::get('b2c_sk');
			$b2c_sk_price = Product_Price::get( $b2c_sk_pricelist, $d['id'] );
			$b2c_sk_price->setVatRate( $vat_rates_sk[$d['products_tax_class_id_sk']]??20 );
			$b2c_sk_price->setPriceWithoutVAT( $d['final_price_sk'] );
			$b2c_sk_price->save();
			
			
			$b2b_cz_pricelist = Pricelists::get('b2b_cz');
			$b2b_cz_price = Product_Price::get( $b2b_cz_pricelist, $d['id'] );
			$b2b_cz_price->setVatRate( $vat_rates_cz[$d['products_tax_class_id']]??21 );
			$b2b_cz_price->setPriceWithoutVAT( $d['final_price_sk'] );
			$b2b_cz_price->save();
			
			
			$b2b_sk_pricelist = Pricelists::get('b2b_sk');
			$b2b_sk_price = Product_Price::get( $b2b_sk_pricelist, $d['id'] );
			$b2b_sk_price->setVatRate( 0 );
			$b2b_sk_price->setPriceWithoutVAT( $d['final_price_sk'] );
			$b2b_sk_price->save();
			
			
			$b2b_eu_pricelist = Pricelists::get('b2b_eu');
			$b2b_eu_price = Product_Price::get( $b2b_eu_pricelist, $d['id'] );
			$b2b_eu_price->setVatRate( 0 );
			$b2b_eu_price->setPriceWithoutVAT( $d['products_price_3']>0?$d['products_price_3']: ($d['products_price']/25) );
			$b2b_eu_price->save();

		}
		
		
	}
	
	public function doAVL() : void
	{
		/*
		$avl = Product_Availability::get( $sd->getEshop()->getDefaultAvailability(), $sd->getId() );
		$avl->setNumberOfAvailable( $old_item['products_quantity'] );
		$avl->save();
		*/
		
		
		
		/*
		products_date_available,
		products_date_available_sk,
		
		$prices = Product_EShopData::dataFetchAll([
			'eshop_code',
			'entity_id',
			'length_of_delivery',
			'available_from',
			'in_stock_qty',
		]);
		
		foreach($prices as $p) {
			if($p['eshop_code']=='sk') {
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
	}
	
}





/*

;
*/





EShops::setCurrent( EShops::getDefault() );

$m = new Migration();

//$m->doBrands();

//$m->doKindOfProducts();
//$m->doPropertyGroups();
//$m->doProperties();
//$m->doPropertyOptions();

//$m->doCategories();

//$m->doProducts();
//$m->doProductProperties();

//$m->doActualizeSet();
//$m->doActualizeVariantMasters();

//$m->doCategoriesAssoc();

//$m->doVK();

//$m->doSignposts();

//$m->doReviews();

//$m->doQuestions();

//$m->doPrices();
//$m->doAVL();

//$m->doFulltext();
