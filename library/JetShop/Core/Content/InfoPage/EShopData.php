<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Exception;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Factory_MVC;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\IO_Dir;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use Jet\SysConf_Jet_MVC;
use Jet\UI_messages;
use JetApplication\EShopEntity_HasURL_Interface;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Content_InfoPage;


#[DataModel_Definition(
	name: 'content_info_page_eshop_data',
	database_table_name: 'content_info_page_eshop_data',
	parent_model_class: Content_InfoPage::class
)]
abstract class Core_Content_InfoPage_EShopData extends EShopEntity_WithEShopData_EShopData implements EShopEntity_HasURL_Interface
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $page_id = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
	)]
	protected string $title = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Meta keywords:'
	)]
	protected string $meta_keywords = '';
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Meta description:'
	)]
	protected string $meta_description = '';
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Text:'
	)]
	protected string $text = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Custom layout script:'
	)]
	protected string $custom_layout_script = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'URL:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter URL',
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter URL',
			'uri_is_not_unique' => 'URL conflicts with page <b>%page%</b>',
		]
	)]
	protected string $relative_path_fragment = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Menu item title:'
	)]
	protected string $menu_title = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Breadcrumb title:'
	)]
	protected string $breadcrumb_title = '';
	
	public static function getIdByURLPathPart( ?string $URL_path ): ?int
	{
		return null;
	}
	
	
	public function getPageId(): string
	{
		if(!$this->page_id) {
			$this->page_id = Content_InfoPage::get( $this->entity_id )->getPageId();
			
			$where = $this->getEshop()->getWhere();
			$where[] = 'AND';
			$where['entity_id'] = $this->entity_id;
			
			static::updateData(data: ['page_id'=>$this->page_id], where: $where);
		}
		return $this->page_id;
	}
	
	public function setPageId( string $page_id ): void
	{
		$this->page_id = $page_id;
	}
	
	public function setTitle( string $value ) : void
	{
		$this->title = $value;
	}
	
	public function getTitle() : string
	{
		return $this->title;
	}
	
	public function setMetaKeywords( string $value ) : void
	{
		$this->meta_keywords = $value;
	}
	
	public function getMetaKeywords() : string
	{
		return $this->meta_keywords;
	}
	
	public function setMetaDescription( string $value ) : void
	{
		$this->meta_description = $value;
	}
	
	public function getMetaDescription() : string
	{
		return $this->meta_description;
	}
	
	public function setText( string $value ) : void
	{
		$this->text = $value;
	}
	
	public function getText() : string
	{
		return $this->text;
	}

	public function setCustomLayoutScript( string $value ) : void
	{
		$this->custom_layout_script = $value;
	}

	public function getCustomLayoutScript() : string
	{
		return $this->custom_layout_script;
	}

	public function setRelativePathFragment( string $value ) : void
	{
		$this->relative_path_fragment = $value;
	}

	public function getRelativePathFragment() : string
	{
		return $this->relative_path_fragment;
	}
	
	public function setMenuTitle( string $value ) : void
	{
		$this->menu_title = $value;
	}
	
	public function getMenuTitle() : string
	{
		return $this->menu_title;
	}
	
	public function setBreadcrumbTitle( string $value ) : void
	{
		$this->breadcrumb_title = $value;
	}
	
	public function getBreadcrumbTitle() : string
	{
		return $this->breadcrumb_title;
	}
	
	
	public function getPage() : ?MVC_Page_Interface
	{
		if($this->getIsNew()) {
			return null;
		}
		$eshop = $this->getEshop();
		
		return MVC::getPage(
			$this->getPageId(),
			$eshop->getLocale(),
			$eshop->getBaseId()
		);
		
	}
	
	public function afterDelete() : void
	{
		$eshop = $this->getEshop();
		
		$page = MVC::getPage(
			$this->getPageId(),
			$eshop->getLocale(),
			$eshop->getBaseId()
		);

		if(!$page) {
			$this->delete();
		}
		
		$path = dirname( $page->getDataFilePath() );
		IO_Dir::remove( $path );
	}
	
	public function publish() : void
	{
		if(!$this->relative_path_fragment) {
			return;
		}
		
		
		$eshop = $this->getEshop();
		
		$page = MVC::getPage(
			$this->getPageId(),
			$eshop->getLocale(),
			$eshop->getBaseId()
		);

		$old_page_data_file = null;
		
		if(!$page) {
			$page = Factory_MVC::getPageInstance();
			$page->setBaseId( $eshop->getBaseId() );
			$page->setLocale( $eshop->getLocale() );
			$page->setId( $this->getPageId() );
			$page->setParentId( MVC::HOMEPAGE_ID );

		} else {
			$old_page_data_file =
				MVC::getBase($eshop->getBaseId())
					->getPagesDataPath($eshop->getLocale())
				.$page->getRelativePathFragment().'/'
				.SysConf_Jet_MVC::getPageDataFileName();
			
		}
		
		$page->setName( Content_InfoPage::getScope()[$this->getId()] );
		$page->setTitle( $this->getTitle() );
		$page->setMenuTitle( $this->getMenuTitle() );
		$page->setBreadcrumbTitle( $this->getBreadcrumbTitle() );
		$page->setRelativePathFragment( $this->getRelativePathFragment() );
		if($this->custom_layout_script) {
			$page->setLayoutScriptName( $this->custom_layout_script );
		} else {
			$page->setLayoutScriptName( 'default' );
		}
		
		$has_meta_description = false;
		$has_meta_keywords = false;
		
		foreach($page->getMetaTags() as $meta_tag) {
			if($meta_tag->getAttribute()!='name') {
				continue;
			}
			
			if($meta_tag->getAttributeValue()=='description') {
				$has_meta_description = true;
				$meta_tag->setContent($this->getMetaDescription());
			}
			
			if($meta_tag->getAttributeValue()=='keywords') {
				$has_meta_keywords = true;
				$meta_tag->setContent($this->getMetaDescription());
			}
		}
		
		if(!$has_meta_description) {
			$meta_description = Factory_MVC::getPageMetaTagInstance();
			$meta_description->setAttribute('name');
			$meta_description->setAttributeValue('description');
			$meta_description->setContent($this->getMetaDescription());
			
			$page->addMetaTag( $meta_description );
		}
		
		if(!$has_meta_keywords) {
			$meta_keywords = Factory_MVC::getPageMetaTagInstance();
			$meta_keywords->setAttribute('name');
			$meta_keywords->setAttributeValue('keywords');
			$meta_keywords->setContent($this->getMetaKeywords());
			
			$page->addMetaTag( $meta_keywords );
		}
		
		
		
		foreach($page->getContent() as $i=>$content) {
			$page->removeContent( $i );
		}
		
		$content = Factory_MVC::getPageContentInstance();
		$content->setOutput( $this->getText() );
		
		$page->addContent( $content );
		$page->setIsActive( $this->isActive() );
		
		
		$page_data_file = MVC::getBase($eshop->getBaseId())
			->getPagesDataPath($eshop->getLocale())
			.$page->getRelativePathFragment().'/'
			.SysConf_Jet_MVC::getPageDataFileName();
			
		$page->setDataFilePath( $page_data_file );
		

		try {
			if(
				$old_page_data_file &&
				$old_page_data_file!=$page_data_file
			) {
				IO_Dir::remove( dirname($old_page_data_file) );
			}
			
			$page->saveDataFile();
		} catch( Exception $e ) {
			UI_messages::danger( $e->getMessage() );
		}
	}
	
	
	public function getURL( array $GET_params=[] ) : string
	{
		$eshop = $this->getEshop();
		
		$page = MVC::getPage(
			$this->getPageId(),
			$eshop->getLocale(),
			$eshop->getBaseId()
		);
		
		if($page) {
			return $page->getURL( GET_params: $GET_params );
		}
		
		return '';
	}
	
	public function getURLNameDataSource() : string
	{
		return '';
	}
	
	public function checkURL( string $URL_path ) : bool
	{
		return true;
	}
	
	public function generateURLPathPart() : string
	{
		return '';
	}
	
}