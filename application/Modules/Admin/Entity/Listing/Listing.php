<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;

use JetApplication\Admin_Managers_EShopEntity_Listing;
use JetApplication\Admin_Listing;
use JetApplication\Admin_Managers;
use JetApplication\EShopEntity_HasActivation_Interface;
use JetApplication\EShopEntity_HasActivationByTimePlan_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasInternalParams_Interface;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\FulltextSearch_IndexDataProvider;
use JetApplication\EShops;


class Listing extends Admin_Listing
{
	
	public function __construct( Admin_Managers_EShopEntity_Listing $listing_manager )
	{
		parent::__construct( $listing_manager );
		
		$default_schema = [];
		$default_order_by = '-id';
		
		$this->addExport( new Listing_Export_CSV() );
		$this->addExport( new Listing_Export_XLSX() );
		
		
		$this->addFilter( $this->init_SearchFilter() );
		$this->addColumn( new Listing_Column_Edit() );
		$this->addColumn( new Listing_Column_ID() );
		
		$default_schema[] = Listing_Column_ID::KEY;
		
		if(
			($this->entity instanceof EShopEntity_HasEShopRelation_Interface ) &&
			EShops::isMultiEShopMode()
		) {
			$this->addColumn( new Listing_Column_EShop() );
			$this->addFilter( new Listing_Filter_EShop() );
			
			$default_schema[] = Listing_Column_EShop::KEY;
		}
		
		
		if(
			$this->entity instanceof EShopEntity_HasNumberSeries_Interface
		) {
			$this->addColumn( new Listing_Column_Number() );
			$default_schema[] = Listing_Column_Number::KEY;
		}

		
		if(
			$this->entity instanceof EShopEntity_HasActivation_Interface
		) {
			$this->addColumn( new Listing_Column_ActiveState() );
			
			$default_schema[] = Listing_Column_ActiveState::KEY;
			
			$this->addFilter( new Listing_Filter_IsActive() );
			
			if(
				$this->entity_manager::getCurrentUserCanEdit() &&
				!($this->entity instanceof EShopEntity_HasActivationByTimePlan_Interface)
			) {
				$this->addOperation( new Listing_Operation_Activate() );
				$this->addOperation( new Listing_Operation_Deactivate() );
			}
		}
		
		if(
			$this->entity_manager::getCurrentUserCanEdit() &&
			$this->entity::hasCommonPropertiesEditableByListingActions()
		) {
			$this->addOperation( new Listing_Operation_SetCommonProperties() );
		}
		
		
		if( $this->entity instanceof EShopEntity_HasActivationByTimePlan_Interface ) {
			$this->addColumn( new Listing_Column_ValidFrom() );
			$this->addColumn( new Listing_Column_ValidTill() );

			$default_schema[] = Listing_Column_ValidFrom::KEY;
			$default_schema[] = Listing_Column_ValidTill::KEY;
		}
		
		if( $this->entity instanceof EShopEntity_HasStatus_Interface ) {
			$this->addColumn( new Listing_Column_Status() );
			
			$default_schema[] = Listing_Column_Status::KEY;
			
			$status_filter = new Listing_Filter_Status();
			$status_filter->setStatusList( $this->entity::getStatusList() );
			$this->addFilter( $status_filter );
		}
		
		if( $this->entity instanceof EShopEntity_HasInternalParams_Interface ) {
			$this->addColumn( new Listing_Column_InternalName() );
			$this->addColumn( new Listing_Column_InternalCode() );
			$this->addColumn( new Listing_Column_InternalNotes() );
			
			$default_schema[] = Listing_Column_InternalName::KEY;
			$default_schema[] = Listing_Column_InternalCode::KEY;
			$default_schema[] = Listing_Column_InternalNotes::KEY;
			
			$default_order_by = '+internal_name';
		}
		
		if( $this->entity instanceof EShopEntity_HasImages_Interface ) {
			$this->addColumn( new Listing_Column_Images() );
		}
		
		$this->setDefaultColumnsSchema( $default_schema );
		
		$this->setDefaultSort($default_order_by);
		
	}
	
	protected function init_SearchFilter() : Listing_Filter_Search
	{
		$search = new Listing_Filter_Search();
		if($this->entity instanceof FulltextSearch_IndexDataProvider) {
			
			$entity = $this->entity;
			
			$search->setWhereCreator( function( string $search ) use ($entity) : array {
				
				$ids = Admin_Managers::FulltextSearch()->search(
					$entity::getEntityType(),
					$search
				);
				
				if(!$ids) {
					$ids = [0];
				}
				
				return [
					'id' => $ids,
				];
				
			} );
			
		}
		
		return $search;
	}
}