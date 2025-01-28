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
use JetApplication\Entity_WithEShopData;
use JetApplication\EShops;


class Listing_Filter_IsActive extends Listing_Filter_Abstract
{
	public const KEY = 'is_active';
	
	protected string $is_active_general = '';
	
	protected array $is_active_per_eshop = [];
	
	protected ?bool $multi_eshop_mode = null;
	
	public function __construct()
	{
	}
	
	public function getKey(): string
	{
		return static::KEY;
	}
	

	public function isMultiEShopMode(): bool
	{
		if($this->multi_eshop_mode===null) {
			/**
			 * @var Listing $listing
			 */
			$listing = $this->listing;
			
			$entity = $listing->getEntity();
			
			$this->multi_eshop_mode = ($entity instanceof Entity_WithEShopData) && EShops::isMultiEShopMode();
			
			if($this->multi_eshop_mode) {
				foreach( EShops::getListSorted() as $code => $eshop) {
					if(!array_key_exists($code, $this->is_active_per_eshop)) {
						$this->is_active_per_eshop[$code] = '';
					}
				}
			}
			
		}
		
		return $this->multi_eshop_mode;
	}
	
	
	
	
	public function catchParams(): void
	{
		$this->is_active_general = Http_Request::GET()->getString('is_active_general', '', ['', '1', '-1']);
		if($this->is_active_general!='') {
			$this->listing->setParam('is_active_general', $this->is_active_general);
		}
		
		if($this->isMultiEShopMode()) {
			foreach( EShops::getListSorted() as $code => $eshop) {
				$this->is_active_per_eshop[$code] = Http_Request::GET()->getString('is_active_'.$code, '', ['', '1', '-1']);
				
				if( $this->is_active_per_eshop[$code]!='') {
					$this->listing->setParam('is_active_'.$code, $this->is_active_per_eshop[$code]);
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
		
		if($this->isMultiEShopMode()) {
			foreach( EShops::getListSorted() as $code => $eshop ) {
				
				$is_active = new Form_Field_Select( 'is_active_' . $code, 'Is active - ' . $eshop->getName() . ':' );
				$is_active->setDefaultValue( $this->is_active_per_eshop[$code] );
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
		
		if($this->isMultiEShopMode()) {
			foreach( EShops::getListSorted() as $code => $eshop) {
				$this->is_active_per_eshop[$code] = $form->field('is_active_'.$code)->getValue();
				if( $this->is_active_per_eshop[$code]!='') {
					$this->listing->setParam('is_active_'.$code, $this->is_active_per_eshop[$code]);
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
		
		if($this->isMultiEShopMode()) {
			
			$eshop_data = $listing->getEntity()::getEntityShopDataInstance();
			foreach( EShops::getList() as $code=>$eshop) {
				if( $this->is_active_per_eshop[$code]=='') {
					continue;
				}
				
				
				$ids = $eshop_data::dataFetchCol(
					select:['entity_id'],
					where: [
						$eshop->getWhere(),
						'AND',
						'is_active_for_eshop' => ($this->is_active_per_eshop[$code]=='1')
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