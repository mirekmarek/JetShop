<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Payment_Method_Option;

#[DataModel_Definition(
	name: 'payment_methods_options_shop_data',
	database_table_name: 'payment_methods_options_shop_data',
	parent_model_class: Payment_Method_Option::class
)]
abstract class Core_Payment_Method_Option_ShopData extends Entity_WithShopData_ShopData {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $method_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
	)]
	protected string $title = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description:',
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;


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

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_icon3 = '';
	
	public function getMethodId(): int
	{
		return $this->method_id;
	}
	
	public function setMethodId( int $method_id ): void
	{
		$this->method_id = $method_id;
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
	
	/**
	 * @return string
	 */
	public function getImageIcon1(): string
	{
		return $this->image_icon1;
	}
	
	public function setImageIcon1( string $image_icon1 ): void
	{
		$this->image_icon1 = $image_icon1;
	}
	
	public function getIcon1ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl( 'icon1', $max_w, $max_h );
	}
	
	
	public function getImageIcon2(): string
	{
		return $this->image_icon2;
	}
	
	public function setImageIcon2( string $image_icon2 ): void
	{
		$this->image_icon2 = $image_icon2;
	}
	
	public function getIcon2ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl( 'icon2', $max_w, $max_h );
	}
	
	
	public function getImageIcon3(): string
	{
		return $this->image_icon3;
	}
	
	public function setImageIcon3( string $image_icon3 ): void
	{
		$this->image_icon3 = $image_icon3;
	}
	
	public function getIcon3ThumbnailUrl( int $max_w, int $max_h ): string
	{
		return $this->getImageThumbnailUrl( 'icon3', $max_w, $max_h );
	}
	
	
	public static function getListForMethod( int $method_id ) : array
	{
		return static::fetch(
			where_per_model: ['payment_methods_options_shop_data'=>['method_id'=>$method_id]],
			item_key_generator: function( Core_Payment_Method_Option_ShopData $item ) : int
			{
				return $item->getId();
			}
		);
	}
	
}