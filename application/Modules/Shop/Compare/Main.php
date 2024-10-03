<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Compare;

use Jet\Application_Module;
use Jet\MVC_Page_Interface;
use Jet\Session;
use JetApplication\Product_ShopData;
use JetApplication\Shop_Managers_Compare;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;
use JetApplication\Shop_Pages;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_Compare, Shop_ModuleUsingTemplate_Interface
{
	use Shop_ModuleUsingTemplate_Trait;
	
	protected ?Session $session = null;
	protected MVC_Page_Interface|null|bool $page = null;
	
	protected function getSession() : Session
	{
		if(!$this->session) {
			$this->session = new Session('compare');
		}
		
		return $this->session;
	}
	
	public function selectProduct( int $id ) : void
	{
		$session = $this->getSession();
		$products = $session->getValue('products', []);
		if(!is_array($products)) {
			$products = [];
		}
		if(!in_array($id, $products)) {
			$products[] = $id;
		}
		$session->setValue('products', $products);
	}
	
	public function unselectProduct( int $id ) : void
	{
		$session = $this->getSession();
		$products = $session->getValue('products', []);
		if(!is_array($products)) {
			$products = [];
		}
		
		$_products = [];
		foreach($products as $p_id) {
			if($p_id!=$id) {
				$_products[] = $p_id;
			}
		}
		
		$session->setValue('products', $_products);
	}
	
	public function getProductIds() : array
	{
		$session = $this->getSession();
		$products = $session->getValue('products', []);
		if(!is_array($products)) {
			return [];
		}
		
		return $products;
	}
	
	public function getProductIsSelected( int $product_id ) : bool
	{
		return in_array( $product_id, $this->getProductIds() );
	}
	
	
	public function getPage() : ?MVC_Page_Interface
	{
		if($this->page===null) {
			$this->page = Shop_Pages::Compare();
		}
		
		return $this->page?:null;
	}
	
	
	public function renderIntegration(): string
	{
		$page = $this->getPage();
		if(!$page || !$page->getIsActive()) {
			return '';
		}
		
		$view = $this->getView();
		$view->setVar('page', $page);
		
		return $view->render('integration');
	}
	
	public function renderProductButton( Product_ShopData $product, bool $container=true ): string
	{
		$page = $this->getPage();
		if(!$page || !$page->getIsActive()) {
			return '';
		}
		
		$view = $this->getView();
		$view->setVar('id', $product->getId());
		$view->setVar('container', $container);
		
		if($this->getProductIsSelected( $product->getId() )) {
			$res = $view->render('button/selected');
		} else {
			$res = $view->render('button/select');
		}
		
		return $res;
	}
	
	
	public function renderIcon(): string
	{
		$page = $this->getPage();
		if(!$page || !$page->getIsActive()) {
			return '';
		}
		
		$view = $this->getView();
		$view->setVar('page', $page);
		$view->setVar('product_ids', $this->getProductIds());
		
		return $view->render('icon');
	}
	
}