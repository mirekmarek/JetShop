<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\GoogleSitemap;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'GoogleSitemapExport'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Products allowed',
	)]
	protected bool $products_allowed = false;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Categories allowed',
	)]
	protected bool $categories_allowed = false;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Signposts allowed',
	)]
	protected bool $signposts_allowed = false;
	
	
	#[Config_Definition(
		type: Config::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Brands allowed',
	)]
	protected bool $brands_allowed = false;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Marketing landing pages allowed',
	)]
	protected bool $marketing_landing_pages_allowed = false;
	
	
	#[Config_Definition(
		type: Config::TYPE_BOOL
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Articles allowed',
	)]
	protected bool $articles_allowed = false;
	
	public function getProductsAllowed(): bool
	{
		return $this->products_allowed;
	}
	
	public function setProductsAllowed( bool $products_allowed ): void
	{
		$this->products_allowed = $products_allowed;
	}
	
	public function getCategoriesAllowed(): bool
	{
		return $this->categories_allowed;
	}
	
	public function setCategoriesAllowed( bool $categories_allowed ): void
	{
		$this->categories_allowed = $categories_allowed;
	}
	
	public function getBrandsAllowed(): bool
	{
		return $this->brands_allowed;
	}
	
	public function setBrandsAllowed( bool $brands_allowed ): void
	{
		$this->brands_allowed = $brands_allowed;
	}
	
	public function getArticlesAllowed(): bool
	{
		return $this->articles_allowed;
	}
	
	public function setArticlesAllowed( bool $articles_allowed ): void
	{
		$this->articles_allowed = $articles_allowed;
	}
	
	public function getSignpostsAllowed(): bool
	{
		return $this->signposts_allowed;
	}
	
	public function setSignpostsAllowed( bool $signposts_allowed ): void
	{
		$this->signposts_allowed = $signposts_allowed;
	}
	
	
	
	public function getMarketingLandingPagesAllowed(): bool
	{
		return $this->marketing_landing_pages_allowed;
	}
	
	public function setMarketingLandingPagesAllowed( bool $marketing_landing_pages_allowed ): void
	{
		$this->marketing_landing_pages_allowed = $marketing_landing_pages_allowed;
	}
	
	

}