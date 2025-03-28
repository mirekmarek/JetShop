<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Category_EShopData;
use JetApplication\ProductListing;

#[DataModel_Definition(
	name: 'ja_event_category_view',
	database_table_name: 'ja_event_category_view',
)]
class Event_CategoryView extends Event
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected string $category_id = '';
	
	protected ?Event_ProductsListView $product_list_view = null;
	
	public function cancelDefaultEvent(): bool
	{
		return true;
	}
	
	public function init( Category_EShopData $category, ?ProductListing $product_listing=null ) : void
	{
		$this->category_id = $category->getId();
		if($product_listing) {
			$this->product_list_view = new Event_ProductsListView();
			$this->product_list_view->init( $product_listing );
		}
	}
	
	public function afterAdd() : void
	{
		parent::afterAdd();
		
		if( $this->product_list_view ) {
			$this->product_list_view->setEvent( $this );
			$this->product_list_view->save();
		}
	}
	
	public function getTitle(): string
	{
		return Tr::_('Category view');
	}
	
	public function getCssClass(): string
	{
		return 'light';
	}
	
	public function showShortDetails(): string
	{
		return Admin_Managers::Category()->renderItemName( $this->category_id );
	}
	
	public function getIcon() : string
	{
		return 'list';
	}
	
	public function showLongDetails(): string
	{
		$listing = Event_ProductsListView::getFormEvent( $this );

		if(!$listing) {
			return '';
		}
		
		return $listing->showLongDetails();
	}
	
}