<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\MagicTags;

use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Float;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Form_Field_Int;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Content_MagicTag_Context;
use JetApplication\Shop_Managers;
use JetApplication\Shops;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		Admin_Managers::UI()->initBreadcrumb();
		
		$manager = Shop_Managers::MagicTags();
		if(!$manager) {
			return;
		}
		
		$list = $manager->getList();
		
		$selected_tag = null;
		$selected_tag_id = Http_Request::GET()->getString('tag', default_value: '', valid_values: array_keys($list));
		if( $selected_tag_id ) {
			$selected_tag = $list[$selected_tag_id];
		}
		
		if($selected_tag) {
			/**
			 * @var Content_MagicTag_Context[] $context
			 */
			$context = Tr::setCurrentDictionaryTemporary(dictionary: $manager->getModuleManifest()->getName(),action: function() use ($selected_tag) : array {
				return $selected_tag->getContexts();
			} );
			
			$form = new Form('generator', []);
			
			$context_data = [];
 
			foreach( $context as $c ) {

				switch($c->getType()) {
					case Content_MagicTag_Context::TYPE_PAGE:
						$shop = Shops::getCurrent();
						$_pages = MVC::getPages(base_id: $shop->getBaseId(), locale: $shop->getLocale());
						$pages = [];
						foreach($_pages as $p) {
							$pages[$p->getId()] = $p->getName();
						}
						
						$page_id = new Form_Field_Select('page_id:'.$c->getName(), $c->getDescription() );
						$page_id->setSelectOptions( $pages );
						
						$page_id->setFieldValueCatcher( function( string $v ) use (&$context_data, $c) {
							$context_data[$c->getName()] = $v;
						} );
						$form->addField($page_id);
						break;
					case Content_MagicTag_Context::TYPE_PRODUCT:
						$product_id = new Form_Field_Hidden('product_id:'.$c->getName(), $c->getDescription() );
						$product_id->setFieldValueCatcher( function( string $v ) use (&$context_data, $c) {
							$context_data[$c->getName()] = $v;
						} );
						$form->addField($product_id);
						break;
					case Content_MagicTag_Context::TYPE_CATEGORY:
						$category_id = new Form_Field_Hidden('category_id:'.$c->getName(), $c->getDescription() );
						$category_id->setFieldValueCatcher( function( string $v ) use (&$context_data, $c) {
							$context_data[$c->getName()] = $v;
						} );
						$form->addField($category_id);
						break;
					case Content_MagicTag_Context::TYPE_STRING:
						$input = new Form_Field_Input($c->getName(), $c->getDescription() );
						$input->setFieldValueCatcher( function( ?string $v ) use (&$context_data, $c) {
							$context_data[$c->getName()] = $v;
						} );
						$form->addField($input);
						break;
					case Content_MagicTag_Context::TYPE_INT:
						$input = new Form_Field_Int($c->getName(), $c->getDescription() );
						$input->setFieldValueCatcher( function( ?int $v ) use (&$context_data, $c) {
							$context_data[$c->getName()] = $v;
						} );
						$form->addField($input);
						break;
					case Content_MagicTag_Context::TYPE_FLOAT:
						$input = new Form_Field_Float($c->getName(), $c->getDescription() );
						$input->setFieldValueCatcher( function( ?float $v ) use (&$context_data, $c) {
							$context_data[$c->getName()] = $v;
						} );
						$form->addField($input);
						break;
					case Content_MagicTag_Context::TYPE_BOOL:
						$input = new Form_Field_Checkbox($c->getName(), $c->getDescription() );
						$input->setFieldValueCatcher( function( ?bool $v ) use (&$context_data, $c) {
							$context_data[$c->getName()] = $v;
						} );
						$form->addField($input);
						break;
				}
				
				if($form->catch()) {
					$generated = $selected_tag->generate( $context_data );
					$this->view->setVar('generated', $generated);
				}
			}
			
			$this->view->setVar('form', $form);
			$this->view->setVar('selected_tag', $selected_tag );
		}
		
		
		$this->view->setVar('list', $list);
		$this->view->setVar('selected_tag_id', $selected_tag_id);
		
		
		$this->output('default');
	}
}