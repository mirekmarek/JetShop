<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\MagicTags;

use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Float;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Form_Field_Int;
use Jet\Form_Field_Select;
use Jet\MVC;
use Jet\MVC_View;
use Jet\Tr;
use JetApplication\Content_MagicTag_Context;
use JetApplication\Application_Service_EShop;
use JetApplication\Application_Service_EShop_MagicTags;
use JetApplication\EShops;
use JetApplication\Content_MagicTag;

class Handler
{
	protected MVC_View $view;
	protected ?Application_Service_EShop_MagicTags $manager = null;
	
	/**
	 * @var Content_MagicTag[]
	 */
	protected array $list = [];
	/**
	 * @var Form[]
	 */
	protected array $forms = [];
	protected array $context_data = [];
	
	public function __construct( MVC_View $view )
	{
		$this->view = $view;
		
		$this->manager = Application_Service_EShop::MagicTags();
		if($this->manager) {
			$this->list = $this->manager->getList();
		}
	}
	
	public function handle() : string
	{
		if(!$this->manager) {
			return '';
		}
		
		foreach($this->list as $tag) {
			$this->generateForm( $tag );
		}
		
		foreach($this->list as $tag) {
			if($this->forms[$tag->getId()]->catch()) {
				$generated = $this->list[$tag->getId()]->generate(
					$this->context_data
				);
				
				AJAX::operationResponse(true, data: [
					'generated' => $generated
				]);
			}
		}
		
		
		
		$this->view->setVar('list', $this->list);
		$this->view->setVar('forms', $this->forms);
		
		return $this->view->render('default');
	}
	
	protected function generateForm( Content_MagicTag $tag ) : void
	{
		/**
		 * @var Content_MagicTag_Context[] $context
		 */
		$context = Tr::setCurrentDictionaryTemporary(
			dictionary: $this->manager->getModuleManifest()->getName(),
			action: function() use ($tag) : array {
				return $tag->getContexts();
			} );
		
		$form = new Form('generator_'.$tag->getId(), []);
		$form->setAction( MVC::getPage('content-magic-tags')->getURL() );
		
		
		foreach( $context as $c ) {
			
			switch($c->getType()) {
				case Content_MagicTag_Context::TYPE_PAGE:
					$eshop = EShops::getCurrent();
					$_pages = MVC::getPages(base_id: $eshop->getBaseId(), locale: $eshop->getLocale());
					$pages = [];
					foreach($_pages as $p) {
						$pages[$p->getId()] = $p->getName();
					}
					
					asort($pages);
					
					$page_id = new Form_Field_Select( 'page_id:'.$tag->getId().'_'.$c->getName(), $c->getDescription() );
					$page_id->setSelectOptions( $pages );
					
					$page_id->setFieldValueCatcher( function( string $v ) use ($c) {
						$this->context_data[$c->getName()] = $v;
					} );
					$form->addField($page_id);
					break;
				case Content_MagicTag_Context::TYPE_PRODUCT:
					$product_id = new Form_Field_Hidden( 'product_id:'.$tag->getId().'_'.$c->getName(), $c->getDescription() );
					$product_id->setFieldValueCatcher( function( string $v ) use ($c) {
						$this->context_data[$c->getName()] = $v;
					} );
					$form->addField($product_id);
					break;
				case Content_MagicTag_Context::TYPE_CATEGORY:
					$category_id = new Form_Field_Hidden( 'category_id:'.$tag->getId().'_'.$c->getName(), $c->getDescription() );
					$category_id->setFieldValueCatcher( function( string $v ) use ($c) {
						$this->context_data[$c->getName()] = $v;
					} );
					$form->addField($category_id);
					break;
				case Content_MagicTag_Context::TYPE_STRING:
					$options = $c->getOptions();
					
					if($options) {
						$select = new Form_Field_Select(  $tag->getId().'_'.$c->getName(), $c->getDescription() );
						$select->setFieldValueCatcher( function( ?string $v ) use ($c) {
							$this->context_data[$c->getName()] = $v;
						} );
						$select->setSelectOptions( $options );
						
						$form->addField($select);
					} else {
						$input = new Form_Field_Input(  $tag->getId().'_'.$c->getName(), $c->getDescription() );
						$input->setFieldValueCatcher( function( ?string $v ) use ($c) {
							$this->context_data[$c->getName()] = $v;
						} );
						$form->addField($input);
					}
					
					break;
				case Content_MagicTag_Context::TYPE_INT:
					$input = new Form_Field_Int(  $tag->getId().'_'.$c->getName(), $c->getDescription() );
					$input->setFieldValueCatcher( function( ?int $v ) use ($c) {
						$this->context_data[$c->getName()] = $v;
					} );
					$form->addField($input);
					break;
				case Content_MagicTag_Context::TYPE_FLOAT:
					$input = new Form_Field_Float(  $tag->getId().'_'.$c->getName(), $c->getDescription() );
					$input->setFieldValueCatcher( function( ?float $v ) use ($c) {
						$this->context_data[$c->getName()] = $v;
					} );
					$form->addField($input);
					break;
				case Content_MagicTag_Context::TYPE_BOOL:
					$input = new Form_Field_Checkbox(  $tag->getId().'_'.$c->getName(), $c->getDescription() );
					$input->setFieldValueCatcher( function( ?bool $v ) use ($c) {
						$this->context_data[$c->getName()] = $v;
					} );
					$form->addField($input);
					break;
			}
			
		}

		$this->forms[$tag->getId()] = $form;
	}
}