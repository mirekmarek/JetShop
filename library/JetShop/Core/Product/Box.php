<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_Basic;

#[DataModel_Definition(
	name: 'product_boxes',
	database_table_name: 'product_boxes',
)]
abstract class Core_Product_Box extends Entity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'EAN:',
	)]
	protected string $EAN = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Width:',
	)]
	protected float $width = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Height:',
	)]
	protected float $height = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Length:',
	)]
	protected float $length = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_FLOAT,
		label: 'Weight:',
	)]
	protected float $weight = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $volume = 0.0;
	
	
	/**
	 * @param int $product_id
	 * @return static[]
	 */
	public static function getBoxes( int $product_id ) : array
	{
		$_boxes = static::fetch(
			where_per_model: ['product_boxes'=>[
				'product_id' => $product_id
			]]
		);
		
		$boxes = [];
		
		foreach($_boxes as $img) {
			$boxes[$img->getId()] = $img;
		}
		
		return $boxes;
	}
	
	public function setProductId( int $id ) : void
	{
		$this->product_id = $id;
	}
	
	public function getProductId() : int
	{
		return $this->product_id;
	}

	protected function calcVolume() : void
	{
		$this->volume = $this->width * $this->height * $this->length;
	}

	public function getWidth(): float
	{
		return $this->width;
	}
	
	public function setWidth( float $width ): void
	{
		$this->width = $width;
		$this->calcVolume();
	}
	
	public function getHeight(): float
	{
		return $this->height;
	}
	
	public function setHeight( float $height ): void
	{
		$this->height = $height;
		$this->calcVolume();
	}
	
	public function getLength(): float
	{
		return $this->length;
	}
	
	public function setLength( float $length ): void
	{
		$this->length = $length;
		$this->calcVolume();
	}
	
	public function getWeight(): float
	{
		return $this->weight;
	}
	
	public function setWeight( float $weight ): void
	{
		$this->weight = $weight;
	}
	
	public function getVolume(): float
	{
		return $this->volume;
	}
	
	public function getEAN(): string
	{
		return $this->EAN;
	}
	
	public function setEAN( string $EAN ): void
	{
		$this->EAN = $EAN;
	}
	
	
}
