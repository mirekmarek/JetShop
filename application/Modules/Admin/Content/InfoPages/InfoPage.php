<?php
namespace JetApplicationModule\Admin\Content\InfoPages;

use Jet\Data_Text;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field;
use Jet\MVC;
use Jet\Form_Field_Input;

use JetApplication\Content_InfoPage;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition]
class InfoPage extends Content_InfoPage implements Admin_Entity_WithEShopData_Interface
{
	
	use Admin_Entity_WithEShopData_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$form->field('page_id')->setIsReadonly( true );
		
		foreach( EShops::getList() as $eshop) {
			
			$form->field('/eshop_data/'.$eshop->getKey().'/relative_path_fragment')
				->setValidator( function( Form_Field_Input $field ) use ($eshop) {
					return $this->_pathFragmentValidator( $field, $eshop );
				} );
			
		}

	}
	
	protected function setupAddForm( Form $form ): void
	{
		
		$form->field('page_id')->setValidator(function( $page_id_field ) {
			$page_id = $page_id_field->getValue();
			
			foreach( EShops::getList() as $eshop) {
				if( MVC::getPage( $page_id, $eshop->getLocale(), $eshop->getBaseId() ) ) {
					$page_id_field->setError( 'page_id_is_not_unique' );
					
					return false;
				}
			}
			
			return true;
		});
		
		
		foreach( EShops::getList() as $eshop) {
			
			$form->field('/eshop_data/'.$eshop->getKey().'/relative_path_fragment')
				->setValidator( function( Form_Field_Input $field ) use ($eshop) {
					return $this->_pathFragmentValidator( $field, $eshop );
				} );
			
		}
	}
	
	public function _pathFragmentValidator( Form_Field_Input $field, EShop $eshop  ) : bool
	{
		$value = $field->getValue();
		
		$value = Data_Text::removeAccents( $value );
		$value = strtolower( $value );
		
		$value = str_replace( ' ', '-', $value );
		$value = preg_replace( '/[^a-z0-9-]/i', '', $value );
		$value = preg_replace( '~(-{2,})~', '-', $value );
		
		$field->setValue( $value );
		
		
		if( !$value ) {
			$field->setError( Form_Field::ERROR_CODE_EMPTY );
			return false;
		}
		
		$parent = MVC::getBase($eshop->getBaseId())->getHomepage($eshop->getLocale());
		
		
		foreach( $parent->getChildren() as $ch ) {
			if( $ch->getId() == $this->getPageId() ) {
				continue;
			}
			
			if( $ch->getRelativePathFragment() == $value ) {
				$field->setError('uri_is_not_unique', [
					'page' => $ch->getName()
				]);
				
				return false;
			}
		}
		
		return true;
	}
	
	
	public function defineImages() : void
	{
	}
	
	
}


