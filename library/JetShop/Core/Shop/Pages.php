<?php

namespace JetShop;

use Jet\MVC;
use Jet\MVC_Page_Interface;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

class Core_Shop_Pages
{
	
	protected static string $cash_desk_page_id = 'cash-desk';
	protected static string $cash_desk_confirmation_page_id = 'cash-desk-confirmation';
	protected static string $cash_desk_payment_page_id = 'cash-desk-payment';
	
	protected static string $change_password_page_id = 'change-password';
	protected static string $password_reset_page_id = 'password-reset';
	protected static string $sign_up_page_id = 'sign-up';
	protected static string $login_page_id = 'login';
	protected static string $customer_section_page_id = 'customer-section';
	
	protected static string $shopping_cart_page_id = 'shopping-cart';
	
	protected static string $search_page_id = 'search';
	protected static string $search_whisper_page_id = 'search-whisper';
	
	protected static string $terms_and_conditions_page_id = 'terms-and-conditions';
	
	protected static string $compare_page_id = 'compare';
	protected static string $wishlist_page_id = 'wishlist';
	
	protected static string $complaints_page_id = 'complaints';
	protected static string $return_of_goods_page_id = 'return-of-goods';
	
	protected static string $oauth = 'o-auth';
	
	public static function getIds() : array
	{
		return [
			MVC::HOMEPAGE_ID,
			
			static::$cash_desk_page_id,
			static::$cash_desk_confirmation_page_id,
			static::$cash_desk_payment_page_id,
			
			static::$change_password_page_id,
			static::$password_reset_page_id,
			static::$sign_up_page_id,
			static::$login_page_id,
			static::$customer_section_page_id,
			
			static::$shopping_cart_page_id,
			
			static::$search_page_id,
			static::$search_whisper_page_id,
			
			static::$terms_and_conditions_page_id,
			
			static::$compare_page_id,
			static::$wishlist_page_id,
			
			static::$complaints_page_id,
			static::$return_of_goods_page_id,
			
			static::$oauth,
		];
	}
	
	
	public static function ChangePassword( ?Shops_Shop $shop = null ): ?MVC_Page_Interface
	{
		return static::getPage( static::$change_password_page_id, $shop );
	}
	
	public static function ResetPassword( ?Shops_Shop $shop = null ): ?MVC_Page_Interface
	{
		return static::getPage( static::$password_reset_page_id, $shop );
	}
	
	public static function SignUp( ?Shops_Shop $shop = null ): ?MVC_Page_Interface
	{
		return static::getPage( static::$sign_up_page_id, $shop );
	}
	
	public static function Login( ?Shops_Shop $shop = null ): ?MVC_Page_Interface
	{
		return static::getPage( static::$login_page_id, $shop );
	}
	
	public static function CustomerSection( ?Shops_Shop $shop = null ): MVC_Page_Interface
	{
		return static::getPage( static::$customer_section_page_id, $shop );
	}
	
	
	public static function CashDesk( ?Shops_Shop $shop = null ): MVC_Page_Interface
	{
		return static::getPage( static::$cash_desk_page_id, $shop );
	}
	
	public static function CashDeskConfirmation( ?Shops_Shop $shop = null ): MVC_Page_Interface
	{
		return static::getPage( static::$cash_desk_confirmation_page_id, $shop );
	}
	
	public static function CashDeskPayment( ?Shops_Shop $shop = null ): MVC_Page_Interface
	{
		return static::getPage( static::$cash_desk_payment_page_id, $shop );
	}
	
	public static function ShoppingCart( ?Shops_Shop $shop = null ): MVC_Page_Interface
	{
		return static::getPage( static::$shopping_cart_page_id, $shop );
	}
	
	public static function Search( ?Shops_Shop $shop = null ): MVC_Page_Interface
	{
		return static::getPage( static::$search_page_id, $shop );
	}
	
	public static function SearchWhisperer( ?Shops_Shop $shop = null ): MVC_Page_Interface
	{
		return static::getPage( static::$search_whisper_page_id, $shop );
	}
	
	public static function TermsAndConditions( ?Shops_Shop $shop = null ): MVC_Page_Interface
	{
		return static::getPage( static::$terms_and_conditions_page_id, $shop );
	}
	
	public static function Compare( ?Shops_Shop $shop = null ): ?MVC_Page_Interface
	{
		return static::getPage( static::$compare_page_id, $shop );
	}
	
	public static function Wishlist( ?Shops_Shop $shop = null ): ?MVC_Page_Interface
	{
		return static::getPage( static::$wishlist_page_id, $shop );
	}
	
	
	public static function Complaints( ?Shops_Shop $shop = null ): ?MVC_Page_Interface
	{
		return static::getPage( static::$complaints_page_id, $shop );
	}
	
	public static function ReturnOfGoods( ?Shops_Shop $shop = null ): ?MVC_Page_Interface
	{
		return static::getPage( static::$return_of_goods_page_id, $shop );
	}
	
	public static function OAuth( ?Shops_Shop $shop = null ): ?MVC_Page_Interface
	{
		return static::getPage( static::$oauth, $shop );
	}
	
	public static function getPage( string $id, ?Shops_Shop $shop=null ): ?MVC_Page_Interface
	{
		if( !$shop ) {
			$shop = Shops::getCurrent();
		}
		
		return MVC::getPage( $id, $shop->getLocale(), $shop->getBaseId() );
	}
	
}