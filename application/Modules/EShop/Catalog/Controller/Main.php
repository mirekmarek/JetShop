<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Catalog;


use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Layout;


class Controller_Main extends MVC_Controller_Default
{

	use Controller_Main_Category;
	use Controller_Main_Product;
	use Controller_Main_Signpost;
	use Controller_Main_Brand;
	
	
	public function resolve(): bool|string
	{

		$main_router = MVC::getRouter();
		$path = $main_router->getUrlPath();
		
		if($this->getContent()->getControllerAction()=='brands') {
			return $this->getContent()->getControllerAction();
		}

		if(!$path) {
			return 'homepage';
		}
		
		$path = explode('/', $path);
		$path_base= explode('-', $path[0]);
		
		if(count($path_base)>1) {
			
			$i = count($path_base);
			
			$object_type = $path_base[$i-2];
			
			
			$object_id = $path_base[$i-1];
			$object_id = str_replace('.html', '', $object_id);
			if(str_contains($object_id, '_')) {
				$object_id = explode('_', $object_id);
				$object_id = $object_id[count($object_id)-1];
			}
			$object_id = (int)$object_id;
			
			if($object_id>0) {
				
				if($object_type=='c') {
					return $this->resolve_category( $object_id, $path );
				}
				
				if($object_type=='p') {
					return $this->resolve_product( $object_id, $path );
				}
				
				if($object_type=='t') {
					return $this->resolve_signpost( $object_id, $path );
				}
				
				if($object_type=='m') {
					return $this->resolve_brand( $object_id, $path );
				}
			}
		}
		
		return false;

	}
	
	public function homepage_Action() : void
	{
		MVC_Layout::getCurrentLayout()->setScriptName('homepage');
		
		$this->output('homepage');
	}

	public function b2b_menu_Action() : void
	{
		$this->view->setVar('category', static::$category);
		
		$this->output('b2b_menu');
	}
	
	public function brands_Action() : void
	{
		$this->output('brands');
	}
	
}
