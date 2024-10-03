<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\SysConf_Path;
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
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Admin_Managers_Entity_Edit_WithShopData;
use JetApplication\Admin_Managers_Entity_Edit_Simple;
use JetApplication\Admin_Managers_Entity_Edit_Common;
use JetApplication\Admin_Managers_Entity_Edit_WithShopRelation;
use JetApplication\Admin_Managers_Entity_Edit_Marketing;
use JetApplication\Admin_Managers_ProductFilter;
use JetApplication\Admin_Managers_PriceFormatter;
use JetApplication\Admin_Managers_Order;
use JetApplication\Admin_Managers_Complaint;
use JetApplication\Admin_Managers_ReturnOfGoods;
use JetApplication\Admin_Managers_Customer;
use JetApplication\Admin_Managers_Timer;
use JetApplication\Admin_Managers_WarehouseManagementOverview;
use JetApplication\Admin_Managers_ReceiptOfGoods;
use JetApplication\Admin_Managers_TransferBetweenWarehouses;
use JetApplication\Admin_Managers_LossOrDestruction;


class Core_Admin_Managers extends Managers {
	
	/**
	 * @var Manager_MetaInfo[]|null
	 */
	protected static ?array $managers_meta_info = null;
	
	protected static ?array $config = null;
	
	protected static array $managers = [];
	
	public static function getCfgFilePath() : string
	{
		return SysConf_Path::getConfig().'shop/managers/admin.php';
	}
	
	
	protected static function registerManagers() : void
	{
		static::registerManager(
			interface_class_name: Admin_Managers_Category::class,
			is_mandatory: true,
			name: 'Catalog - Categories',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Brand::class,
			is_mandatory: true,
			name: 'Catalog - Brands',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_OrderDispatch::class,
			is_mandatory: true,
			name: 'Order dispatch',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Product::class,
			is_mandatory: true,
			name: 'Catalog - products',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Property::class,
			is_mandatory: true,
			name: 'Catalog - properties',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_PropertyGroup::class,
			is_mandatory: true,
			name: 'Catalog - property groups',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_KindOfProduct::class,
			is_mandatory: true,
			name: 'Catalog - Kind of products',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Image::class,
			is_mandatory: true,
			name: 'Images',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_UI::class,
			is_mandatory: true,
			name: 'UI',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_FulltextSearch::class,
			is_mandatory: true,
			name: 'Fulltext Search',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		
		static::registerManager(
			interface_class_name: Admin_Managers_Entity_Listing::class,
			is_mandatory: true,
			name: 'Entity Listing',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Entity_Edit_WithShopData::class,
			is_mandatory: true,
			name: 'Entity Edit With Shop Data',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Entity_Edit_Simple::class,
			is_mandatory: true,
			name: 'Entity Edit Simple',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Entity_Edit_Common::class,
			is_mandatory: true,
			name: 'Entity Edit Common',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Entity_Edit_WithShopRelation::class,
			is_mandatory: true,
			name: 'Entity Edit With Shop Relation',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Entity_Edit_Marketing::class,
			is_mandatory: true,
			name: 'Entity Edit Marketing',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_ProductFilter::class,
			is_mandatory: true,
			name: 'Product Filter',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_PriceFormatter::class,
			is_mandatory: true,
			name: 'Price Formatter',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Order::class,
			is_mandatory: true,
			name: 'Orders',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Invoice::class,
			is_mandatory: true,
			name: 'Invoices',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_InvoiceInAdvance::class,
			is_mandatory: true,
			name: 'Invoices in advance',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_DeliveryNote::class,
			is_mandatory: true,
			name: 'Delivery note',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		
		
		static::registerManager(
			interface_class_name: Admin_Managers_Complaint::class,
			is_mandatory: true,
			name: 'Complaints',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_ReturnOfGoods::class,
			is_mandatory: true,
			name: 'Return of goods',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Customer::class,
			is_mandatory: true,
			name: 'Customers',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Timer::class,
			is_mandatory: false,
			name: 'Timer',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_WarehouseManagementOverview::class,
			is_mandatory: false,
			name: 'Warehouse Management Overview',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_Context::class,
			is_mandatory: true,
			name: 'Context',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_ReceiptOfGoods::class,
			is_mandatory: true,
			name: 'Receipt of goods',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		static::registerManager(
			interface_class_name: Admin_Managers_TransferBetweenWarehouses::class,
			is_mandatory: true,
			name: 'Transfer between warehouses',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		
		static::registerManager(
			interface_class_name: Admin_Managers_SupplierGoodsOrders::class,
			is_mandatory: false,
			name: 'Supplier - Goods Orders',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		
		static::registerManager(
			interface_class_name: Admin_Managers_LossOrDestruction::class,
			is_mandatory: false,
			name: 'Warehouse - Loss or destruction',
			description: '',
			module_name_prefix: 'Admin.'
		);
		
		
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
	
	public static function UI() : Admin_Managers_UI|Application_Module
	{
		return static::get( Admin_Managers_UI::class );
	}
	
	public static function FulltextSearch() : Admin_Managers_FulltextSearch|Application_Module
	{
		return static::get( Admin_Managers_FulltextSearch::class );
	}
	
	public static function EntityListing() : Admin_Managers_Entity_Listing|Application_Module
	{
		return static::get( Admin_Managers_Entity_Listing::class );
	}
	
	public static function EntityEdit_WithShopData() : Admin_Managers_Entity_Edit_WithShopData|Application_Module
	{
		return static::get( Admin_Managers_Entity_Edit_WithShopData::class );
	}
	
	public static function EntityEdit_Simple() : Admin_Managers_Entity_Edit_Simple|Application_Module
	{
		return static::get( Admin_Managers_Entity_Edit_Simple::class );
	}
	
	
	public static function EntityEdit_Common() : Admin_Managers_Entity_Edit_Common|Application_Module
	{
		return static::get( Admin_Managers_Entity_Edit_Common::class );
	}
	
	public static function EntityEdit_WithShopRelation() : Admin_Managers_Entity_Edit_WithShopRelation|Application_Module
	{
		return static::get( Admin_Managers_Entity_Edit_WithShopRelation::class );
	}
	
	public static function EntityEdit_Marketing() : Admin_Managers_Entity_Edit_Marketing|Application_Module
	{
		return static::get( Admin_Managers_Entity_Edit_Marketing::class );
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
	
	public static function ReceiptOfGoods() : Admin_Managers_ReceiptOfGoods|Application_Module
	{
		return static::get( Admin_Managers_ReceiptOfGoods::class );
	}
	
	public static function TransferBetweenWarehouses() : Admin_Managers_TransferBetweenWarehouses|Application_Module
	{
		return static::get( Admin_Managers_TransferBetweenWarehouses::class );
	}
	
	
	public static function Complaint() : Admin_Managers_Complaint|Application_Module
	{
		return static::get( Admin_Managers_Complaint::class );
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
	
	public static function WarehouseManagementOverview() : Admin_Managers_WarehouseManagementOverview|Application_Module|null
	{
		return static::get( Core_Admin_Managers_WarehouseManagementOverview::class );
	}
	
	public static function Context() : Admin_Managers_Context|Application_Module
	{
		return static::get( Admin_Managers_Context::class );
	}
	
	public static function SupplierGoodsOrders() : Admin_Managers_SupplierGoodsOrders|Application_Module|null
	{
		return static::get( Admin_Managers_SupplierGoodsOrders::class );
	}
	
	public static function LossOrDestruction() : Admin_Managers_LossOrDestruction|Application_Module|null
	{
		return static::get( Admin_Managers_LossOrDestruction::class );
	}
	
}