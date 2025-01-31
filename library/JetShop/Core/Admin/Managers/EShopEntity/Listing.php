<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Closure;
use Jet\DataListing_Column;
use Jet\DataListing_Export;
use Jet\DataListing_Filter;
use JetApplication\Admin_EntityManager_Module;
use JetApplication\EShopEntity_Basic;

interface Core_Admin_Managers_EShopEntity_Listing
{
	public function setUp(
		Admin_EntityManager_Module $entity_manager
	) : void;
	
	public function renderListing() : string;
	
	public function getDeleteUriCreator(): callable;
	
	public function setDeleteUriCreator( callable $delete_uri_creator ): void;
	
	public function getCreateUriCreator(): callable;
	
	public function setCreateUriCreator( callable $create_uri_creator ): void;
	
	public function getCreateBtnRenderer(): ?callable;
	
	public function setCreateBtnRenderer( callable $renderer ): void;
	
	public function getCustomBtnRenderer(): ?callable;
	
	public function setCustomBtnRenderer( callable $renderer ): void;
	
	public function setDefaultSort( string $default_sort ): void;
	
	
	public function addColumn( DataListing_Column $column ) : void;
	
	public function addFilter( DataListing_Filter $filter ) : void;
	
	public function addExport( DataListing_Export $export ) : void;
	
	public function setDefaultColumnsSchema( array $schema ) : void;
	
	public function getPrevEditUrl( int $current_id ): string;
	
	public function getNextEditUrl( int $current_id ): string;
	
	public function getEditUrl( EShopEntity_Basic $item ): string;

	public function setSearchWhereCreator( Closure $creator ) : void;
	
	public function renderListingFilter(
		DataListing_Filter $filter,
		string $title,
		array $form_fields,
		bool $is_active,
		callable $renderer,
		string $reset_value = ''
	) : string;
}