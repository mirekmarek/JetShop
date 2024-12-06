<?php
namespace JetShop;

use Jet\Data_Text;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Http_Request;
use JetApplication\Entity_Marketing;
use Jet\DataModel;


#[DataModel_Definition(
	name: 'marketing_landing_page',
	database_table_name: 'marketing_landing_pages',
)]
abstract class Core_Marketing_LandingPage extends Entity_Marketing
{
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
	
}