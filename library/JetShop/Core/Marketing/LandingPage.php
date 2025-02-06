<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_Text;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Http_Request;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\EShopEntity_Marketing;
use Jet\DataModel;
use JetApplication\EShopEntity_Definition;
use JetApplication\Marketing_LandingPage;


#[DataModel_Definition(
	name: 'marketing_landing_page',
	database_table_name: 'marketing_landing_pages',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Landing page',
	admin_manager_interface: Marketing_LandingPage::class
)]
abstract class Core_Marketing_LandingPage extends EShopEntity_Marketing implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Landing page title:'
	)]
	protected string $landing_page_title = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len : 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Landing page description:'
	)]
	protected string $landing_page_description = '';
	
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Landing page URL path text:'
	)]
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len : 255
	)]
	protected string $landing_page_url = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len : 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Landing page HTML:'
	)]
	protected string $landing_page_html = '';
	
	public function getLandingPageTitle(): string
	{
		return $this->landing_page_title;
	}
	
	public function setLandingPageTitle( string $landing_page_title ): void
	{
		$this->landing_page_title = $landing_page_title;
	}

	public function getLandingPageDescription(): string
	{
		return $this->landing_page_description;
	}

	public function setLandingPageDescription( string $landing_page_description ): void
	{
		$this->landing_page_description = $landing_page_description;
	}

	public function getLandingPageUrl(): string
	{
		return $this->landing_page_url;
	}

	public function setLandingPageUrl( string $landing_page_url ): void
	{
		$this->landing_page_url = $landing_page_url;
	}
	
	public function getLandingPageHtml(): string
	{
		return $this->landing_page_html;
	}
	
	public function setLandingPageHtml( string $landing_page_html ): void
	{
		$this->landing_page_html = $landing_page_html;
	}
	
	public function getURLPathPart() : string
	{
		$URL = Data_Text::removeAccents( $this->landing_page_url );
		
		$URL = strtolower($URL);
		$URL = preg_replace('/([^0-9a-zA-Z ])+/', '', $URL);
		$URL = preg_replace( '/([[:blank:]])+/', '-', $URL);
		
		
		$min_len = 2;
		
		$parts = explode('-', $URL);
		$valid_parts = array();
		foreach( $parts as $value ) {
			
			if (strlen($value) > $min_len) {
				$valid_parts[] = $value;
			}
		}
		
		$URL = count($valid_parts) > 1 ? implode('-', $valid_parts) : $URL;
		
		$URL = $URL.'-lp-'.$this->getId();
		
		return $URL;
	}
	
	public function getURL() : string
	{
		return $this->getEshop()->getURL( [$this->getURLPathPart()] );
	}
	
	public function getPreviewURL() : string
	{
		return $this->getEshop()->getURL( [$this->getURLPathPart()], GET_params: ['pvk' =>$this->generatePreviewKey()] );
	}
	
	public function generatePreviewKey() : string
	{
		return sha1(
			$this->id.'|'.$this->landing_page_title.'|'.$this->landing_page_html.'|'.$this->created.'|'.$this->last_update
		);
	}
	
	public static function getByURLPathPart( ?string $URL_path ) : ?static
	{
		
		if(!preg_match('/-lp-([0-9]+)$/', $URL_path, $res)) {
			return null;
		}
		
		$id = (int)$res[1];
		
		$lp = static::load( $id );
		
		return $lp;
	}
	
	public function checkPreviewKey() : bool
	{
		return Http_Request::GET()->getString('pvk')==$this->generatePreviewKey();
	}
	
	public function hasImages(): bool
	{
		return false;
	}
	
	public function setupForm( Form $form ): void
	{
		$form->removeField('relevance_mode');
	}
	
	protected function setupAddForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$this->setupForm( $form );
	}
	
	
	public ?Form $landing_page_edit_form = null;
	
	public function getLandingPageEditForm() : Form
	{
		if(!$this->landing_page_edit_form) {
			$this->landing_page_edit_form = $this->createForm('landing_page_edit_form', [
				'landing_page_title',
				'landing_page_description',
				'landing_page_url',
				'landing_page_html',
			]);
		}
		
		return $this->landing_page_edit_form;
	}
	
	public function catchLandingPageEditForm() : bool
	{
		return $this->getLandingPageEditForm()->catch();
	}
	
}