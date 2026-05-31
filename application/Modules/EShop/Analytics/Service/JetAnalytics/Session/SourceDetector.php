<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */


namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

class Session_SourceDetector
{
	public const PAID_SEARCH = 'Paid Search';
	public const PAID_SOCIAL = 'Paid Social';
	public const EMAIL = 'Email';
	public const REFERRAL = 'Referral';
	public const OTHER_CAMPAIGN = 'Other campaign';
	public const DIRECT = 'Direct';
	public const INTERNAL = 'Internal';
	public const ORGANIC_SEARCH = 'Organic Search';
	public const ORGANIC_SOCIAL = 'Organic Social';
	
	public static array $search_services = [
						'google.',
						'seznam.cz',
						'bing.com',
						'yahoo.com',
						'duckduckgo.com',
						'ecosia.org'
					];
	
	public static array $social_networks = [
						'facebook.com',
						'instagram.com',
						't.co',
						'twitter.com',
						'linkedin.com',
						'pinterest.com',
						'tiktok.com'
					];
	
	public static function getSources() : array
	{
		return [
			static::PAID_SEARCH,
			static::PAID_SOCIAL,
			static::EMAIL,
			static::REFERRAL,
			static::OTHER_CAMPAIGN,
			static::DIRECT,
			static::INTERNAL,
			static::ORGANIC_SEARCH,
			static::ORGANIC_SOCIAL,
		];
	}
	
	public static function detect( Session $session ) : string
	{

		$utm_medium = $session->getUtmMedium();
		$utm_source = $session->getUtmSource();
		
		if( $utm_medium ) {
			if( in_array( $utm_medium, [
				'cpc',
				'ppc',
				'paidsearch'
			] ) ) {
				return static::PAID_SEARCH;
			}
			
			if( in_array( $utm_medium, [
				'social',
				'social-network',
				'social-media'
			] ) ) {
				return static::PAID_SOCIAL;
			}
			
			if( $utm_medium === 'email' ) {
				return static::EMAIL;
			}
			
			return static::OTHER_CAMPAIGN;
		}
		
		if( $session->getGclid() ) {
			return static::PAID_SEARCH;
		}
		
		
		$referer_host = $session->getRefererDomain();
		if( !$referer_host ) {
			return static::DIRECT;
		}
		
		
		if( str_contains( $referer_host, parse_url( $session->getEshop()->getHomepage()->getURL() )['host'] ) ) {
			return static::INTERNAL;
		}
		
		foreach( static::$search_services as $search_service ) {
			if( str_contains( $referer_host, $search_service ) ) {
				return static::ORGANIC_SEARCH;
			}
		}
		
		foreach( static::$social_networks as $social_network ) {
			if( str_contains( $referer_host, $social_network ) ) {
				return static::ORGANIC_SOCIAL;
			}
		}
		
		return static::REFERRAL;
		
	}
}

