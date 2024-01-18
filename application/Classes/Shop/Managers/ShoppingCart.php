<?php
namespace JetApplication;

use Jet\MVC_Page_Interface;

interface Shop_Managers_ShoppingCart {
	public function getCart() : ShoppingCart;
	
	public function getCartPageId(): string;
	
	public function getCartPage(): MVC_Page_Interface;
	
	public function getCartPageURL(): string;
	
	public function saveCart() : void;
	
}