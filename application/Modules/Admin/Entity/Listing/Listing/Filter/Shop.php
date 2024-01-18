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
use JetApplication\Shops;


class Listing_Filter_Shop extends Listing_Filter_Abstract
{
	public const KEY = 'shop';
	
	protected ?string $shop_key = null;
	
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
			'' => Tr::_('- all -'),
		];
		
		foreach(Shops::getListSorted() as $shop) {
			$options[$shop->getKey()] = $shop->getShopName();
		}
		
		return $options;
	}
	
	public function catchParams(): void
	{
		
		$this->shop_key = Http_Request::GET()->getString('shop', '', array_keys( $this->getScope() ) );
		if($this->shop_key) {
			$this->listing->setParam('shop', $this->shop_key);
		}
		
	}
	
	public function generateFormFields( Form $form ): void
	{

		
		$error_messages = [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select option',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select option'
		];
		
		$is_active_general = new Form_Field_Select('shop', 'Shop:' );
		$is_active_general->setDefaultValue( $this->shop_key );
		$is_active_general->setSelectOptions( $this->getScope() );
		$is_active_general->setErrorMessages($error_messages);
		$form->addField($is_active_general);
		

	}
	
	public function catchForm( Form $form ): void
	{
		$this->shop_key = $form->field('shop')->getValue();
		if($this->shop_key!='') {
			$this->listing->setParam('shop', $this->shop_key);
		} else {
			$this->listing->setParam('shop', '');
		}
	}
	
	public function generateWhere(): void
	{
		/**
		 * @var Listing $listing
		 */
		$listing = $this->listing;
		
		if($this->shop_key) {
			$shop = Shops::get($this->shop_key);
			
			$this->listing->addFilterWhere( $shop->getWhere() );
		}
	}
	
}