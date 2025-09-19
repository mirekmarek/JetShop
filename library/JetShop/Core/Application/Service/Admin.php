<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_Admin_Content_MagicTags;
use JetApplication\Application_Service_Admin_Document;
use JetApplication\Application_Service_Admin_MoneyRefund;
use JetApplication\Application_Service_Admin_Note;
use JetApplication\Application_Service_Admin_OrderPersonalReceipt;
use JetApplication\Application_Service_Admin_Signpost;
use JetApplication\Application_Service_Admin_Todo;
use JetApplication\EShopConfig;
use JetApplication\Application_Service_Admin_Context;
use JetApplication\Application_Service_Admin_Invoice;
use JetApplication\Application_Service_Admin_ProformaInvoice;
use JetApplication\Application_Service_Admin_DeliveryNote;
use JetApplication\Application_Service_Admin_SupplierGoodsOrders;
use JetApplication\Application_Service_Admin_Category;
use JetApplication\Application_Service_Admin_Brand;
use JetApplication\Application_Service_Admin_OrderDispatch;
use JetApplication\Application_Service_Admin_Product;
use JetApplication\Application_Service_Admin_Property;
use JetApplication\Application_Service_Admin_PropertyGroup;
use JetApplication\Application_Service_Admin_KindOfProduct;
use JetApplication\Application_Service_Admin_Image;
use JetApplication\Application_Service_Admin_UI;
use JetApplication\Application_Service_Admin_FulltextSearch;
use JetApplication\Application_Service_Admin_EShopEntity_Listing;
use JetApplication\Application_Service_Admin_EShopEntity_Edit;
use JetApplication\Application_Service_Admin_ProductFilter;
use JetApplication\Application_Service_Admin_PriceFormatter;
use JetApplication\Application_Service_Admin_Order;
use JetApplication\Application_Service_Admin_Complaint;
use JetApplication\Application_Service_Admin_ReturnOfGoods;
use JetApplication\Application_Service_Admin_Customer;
use JetApplication\Application_Service_Admin_Timer;
use JetApplication\Application_Service_Admin_WarehouseManagement_Overview;
use JetApplication\Application_Service_Admin_ReceiptOfGoods;
use JetApplication\Application_Service_Admin_WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\Application_Service_Admin_WarehouseManagement_LossOrDestruction;
use JetApplication\Application_Service_List;


class Core_Application_Service_Admin {
	
	public const GROUP = 'admin';
	
	protected static ?Application_Service_List $list = null;
	
	public static function list(): Application_Service_List
	{
		if(!static::$list) {
			static::$list = new Application_Service_List(
				EShopConfig::getRootDir().'services/admin.php',
				static::GROUP
			);
		}
		
		return static::$list;
	}

	public static function Category() : Application_Service_Admin_Category|Application_Module
	{
		return static::list()->get( Application_Service_Admin_Category::class );
	}
	
	public static function Brand() : Application_Service_Admin_Brand|Application_Module
	{
		return static::list()->get( Application_Service_Admin_Brand::class );
	}
	
	public static function Product() : Application_Service_Admin_Product|Application_Module
	{
		return static::list()->get( Application_Service_Admin_Product::class );
	}
	
	public static function Property() : Application_Service_Admin_Property|Application_Module
	{
		return static::list()->get( Application_Service_Admin_Property::class );
	}
	
	public static function PropertyGroup() : Application_Service_Admin_PropertyGroup|Application_Module
	{
		return static::list()->get( Application_Service_Admin_PropertyGroup::class );
	}
	
	public static function KindOfProduct() : Application_Service_Admin_KindOfProduct|Application_Module
	{
		return static::list()->get( Application_Service_Admin_KindOfProduct::class );
	}
	
	public static function Image() : Application_Service_Admin_Image|Application_Module
	{
		return static::list()->get( Application_Service_Admin_Image::class );
	}
	
	public static function MagicTags() : Application_Service_Admin_Content_MagicTags|Application_Module
	{
		return static::list()->get( Application_Service_Admin_Content_MagicTags::class );
	}
	
