<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\EShops;


class Listing_Filter_EShop extends Listing_Filter_Abstract
{
	public const KEY = 'eshop';
	
	protected ?string $eshop_key = null;
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	
	protected function getScope() : array
	{
		$options = [
			'' => Tr::_('- all -', dictionary: Tr::COMMON_DICTIONARY),
		];
		
		foreach( EShops::getListSorted() as $eshop) {
			$options[$eshop->getKey()] = $eshop->getName();
		}
		
		return $options;
	}
	
	public function catchParams(): void
	{
		
		$this->eshop_key = Http_Request::GET()->getString('eshop', '', array_keys( $this->getScope() ) );
		if($this->eshop_key) {
			$this->listing->setParam('eshop', $this->eshop_key);
		}
		
	}
	
	public function generateFormFields( Form $form ): void
	{

		
		$error_messages = [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select option',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select option'
		];
		
		$is_active_general = new Form_Field_Select('eshop', 'Shop:' );
		$is_active_general->setDefaultValue( $this->eshop_key );
		$is_active_general->setSelectOptions( $this->getScope() );
		$is_active_general->setErrorMessages($error_messages);
		$form->addField($is_active_general);
		

	}
	
	public function catchForm( Form $form ): void
	{
		$this->eshop_key = $form->field('eshop')->getValue();
		if($this->eshop_key!='') {
			$this->listing->setParam('eshop', $this->eshop_key);
		} else {
			$this->listing->setParam('eshop', '');
		}
	}
	
	public function generateWhere(): void
	{
		/**
		 * @var Listing $listing
		 */
		$listing = $this->listing;
		
		if($this->eshop_key) {
			$eshop = EShops::get($this->eshop_key);
			
			$this->listing->addFilterWhere( $eshop->getWhere() );
		}
	}
	
}