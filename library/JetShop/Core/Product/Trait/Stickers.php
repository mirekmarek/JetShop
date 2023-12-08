<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Product_Sticker;
use JetApplication\Sticker;

trait Core_Product_Trait_Stickers
{
	/**
	 * @var Product_Sticker[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Product_Sticker::class,
	)]
	protected array $stickers = [];


	/**
	 * @var Sticker[]
	 */
	protected array|null $_stickers = null;

	/**
	 * @return Sticker[]
	 */
	public function getStickers() : array
	{
		if($this->_stickers===null) {
			$this->_stickers = [];
			foreach($this->stickers as $s) {
				/**
				 * @var Sticker $s
				 */
				$sticker = Sticker::get($s->getCode());
				if($sticker) {
					$this->_stickers[ $sticker->getCode() ] = $sticker;
				}
			}
		}

		return $this->_stickers;
	}

	public function hasSticker( string $sticker_code) : bool
	{
		return isset($this->stickers[$sticker_code]);
	}

	public function addSticker( string $sticker_code ) : bool
	{
		$sticker = Sticker::get( $sticker_code );
		if(!$sticker) {
			return false;
		}

		if(isset($this->stickers[$sticker->getCode()])) {
			return false;
		}

		$_sticker = new Product_Sticker();
		$_sticker->setProductId( $this->id );
		$_sticker->setStickerCode( $sticker->getCode() );

		$this->stickers[] = $_sticker;

		return true;
	}

	public function removeSticker( string $sticker_code ) : bool
	{

		if(!isset( $this->stickers[$sticker_code])) {
			return false;
		}

		unset( $this->stickers[$sticker_code]);
		if( $this->_stickers ) {
			unset( $this->_stickers[$sticker_code]);
		}

		return true;
	}


}