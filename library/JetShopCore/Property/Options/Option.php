<?php
namespace JetShop;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\DataModel;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;

#[DataModel_Definition(
	name: 'properties_options',
	database_table_name: 'properties_options',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	default_order_by: ['priority'],
	parent_model_class: Property::class
)]
abstract class Core_Property_Options_Option extends DataModel_Related_1toN
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		related_to: 'main.id'
	)]
	protected int $property_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name:'
	)]
	protected string $internal_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Internal notes:'
	)]
	protected string $internal_notes = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active'
	)]
	protected bool $is_active = true;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;
	
	protected Property_Options_Option_Filter $filter;
	
	/**
	 * @var Property_Options_Option_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Property_Options_Option_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];
	
	protected bool $is_first = false;
	
	protected bool $is_last = false;
	
	protected Form|null $_add_form = null;
	
	protected Form|null $_edit_form = null;
	
	public function __construct()
	{
		
		parent::__construct();
		
		$this->afterLoad();
	}
	
	public function afterLoad(): void
	{
		Property_Options_Option_ShopData::checkShopData( $this, $this->shop_data );
	}
	
	public function setPropertyId( int $property_id ): void
	{
		$this->property_id = $property_id;
	}
	
	public function getPropertyId(): int
	{
		return $this->property_id;
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function setId( int $id ): void
	{
		$this->id = $id;
	}
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	public function getInternalName(): string
	{
		return $this->internal_name;
	}
	
	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}
	
	public function getInternalNotes(): string
	{
		return $this->internal_notes;
	}
	
	public function setInternalNotes( string $internal_notes ): void
	{
		$this->internal_notes = $internal_notes;
	}
	
	
	public function setIsActive( bool $is_active ): void
	{
		$this->is_active = $is_active;
	}
	
	public function getIsActive(): bool
	{
		return $this->is_active;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	
	public function getShopData( ?Shops_Shop $shop = null ): Property_Options_Option_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}
	
	
	public function isFirst(): bool
	{
		return $this->is_first;
	}
	
	public function setIsFirst( bool $is_first ): void
	{
		$this->is_first = $is_first;
	}
	
	public function isLast(): bool
	{
		return $this->is_last;
	}
	
	public function setIsLast( bool $is_last ): void
	{
		$this->is_last = $is_last;
	}
	
	public function getAddForm(): Form
	{
		if( !$this->_add_form ) {
			$form = $this->createForm( 'option_add_form' );
			
			$this->_add_form = $form;
		}
		
		return $this->_add_form;
	}
	
	public function catchAddForm(): bool
	{
		return $this->getAddForm()->catch();
	}
	
	public function getEditForm(): Form
	{
		if( !$this->_edit_form ) {
			$form = $this->createForm( 'option_edit_form_' . $this->id );
			
			$this->_edit_form = $form;
		}
		
		return $this->_edit_form;
	}
	
	public function catchEditForm(): bool
	{
		return $this->getEditForm()->catch();
	}
	
	public function getFilterLabel( ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getFilterLabel();
	}
	
	public function getProductDetailLabel( ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getProductDetailLabel();
	}
	
	public function getUrlParam( ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getUrlParam();
	}
	
	public function getDescription( ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getDescription();
	}
	
	
	public function getImageMain( ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getImageMain();
	}
	
	public function getImageMainUrl( ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getImageMainUrl();
	}
	
	public function getImageMainThumbnailUrl( int $max_w, int $max_h, ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getImageMainThumbnailUrl( $max_w, $max_h );
	}
	
	public function getImagePictogram( ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getImagePictogram();
	}
	
	public function getImagePictogramUrl( ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getImagePictogramUrl();
	}
	
	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h, ?Shops_Shop $shop = null ): string
	{
		return $this->getShopData( $shop )->getImagePictogramThumbnailUrl( $max_w, $max_h );
	}
	
	public function initFilter( ProductListing $listing, Property_Options $property ): void
	{
		$this->filter = new Property_Options_Option_Filter( $listing, $property, $this );
	}
	
	public function filter() : Property_Options_Option_Filter
	{
		return $this->filter;
	}
}