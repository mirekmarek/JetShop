<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\Application_Service_MetaInfo;
use JetShop\Core_Application_Service_EShop_ProductListing;

#[Application_Service_MetaInfo]
abstract class Application_Service_EShop_ProductListing extends Core_Application_Service_EShop_ProductListing
{
	/**
	 * @var Marketing_CategoryBanner[]
	 */
	protected array $listing_banners = [];

	public function setListingBanners( array $banners ) : void
	{
		$this->listing_banners = $banners;
	}
	
	/**
	 * @return Marketing_CategoryBanner[]
	 */
	public function getListingBanners() : array
	{
		return $this->listing_banners;
	}
	
	
}