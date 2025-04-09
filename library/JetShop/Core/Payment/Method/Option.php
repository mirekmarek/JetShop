<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel;

use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Common;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_Definition;

#[DataModel_Definition(
	name: 'payment_methods_options',
	database_table_name: 'payment_methods_options',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Payment method option',
	separate_tab_form_shop_data: false,
	images: [
		'icon1' => 'Icon 1',
		'icon2' => 'Icon 2',
		'icon3' => 'Icon 3',
	]
)]
abstract class Core_Payment_Method_Option extends EShopEntity_Common implements
	EShopEntity_Admin_Interface,
	EShopEntity_HasImages_Interface,
	EShopEntity_HasEShopRelation_Interface
{
	use EShopEntity_Admin_Trait;
	use EShopEntity_HasImages_Trait;
	use EShopEntity_HasEShopRelation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $method_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
	)]
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
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
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}
	
	
	
	public static function getListForMethod( int $method_id ) : array
	{
		$options = static::fetchInstances(['method_id'=>$method_id] );
		$options->getQuery()->setOrderBy(['priority']);
		
		$res = [];
		
		foreach($options as $opt) {
			$res[$opt->getInternalCode()] = $opt;
		}
		
		return $res;
	}
	
	public function getEditUrl( array $get_params=[] ) : string
	{
		return '';
	}
	
	
	public function getTitle() : string
	{
		return $this->title;
	}
	
	public function setTitle( string $title ) : void
	{
		$this->title = $title;
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
	
}