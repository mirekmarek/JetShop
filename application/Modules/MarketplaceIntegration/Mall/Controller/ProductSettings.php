<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\MarketplaceIntegration\Mall;

use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_DateTime;
use Jet\Form_Field_Int;
use Jet\Form_Field_Select;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\MarketplaceIntegration_Module_Controller_ProductSettings;

/**
 *
 */
class Controller_ProductSettings extends MarketplaceIntegration_Module_Controller_ProductSettings
{
	
	public function initTabs() : array
	{
		$tabs = parent::initTabs();
		$tabs['labels'] = Tr::_('Labels');
		$tabs['other'] = Tr::_('Other options');
		
		return $tabs;
	}
	
	
	
	public function labels_Action() : void
	{
		/**
		 * @var Main $mp
		 */
		$mp = $this->marketplace;

		$labels = $mp->getLabels( $this->shop );
		
		$form = new Form('labels_form', []);
		
		$active_labels = $mp->getProductCommonData( $this->shop, $this->product->getId(), 'active_labels' );

		if(!$active_labels) {
			$active_labels = [];
		}
		
		foreach($labels as $id=>$title) {
			$label = new Form_Field_Checkbox('/label/'.$id.'/active', $id.' - '.$title);
			$label->setDoNotTranslateLabel( true );
			
			$from = new Form_Field_DateTime('/label/'.$id.'/from', 'From:' );
			$till = new Form_Field_DateTime('/label/'.$id.'/till', 'From:' );
			
			if(isset($active_labels[$id])) {
				/**
				 * @var ActiveLabel $active_label
				 */
				$active_label = $active_labels[$id];
				$label->setDefaultValue( true );
				$from->setDefaultValue( $active_label->getFrom() );
				$till->setDefaultValue( $active_label->getTill() );
			}
			
			$form->addField( $label );
			$form->addField( $from );
			$form->addField( $till );
			
		}
		
		if($this->editable) {
			if(Http_Request::GET()->exists('actualize_list_of_labels')) {
				$mp->getLabels( $this->shop, true );
				
				Http_Headers::reload(unset_GET_params: ['actualize_list_of_labels']);
			}
			
			if($form->catch()) {
				$active_labels = [];
				foreach($labels as $id=>$title) {
					$label = $form->field('/label/'.$id.'/active');
					if(!$label->getValue()) {
						continue;
					}
					
					$from = $form->field('/label/'.$id.'/from' );
					$till = $form->field('/label/'.$id.'/till' );
					
					$active_label = new ActiveLabel();
					$active_label->setId( $id );
					$active_label->setFrom( $from->getValue() );
					$active_label->setTill( $till->getValue() );
					
					$active_labels[$id] = $active_label;
				}
				
				$this->marketplace->setProductCommonData( $this->shop, $this->product->getId(), 'active_labels', $active_labels );
				
				Http_Headers::reload();
			}
		} else {
			$form->setIsReadonly();
		}
		
		$this->view->setVar('labels', $labels);
		$this->view->setVar('form', $form);
		
		echo $this->view->render('product_settings/labels');
	}
	
	
	/** @noinspection SpellCheckingInspection */
	public function other_Action() : void
	{
		$form = new Form('labels_form', []);
		
		$priority = new Form_Field_Int('priority', 'Priority:');
		$priority->setDefaultValue( $this->marketplace->getProductCommonData_int($this->shop, $this->product->getId(), 'priority') );
		$form->addField( $priority );
		
		$package_size = new Form_Field_Select('package_size', 'Box size:');
		$package_size->setSelectOptions([
			'' => '',
			'smallbox' => 'smallbox',
			'bigbox' => 'bigbox',
		]);
		$package_size->setDefaultValue( $this->marketplace->getProductCommonData_string($this->shop, $this->product->getId(), 'package_size') );
		$form->addField( $package_size );
		
		
		if($this->editable) {
			if($form->catch()) {
				$this->marketplace->setProductCommonData( $this->shop, $this->product->getId(), 'priority', $priority->getValue() );
				$this->marketplace->setProductCommonData( $this->shop, $this->product->getId(), 'package_size', $package_size->getValue() );
				
				Http_Headers::reload();
			}
		} else {
			$form->setIsReadonly();
		}
		
		$this->view->setVar('form', $form);
		
		echo $this->view->render('product_settings/other');
	}
}