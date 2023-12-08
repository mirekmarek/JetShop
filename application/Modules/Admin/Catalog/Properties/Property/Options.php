<?php
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\Form;
use JetApplication\ProductListing;
use JetApplication\Property_Filter;
use JetApplication\Property_Value;
use JetApplication\Shops;

/**
 *
 */
class Property_Options extends Property
{
	
	/**
	 * @var Property_Options_Option[]
	 */
	protected ?array $options = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->type = Property::PROPERTY_TYPE_OPTIONS;
	}
	
	protected function _generateAddForm( Form $form ) : void
	{
		$form->removeField('decimal_places');
		
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
			
			$form->removeField('/shop_data/'.$shop_key.'/bool_yes_description');
		}
		
	}
	
	protected function _generateEditForm( Form $form ) : void
	{
		
		$form->removeField('decimal_places');
		
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
			
			$form->removeField('/shop_data/'.$shop_key.'/bool_yes_description');
		}
		
	}
	
	public function getValueInstance(): Property_Value|null
	{
		return null;
	}
	
	public function initFilter( ProductListing $listing ): void
	{
	}
	
	public function filter(): ?Property_Filter
	{
		return null;
	}
	
	
	public function getOption( int $id ) : Property_Options_Option|null
	{
		$this->getOptions();
		
		if(!isset($this->options[$id])) {
			return null;
		}
		
		return $this->options[$id];
	}
	
	/**
	 * @return Property_Options_Option[]
	 */
	public function getOptions() : array
	{
		if($this->options===null) {
			$this->options = Property_Options_Option::getListForProperty( $this->id );
		}
		
		return $this->options;
	}
	
	
	public function sortOptions( array $sort ) : void
	{
		$this->getOptions();
		$i = 0;
		foreach($sort as $id) {
			if(isset($this->options[$id])) {
				$i++;
				$this->options[$id]->setPriority($i);
				$this->options[$id]->save();
			}
		}
	}
	
	
}