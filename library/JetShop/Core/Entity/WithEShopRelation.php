<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Locale;
use JetApplication\Entity_Basic;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition]
abstract class Core_Entity_WithEShopRelation extends Entity_Basic
{
	protected ?EShop $_eshop = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $eshop_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
	)]
	protected ?Locale $locale = null;
	
	public function setEshop( EShop $eshop ) : void
	{
		$this->eshop_code = $eshop->getCode();
		$this->locale = $eshop->getLocale();
		$this->_eshop = $eshop;
	}
	
	public function getEshopCode() : string
	{
		return $this->eshop_code;
	}
	
	public function getLocale(): ?Locale
	{
		return $this->locale;
	}
	
	public function getEshop() : EShop
	{
		if(!$this->_eshop) {
			$this->_eshop = EShops::get( $this->getEshopKey() );
		}
		
		return $this->_eshop;
	}
	
	public function getEshopKey() : string
	{
		return $this->eshop_code.'_'.$this->locale;
	}
}