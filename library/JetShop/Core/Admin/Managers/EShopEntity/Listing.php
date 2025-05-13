<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Closure;
use Jet\Application_Module;
use Jet\DataListing_Filter;
use JetApplication\Admin_EntityManager_Module;
use JetApplication\Admin_Listing_Column;
use JetApplication\Admin_Listing_Export;
use JetApplication\Admin_Listing_Filter;
use JetApplication\Admin_Listing_Handler;
use JetApplication\Admin_Listing_Operation;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Basic;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Entity Listing',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_EShopEntity_Listing extends Application_Module
{
	abstract public function setUp(
		Admin_EntityManager_Module $entity_manager
	) : void;
	
	abstract public function getEntity(): EShopEntity_Admin_Interface;
	
	abstract public function getEntityManager(): Admin_EntityManager_Module;
	
	abstract public function renderListing() : string;
	
	abstract public function getDeleteUriCreator(): callable;
	
	abstract public function setDeleteUriCreator( callable $delete_uri_creator ): void;
	
	abstract public function getCreateUriCreator(): callable;
	
	abstract public function setCreateUriCreator( callable $create_uri_creator ): void;
	
	abstract public function getCreateBtnRenderer(): ?callable;
	
	abstract public function setCreateBtnRenderer( callable $renderer ): void;
	
	abstract public function getCustomBtnRenderer(): ?callable;
	
	abstract public function setCustomBtnRenderer( callable $renderer ): void;
	
	abstract public function setDefaultSort( string $default_sort ): void;
	
	
	abstract public function addColumn( Admin_Listing_Column $column ) : void;
	
	abstract public function addFilter( Admin_Listing_Filter $filter ) : void;
	
	abstract public function addExport( Admin_Listing_Export $export ) : void;
	
	abstract public function addHandler( Admin_Listing_Handler $handler ) : void;
	
	abstract public function addOperation( Admin_Listing_Operation $operation ) : void;
	
	abstract public function setDefaultColumnsSchema( array $schema ) : void;
	
	abstract public function getPrevEditUrl( int $current_id ): string;
	
	abstract public function getNextEditUrl( int $current_id ): string;
	
	abstract public function getEditUrl( EShopEntity_Basic $item ): string;
	
	abstract public function setSearchWhereCreator( Closure $creator ) : void;
	
	abstract public function getSelectItemsEnabled(): bool;
	
	abstract public function setSelectItemsEnabled( bool $select_items_enabled ): void;
	
	
	abstract public function renderListingFilter(
		DataListing_Filter $filter,
		string $title,
		array $form_fields,
		bool $is_active,
		callable $renderer,
		string $reset_value = ''
	) : string;
}