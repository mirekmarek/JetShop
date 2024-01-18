<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Order_Status;
use JetApplication\Order_Status_Kind;

#[DataModel_Definition(
	name: 'order_statuses_shop_data',
	database_table_name: 'order_statuses_shop_data',
	parent_model_class: Order_Status::class
)]
abstract class Core_Order_Status_ShopData extends Entity_WithShopData_ShopData {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $kind = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $is_default = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:'
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Description:'
	)]
	protected string $description = '';
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_icon1 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_icon2 = '';
	
	public function getKindCode(): string
	{
		return $this->kind;
	}
	
	public function getKind() : ?Order_Status_Kind
	{
		return Order_Status_Kind::get( $this->kind );
	}
	
	public function getKindTitle() : string
	{
		$kind = $this->getKind();
		return $kind ? $kind->getTitle() : '';
	}
	
	public function setKind( string $kind ): void
	{
		$this->kind = $kind;
	}
	
	public function isDefault(): bool
	{
		return $this->is_default;
	}
	
	public function setIsDefault( bool $is_default ): void
	{
		$this->is_default = $is_default;
	}
	
	public function getName() : string
	{
		return $this->name;
	}
	
	public function getDescription() : string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	
	public function setImageIcon1( string $image ) : void
	{
		$this->image_icon1 = $image;
	}
	
	public function getImageIcon1() : string
	{
		return $this->image_icon1;
	}
	
	
	public function setImageIcon2( string $image ) : void
	{
		$this->image_icon2 = $image;
	}
	
	public function getImageIcon2() : string
	{
		return $this->image_icon2;
	}
	
}