	public static function TODO() : Application_Service_Admin_Todo|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_Todo::class );
	}
	
	public static function UI() : Application_Service_Admin_UI|Application_Module
	{
		return static::list()->get( Application_Service_Admin_UI::class );
	}
	
	public static function FulltextSearch() : Application_Service_Admin_FulltextSearch|Application_Module
	{
		return static::list()->get( Application_Service_Admin_FulltextSearch::class );
	}
	
	public static function EntityListing() : Application_Service_Admin_EShopEntity_Listing|Application_Module
	{
		return static::list()->get( Application_Service_Admin_EShopEntity_Listing::class );
	}
	
	public static function EntityEdit() : Application_Service_Admin_EShopEntity_Edit|Application_Module
	{
		return static::list()->get( Application_Service_Admin_EShopEntity_Edit::class );
	}

	
	public static function ProductFilter() : Application_Service_Admin_ProductFilter|Application_Module
	{
		return static::list()->get( Application_Service_Admin_ProductFilter::class );
	}
	
	public static function PriceFormatter() : Application_Service_Admin_PriceFormatter|Application_Module
	{
		return static::list()->get( Application_Service_Admin_PriceFormatter::class );
	}
	
	public static function Order() : Application_Service_Admin_Order|Application_Module
	{
		return static::list()->get( Application_Service_Admin_Order::class );
	}
	
	public static function Invoice() : Application_Service_Admin_Invoice|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_Invoice::class );
	}
	
	public static function ProformaInvoice() : Application_Service_Admin_Invoice|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_ProformaInvoice::class );
	}
	
	public static function DeliveryNote() : Application_Service_Admin_DeliveryNote|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_DeliveryNote::class );
	}
	
	public static function OrderDispatch() : Application_Service_Admin_OrderDispatch|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_OrderDispatch::class );
	}
	
	public static function OrderPersonalReceipt() : Application_Service_Admin_OrderPersonalReceipt|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_OrderPersonalReceipt::class );
	}
	
	public static function ReceiptOfGoods() : Application_Service_Admin_ReceiptOfGoods|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_ReceiptOfGoods::class );
	}
	
	public static function TransferBetweenWarehouses() : Application_Service_Admin_WarehouseManagement_TransferBetweenWarehouses|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_WarehouseManagement_TransferBetweenWarehouses::class );
	}
	
	public static function Complaint() : Application_Service_Admin_Complaint|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_Complaint::class );
	}
	
	public static function MoneyRefund() : Application_Service_Admin_MoneyRefund|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_MoneyRefund::class );
	}
	
	
	public static function ReturnOfGoods() : Application_Service_Admin_ReturnOfGoods|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_ReturnOfGoods::class );
	}
	
	public static function Customer() : Application_Service_Admin_Customer|Application_Module
	{
		return static::list()->get( Application_Service_Admin_Customer::class );
	}
	
	public static function Timer() : Application_Service_Admin_Timer|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_Timer::class );
	}
	
	public static function WarehouseManagementOverview() : Application_Service_Admin_WarehouseManagement_Overview|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_WarehouseManagement_Overview::class );
	}
	
	public static function Context() : Application_Service_Admin_Context|Application_Module
	{
		return static::list()->get( Application_Service_Admin_Context::class );
	}
	
	public static function SupplierGoodsOrders() : Application_Service_Admin_SupplierGoodsOrders|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_SupplierGoodsOrders::class );
	}
	
	public static function LossOrDestruction() : Application_Service_Admin_WarehouseManagement_LossOrDestruction|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_WarehouseManagement_LossOrDestruction::class );
	}
	
	public static function Note() : Application_Service_Admin_Note|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_Note::class );
	}
	
	public static function Signpost() : Application_Service_Admin_Signpost|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_Signpost::class );
	}
	
	public static function Document() : Application_Service_Admin_Document|Application_Module|null
	{
		return static::list()->get( Application_Service_Admin_Document::class );
	}
	
}