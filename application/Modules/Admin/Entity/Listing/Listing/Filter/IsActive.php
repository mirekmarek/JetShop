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
use JetApplication\Entity_WithShopData;
use JetApplication\Shops;


class Listing_Filter_IsActive extends Listing_Filter_Abstract
{
	public const KEY = 'is_active';
	
	protected string $is_active_general = '';
	
	protected array $is_active_per_shop = [];
	
	protected ?bool $multi_shop_mode = null;
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	

	public function isMultiShopMode(): bool
	{
		if($this->multi_shop_mode===null) {
			/**
			 * @var Listing $listing
			 */
			$listing = $this->listing;
			
			$entity = $listing->getEntity();
			
			$this->multi_shop_mode = ($entity instanceof Entity_WithShopData);
			
			if(
				$this->multi_shop_mode &&
				!Shops::isMultiShopMode()
			)  {
				$this->multi_shop_mode = false;
			}
			
			if($this->multi_shop_mode) {
				foreach(Shops::getListSorted() as $code => $shop) {
					if(!array_key_exists($code, $this->is_active_per_shop)) {
						$this->is_active_per_shop[$code] = '';
					}
				}
			}
			
		}
		
		return $this->multi_shop_mode;
	}
	
	
	
	
	public function catchParams(): void
	{
		$this->is_active_general = Http_Request::GET()->getString('is_active_general', '', ['', '1', '-1']);
		if($this->is_active_general!='') {
			$this->listing->setParam('is_active_general', $this->is_active_general);
		}
		
		if($this->isMultiShopMode()) {
			foreach(Shops::getListSorted() as $code => $shop) {
				$this->is_active_per_shop[$code] = Http_Request::GET()->getString('is_active_'.$code, '', ['', '1', '-1']);
				
				if($this->is_active_per_shop[$code]!='') {
					$this->listing->setParam('is_active_'.$code, $this->is_active_per_shop[$code]);
				}
			}
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [
			'' => Tr::_('- all -'),
			'-1' => Tr::_('Not active'),
			'1' => Tr::_('Active'),
		];
		
		$error_messages = [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select option',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select option'
		];
		
		$is_active_general = new Form_Field_Select('is_active_general', 'Is active - general:' );
		$is_active_general->setDefaultValue( $this->is_active_general );
		$is_active_general->setSelectOptions( $options );
		$is_active_general->setErrorMessages($error_messages);
		$form->addField($is_active_general);
		
		if($this->isMultiShopMode()) {
			foreach( Shops::getListSorted() as $code => $shop ) {
				
				$is_active = new Form_Field_Select( 'is_active_' . $code, 'Is active - ' . $shop->getShopName() . ':' );
				$is_active->setDefaultValue( $this->is_active_per_shop[$code] );
				$is_active->setSelectOptions( $options );
				$is_active->setErrorMessages( $error_messages );
				$form->addField( $is_active );
			}
		}
	}
	
	public function catchForm( Form $form ): void
	{
		$this->is_active_general = $form->field('is_active_general')->getValue();
		if($this->is_active_general!='') {
			$this->listing->setParam('is_active_general', $this->is_active_general);
		} else {
			$this->listing->setParam('is_active_general', '');
		}
		
		if($this->isMultiShopMode()) {
			foreach(Shops::getListSorted() as $code => $shop) {
				$this->is_active_per_shop[$code] = $form->field('is_active_'.$code)->getValue();
				if($this->is_active_per_shop[$code]!='') {
					$this->listing->setParam('is_active_'.$code, $this->is_active_per_shop[$code]);
				} else {
					$this->listing->unsetParam('is_active_'.$code);
				}
			}
		}
	}
	
	public function generateWhere(): void
	{
		$where = [];
		/**
		 * @var Listing $listing
		 */
		$listing = $this->listing;
		
		if($this->is_active_general!='') {
			$where['is_active'] = ($this->is_active_general=='1');
		}
		
		if($this->isMultiShopMode()) {
			
			$shop_data = $listing->getEntity()::getEntityShopDataInstance();
			foreach(Shops::getList() as $code=>$shop) {
				if($this->is_active_per_shop[$code]=='') {
					continue;
				}
				
				
				$ids = $shop_data::dataFetchCol(
					select:['entity_id'],
					where: [
						$shop->getWhere(),
						'AND',
						'is_active_for_shop' => ($this->is_active_per_shop[$code]=='1')
					]);

				if(!$ids) {
					$ids = [0];
				}
				
				if($where) {
					$where[] = 'AND';
				}
				
				$where[] = [
					'id' => $ids
				];
			}
		}
		
		if($where) {
			$this->listing->addFilterWhere($where);
		}
	}
	
}