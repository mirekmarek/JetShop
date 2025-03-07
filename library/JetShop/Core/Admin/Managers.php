<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Admin_Managers_Content_MagicTags;
use JetApplication\Admin_Managers_MoneyRefund;
use JetApplication\Admin_Managers_Note;
use JetApplication\Admin_Managers_OrderPersonalReceipt;
use JetApplication\Admin_Managers_Signpost;
use JetApplication\Admin_Managers_Todo;
use JetApplication\EShopConfig;
use JetApplication\Managers;
use JetApplication\Manager_MetaInfo;
use JetApplication\Admin_Managers_Context;
use JetApplication\Admin_Managers_Invoice;
use JetApplication\Admin_Managers_InvoiceInAdvance;
use JetApplication\Admin_Managers_DeliveryNote;
use JetApplication\Admin_Managers_SupplierGoodsOrders;
use JetApplication\Admin_Managers_Category;
use JetApplication\Admin_Managers_Brand;
use JetApplication\Admin_Managers_OrderDispatch;
use JetApplication\Admin_Managers_Product;
use JetApplication\Admin_Managers_Property;
use JetApplication\Admin_Managers_PropertyGroup;
use JetApplication\Admin_Managers_KindOfProduct;
use JetApplication\Admin_Managers_Image;
use JetApplication\Admin_Managers_UI;
use JetApplication\Admin_Managers_FulltextSearch;
use JetApplication\Admin_Managers_EShopEntity_Listing;
use JetApplication\Admin_Managers_EShopEntity_Edit;
use JetApplication\Admin_Managers_ProductFilter;
use JetApplication\Admin_Managers_PriceFormatter;
use JetApplication\Admin_Managers_Order;
use JetApplication\Admin_Managers_Complaint;
use JetApplication\Admin_Managers_ReturnOfGoods;
use JetApplication\Admin_Managers_Customer;
use JetApplication\Admin_Managers_Timer;
use JetApplication\Admin_Managers_WarehouseManagement_Overview;
use JetApplication\Admin_Managers_ReceiptOfGoods;
use JetApplication\Admin_Managers_WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\Admin_Managers_WarehouseManagement_LossOrDestruction;


class Core_Admin_Managers extends Managers {
	
	/**
	 * @var Manager_MetaInfo[]|null
	 */
	protected static ?array $managers_meta_info = null;
	
	protected static ?array $config = null;
	
	protected static array $managers = [];
	
	public static function getCfgFilePath() : string
	{
		return EShopConfig::getRootDir().'managers/admin.php';
	}
	
	protected static function registerManagers() : void
	{
		static::_registerManagers( Manager_MetaInfo::GROUP_ADMIN );
	}
	
	public static function Category() : Admin_Managers_Category|Application_Module
	{
		return static::get( Admin_Managers_Category::class );
	}
	
	public static function Brand() : Admin_Managers_Brand|Application_Module
	{
		return static::get( Admin_Managers_Brand::class );
	}
	
	public static function Product() : Admin_Managers_Product|Application_Module
	{
		return static::get( Admin_Managers_Product::class );
	}
	
	public static function Property() : Admin_Managers_Property|Application_Module
	{
		return static::get( Admin_Managers_Property::class );
	}
	
	public static function PropertyGroup() : Admin_Managers_PropertyGroup|Application_Module
	{
		return static::get( Admin_Managers_PropertyGroup::class );
	}
	
	public static function KindOfProduct() : Admin_Managers_KindOfProduct|Application_Module
	{
		return static::get( Admin_Managers_KindOfProduct::class );
	}
	
	public static function Image() : Admin_Managers_Image|Application_Module
	{
		return static::get( Admin_Managers_Image::class );
	}
	
	public static function MagicTags() : Admin_Managers_Content_MagicTags|Application_Module
	{
		return static::get( Admin_Managers_Content_MagicTags::class );
	}
	
	public static function TODO() : Admin_Managers_Todo|Application_Module|null
	{
		return static::get( Admin_Managers_Todo::class );
	}
	
	public static function UI() : Admin_Managers_UI|Application_Module
	{
		return static::get( Admin_Managers_UI::class );
	}
	
