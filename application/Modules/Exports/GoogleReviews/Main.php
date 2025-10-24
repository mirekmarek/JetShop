<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\GoogleReviews;


use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\EShops;
use JetApplication\Exports_Definition;
use JetApplication\Exports_Generator_XML;
use JetApplication\Exports_Module;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShop;
use JetApplication\Product_EShopData;
use JetApplication\ProductReview;


class Main extends Exports_Module implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected ?array $export_categories = null;

	public function getTitle(): string
	{
		return 'Google Reviews';
	}

	public function isAllowedForShop( EShop $eshop ): bool
	{
		return false;
	}

	public function actualizeCategories( EShop $eshop ) : void
	{
	}
	
	
	public function actualizeCategory( EShop $eshop, string $category_id ): void
	{
	}
	
	
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_EXPORTS;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'Google Reviews export';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'file-export';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
	public function getExportsDefinitions(): array
	{
		$allowed_eshops = [];
		foreach(EShops::getList() as $eshop) {
			$config = $this->getEshopConfig( $eshop );
			if($config->getSourceEShops()) {
				$allowed_eshops[] = $eshop;
			}
		}
		
		$reviews = new Exports_Definition(
			module: $this,
			name: Tr::_('Google Reviews'),
			description: '',
			export_code: 'products',
			export: function() {
				$this->export( EShops::getCurrent() );
			}
		);
		
		$reviews->setAllowedEshops( $allowed_eshops );

		return [
			$reviews,
		];
	}
	
	protected function export( EShop $eshop ): void
	{
		/**
		 * @var Config_PerShop $config
		 */
		$config = $this->getEshopConfig( $eshop );
		$eshops = $config->getSourceEShops( true );
		if(!$eshops) {
			return;
		}
		
		$eshops_where = [];
		foreach( $eshops as $_eshop ) {
			if($eshops_where) {
				$eshops_where[] = 'OR';
			}
			/**
			 * @var EShop $_eshop
			 */
			$eshops_where[] = $_eshop->getWhere();
		}
		
		$where = [
			'approved' => true,
			'AND',
			$eshops_where
		];
		
		$reviews = ProductReview::fetchInstances( $where );
		
		$f = new Exports_Generator_XML( $this->getCode(),  $eshop );
		$f->start();
		
		$f->tagStart('feed', [
			'xmlns:vc' => "http://www.w3.org/2007/XMLSchema-versioning",
			'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
			'xsi:noNamespaceSchemaLocation' => "http://www.google.com/shopping/reviews/schema/product/2.2/product_reviews.xsd"
		]);
		
		
		$f->tagPair('version', '2.2');
		$f->tagStart('aggregator');
		$f->tagPair('name', $config->getAgregatorName() );
		$f->tagEnd('aggregator');
		
		$f->tagStart('publisher');
		$f->tagPair('name',  $config->getPublishedName() );
		$f->tagPair('favicon',  $config->getPublishedFavicon() );
		$f->tagEnd('publisher' );
		
		
		
		$f->tagStart('reviews');
		
		foreach( $reviews as $r ) {
			$product = Product_EShopData::get($r->getProductId(), $r->getEshop());
			if(!$product?->isActive()) {
				continue;
			}
			
			if(!$r->getSummary()) {
				continue;
			}
			
			$f->tagStart('review');
			
			
			$f->tagPair('review_id', $r->getId());
			
			$f->tagStart('reviewer');
			$f->tagPair('name', $r->getAuthorName());
			$f->tagEnd('reviewer');
			
			$f->tagPair('review_timestamp', $r->getCreated()->toString() );
			$f->tagPair('content', $r->getSummary());
			
			
			
			$f->tagPair('review_url', $product->getUrl()."?review=".$r->getId(), attributes: ['type'=>'group']);
			
			if($r->getNegativeCharacteristics()) {
				$pros = explode("\n", $r->getNegativeCharacteristics());
				
				$f->tagStart('pros');
				foreach($pros as $pro) {
					if($pro) {
						$f->tagPair('pro', $pro);
					}
				}
				$f->tagEnd('pros');
			}
			if($r->getPositiveCharacteristics()) {
				$cons = explode("\n", $r->getPositiveCharacteristics());
				
				$f->tagStart('cons');
				foreach($cons as $con) {
					if($con) {
						$f->tagPair('con', $con);
					}
				}
				$f->tagEnd('cons');
			}
			
			
			
			$f->tagStart('ratings');
			
			$f->tagPair('overall', $r->getRank(), attributes: ['min'=>0, 'max'=>100]);
			$f->tagEnd('ratings');
			
			
			$f->tagStart('products');
			
			$f->tagStart('product');
			$f->tagPair('product_url', $product->getUrl() );
			$f->tagEnd('product');
			
			$f->tagEnd('products');
			
			$f->tagEnd('review');
		}
		
		$f->tagEnd('reviews');
		
		$f->tagEnd('feed');
		
	}

}