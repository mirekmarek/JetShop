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
						'google.com',
						'android.gm',
						'android.googlequicksearchbox',
						'seznam.cz',
						'bing.com',
						'yahoo.com',
						'duckduckgo.com',
						'ecosia.org',
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
	
	
	public static array $utm_medium_aliases = [
						'fb' => 'facebook'
					];
	
	
	public static array $utm_medium_paid_search = [
						'cpc',
						'ppc',
						'paidsearch'
					];
	
	public static array $utm_sources_paid_search = [
						'seznam',
						'google'
					];
	
	public static array $utm_medium_paid_social = [
						'facebook',
					];
	
	
	
	protected Session $session;
	
	protected string $source = '';
	protected string $sub_source_1 = '';
	protected string $sub_source_2 = '';
	
	
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
	
	public function __construct( Session $session )
	{
		$this->session = $session;
		$this->detect();
	}
	
	
	
	public function detect() : void
	{
		if( $this->session->getGclid() ) {
			$this->source = static::PAID_SEARCH;
			$this->sub_source_1 = 'google';
			
			return;
		}

		$utm_medium = $this->session->getUtmMedium();
		$utm_source = $this->session->getUtmSource();
		
		if(isset(static::$utm_medium_aliases[$utm_medium])) {
			$utm_medium = static::$utm_medium_aliases[$utm_medium];
		}
		
		
		$cutDomain = function( string $domain ) : string  {
			$domain = explode('.', $domain);
			if( count($domain)>2 ) {
				$cnt = count($domain);
				
				$domain = $domain[$cnt-2].'.'.$domain[$cnt-1];
			} else {
				$domain = implode('.', $domain);
			}
			
			return $domain;
		};
		
		$referer_host = $cutDomain( $this->session->getRefererDomain() );
		$eshop_host = $cutDomain( parse_url( $this->session->getEshop()->getHomepage()->getURL() )['host'] );
		
		if( $utm_medium ) {
			
			if(
				in_array(
					$utm_medium,
					static::$utm_medium_paid_search
				) &&
				in_array(
					$utm_source,
					static::$utm_sources_paid_search
				)
			) {
				$this->source = static::PAID_SEARCH;
				$this->sub_source_1 = $utm_source;
				return;
			}
			
			
			
			if( in_array( $utm_medium, static::$utm_medium_paid_social ) ) {
				$this->source = static::PAID_SOCIAL;
				$this->sub_source_1 = $utm_medium;
				return;
			}
			
			if(
				$utm_medium === 'email' ||
				str_contains( $this->session->getRefererDomain(), 'mail' )
			) {
				$this->source = static::EMAIL;
				$this->sub_source_1 = $this->session->getRefererDomain();
				
				return;
			}
			
			$this->source = static::OTHER_CAMPAIGN;
			$this->sub_source_1 = $referer_host ? : $this->session->getUtmSource();
			
			return;
		}
		
		
		
		if( !$referer_host ) {
			$this->source = static::DIRECT;
			return;
		}
		
		if( $referer_host ==  $eshop_host ) {
			$this->source = static::INTERNAL;
			
			return;
		}
		
		foreach( static::$search_services as $search_service ) {
			if( str_contains( $referer_host, $search_service ) ) {
				$this->source = static::ORGANIC_SEARCH;
				$this->sub_source_1 = $referer_host;
				
				return;
			}
		}
		
		foreach( static::$social_networks as $social_network ) {
			if( str_contains( $referer_host, $social_network ) ) {
				$this->source = static::ORGANIC_SOCIAL;
				$this->sub_source_1 = $referer_host;
				return;
			}
		}
		
		$this->source = static::REFERRAL;
		$this->sub_source_1 = $referer_host;
	}
	
	public function getSource(): string
	{
		return $this->source;
	}
	
	public function getSubSource1(): string
	{
		return $this->sub_source_1;
	}
	
	public function getSubSource2(): string
	{
		return $this->sub_source_2;
	}
	
	
}