	public static function FulltextSearch() : Admin_Managers_FulltextSearch|Application_Module
	{
		return static::get( Admin_Managers_FulltextSearch::class );
	}
	
	public static function EntityListing() : Admin_Managers_EShopEntity_Listing|Application_Module
	{
		return static::get( Admin_Managers_EShopEntity_Listing::class );
	}
	
	public static function EntityEdit() : Admin_Managers_EShopEntity_Edit|Application_Module
	{
		return static::get( Admin_Managers_EShopEntity_Edit::class );
	}

	
	public static function ProductFilter() : Admin_Managers_ProductFilter|Application_Module
	{
		return static::get( Admin_Managers_ProductFilter::class );
	}
	
	public static function PriceFormatter() : Admin_Managers_PriceFormatter|Application_Module
	{
		return static::get( Admin_Managers_PriceFormatter::class );
	}
	
	public static function Order() : Admin_Managers_Order|Application_Module
	{
		return static::get( Admin_Managers_Order::class );
	}
	
	public static function Invoice() : Admin_Managers_Invoice|Application_Module
	{
		return static::get( Admin_Managers_Invoice::class );
	}
	
	public static function InvoiceInAdvance() : Admin_Managers_Invoice|Application_Module
	{
		return static::get( Admin_Managers_InvoiceInAdvance::class );
	}
	
	public static function DeliveryNote() : Admin_Managers_DeliveryNote|Application_Module
	{
		return static::get( Admin_Managers_DeliveryNote::class );
	}
	
	public static function OrderDispatch() : Admin_Managers_OrderDispatch|Application_Module
	{
		return static::get( Admin_Managers_OrderDispatch::class );
	}
	
	public static function OrderPersonalReceipt() : Admin_Managers_OrderPersonalReceipt|Application_Module
	{
		return static::get( Admin_Managers_OrderPersonalReceipt::class );
	}
	
	public static function ReceiptOfGoods() : Admin_Managers_ReceiptOfGoods|Application_Module
	{
		return static::get( Admin_Managers_ReceiptOfGoods::class );
	}
	
	public static function TransferBetweenWarehouses() : Admin_Managers_WarehouseManagement_TransferBetweenWarehouses|Application_Module
	{
		return static::get( Admin_Managers_WarehouseManagement_TransferBetweenWarehouses::class );
	}
	
	public static function Complaint() : Admin_Managers_Complaint|Application_Module
	{
		return static::get( Admin_Managers_Complaint::class );
	}
	
	public static function MoneyRefund() : Admin_Managers_MoneyRefund|Application_Module|null
	{
		return static::get( Admin_Managers_MoneyRefund::class );
	}
	
	
	public static function ReturnOfGoods() : Admin_Managers_ReturnOfGoods|Application_Module
	{
		return static::get( Admin_Managers_ReturnOfGoods::class );
	}
	
	public static function Customer() : Admin_Managers_Customer|Application_Module
	{
		return static::get( Admin_Managers_Customer::class );
	}
	
	public static function Timer() : Admin_Managers_Timer|Application_Module|null
	{
		return static::get( Admin_Managers_Timer::class );
	}
	
	public static function WarehouseManagementOverview() : Admin_Managers_WarehouseManagement_Overview|Application_Module|null
	{
		return static::get( Admin_Managers_WarehouseManagement_Overview::class );
	}
	
	public static function Context() : Admin_Managers_Context|Application_Module
	{
		return static::get( Admin_Managers_Context::class );
	}
	
	public static function SupplierGoodsOrders() : Admin_Managers_SupplierGoodsOrders|Application_Module|null
	{
		return static::get( Admin_Managers_SupplierGoodsOrders::class );
	}
	
	public static function LossOrDestruction() : Admin_Managers_WarehouseManagement_LossOrDestruction|Application_Module|null
	{
		return static::get( Admin_Managers_WarehouseManagement_LossOrDestruction::class );
	}
	
	public static function Note() : Admin_Managers_Note|Application_Module|null
	{
		return static::get( Admin_Managers_Note::class );
	}
	
	public static function Signpost() : Admin_Managers_Signpost|Application_Module|null
	{
		return static::get( Admin_Managers_Signpost::class );
	}
}