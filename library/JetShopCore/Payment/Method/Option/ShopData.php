<?php
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Tr;

/**
 *
 */
#[DataModel_Definition(
	name: 'payment_methods_options_shop_data',
	database_table_name: 'payment_methods_options_shop_data',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Payment_Method_Option::class
)]
abstract class Core_Payment_Method_Option_ShopData extends DataModel_Related_1toN implements Images_ShopDataInterface, CommonEntity_ShopDataInterface {

	const IMG_ICON1 = 'icon1';
	const IMG_ICON2 = 'icon2';
	const IMG_ICON3 = 'icon3';
	
	use CommonEntity_ShopDataTrait;
	use Images_ShopDataTrait;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_id: true,
		related_to: 'main.code',
		form_field_type: false
	)]
	protected string $payment_method_code = '';

	protected ?Payment_Method $payment_method = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_id: true,
		related_to: 'parent.code',
		form_field_type: false
	)]
	protected string $option_code = '';

	protected ?Payment_Method_Option $option = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Filter label:',
		max_len: 255
	)]
	protected string $title = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Description:',
		max_len: 65536
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $priority = 0;


	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_icon1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_icon2 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_icon3 = '';

	public function setParents( Payment_Method $payment_method, Payment_Method_Option $option ) : void
	{
		$this->payment_method = $payment_method;
		$this->payment_method_code = $payment_method->getCode();

		$this->option = $option;
		$this->option_code = $option->getCode();

	}

	public function getArrayKeyValue() : string
	{
		return $this->shop_code;
	}
	
	public function getShopCode() : string
	{
		return $this->shop_code;
	}

	public function setShopCode( string $shop_code ) : void
	{
		$this->shop_code = $shop_code;
	}

	public function getPaymentMethodCode() : string
	{
		return $this->payment_method_code;
	}

	public function getPaymentMethod() : Payment_Method
	{
		return $this->payment_method;
	}

	public function getOptionCode() : int
	{
		return $this->option_code;
	}

	public function getOption() : Payment_Method_Option
	{
		return $this->option;
	}

	public function getTitle() : string
	{
		return $this->title;
	}

	public function setTitle( string $title ) : void
	{
		$this->title = $title;
	}

	public function getPriority(): int
	{
		return $this->priority;
	}

	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}


	public function getDescription() : string
	{
		return $this->description;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getImageEntity() : string
	{
		return 'payment_method_option';
	}

	public function getImageObjectId() : int|string
	{
		return $this->payment_method_code;
	}

	public static function getImageClasses() : array
	{
		return [
			Payment_Method_Option_ShopData::IMG_ICON1 => Tr::_('Icon 1', [], Payment_Method::getManageModuleName() ),
			Payment_Method_Option_ShopData::IMG_ICON2 => Tr::_('Icon 2', [], Payment_Method::getManageModuleName() ),
			Payment_Method_Option_ShopData::IMG_ICON3 => Tr::_('Icon 3', [], Payment_Method::getManageModuleName() ),
		];
	}

	public function setIcon1( string $image ) : void
	{
		$this->setImage( Payment_Method_ShopData::IMG_ICON1, $image );
	}

	public function getIcon1() : string
	{
		return $this->getImage( Payment_Method_ShopData::IMG_ICON1 );
	}

	public function getIcon1Url() : string
	{
		return $this->getImageUrl( Payment_Method_ShopData::IMG_ICON1 );
	}

	public function getIcon1ThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Payment_Method_ShopData::IMG_ICON1, $max_w, $max_h );
	}



	public function setIcon2( string $image ) : void
	{
		$this->setImage( Payment_Method_ShopData::IMG_ICON2, $image );
	}

	public function getIcon2() : string
	{
		return $this->getImage( Payment_Method_ShopData::IMG_ICON2 );
	}

	public function getIcon2Url() : string
	{
		return $this->getImageUrl( Payment_Method_ShopData::IMG_ICON2 );
	}

	public function getIcon2ThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Payment_Method_ShopData::IMG_ICON2, $max_w, $max_h );
	}


	public function setIcon3( string $image ) : void
	{
		$this->setImage( Payment_Method_ShopData::IMG_ICON3, $image );
	}

	public function getIcon3() : string
	{
		return $this->getImage( Payment_Method_ShopData::IMG_ICON3 );
	}

	public function getIcon3Url() : string
	{
		return $this->getImageUrl( Payment_Method_ShopData::IMG_ICON3 );
	}

	public function getIcon3ThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Payment_Method_ShopData::IMG_ICON3, $max_w, $max_h );
	}


	public function getPossibleToEditImages() : bool
	{
		if($this->option->getEditForm()->getIsReadonly()) {
			return false;
		}

		return true;
	}
}