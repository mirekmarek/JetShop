<?php
/**
 * 
 */

namespace JetShop;

use Exception;
use Jet\Data_Text;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use Jet\Form_Field_Input;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use Jet\UI_messages;

use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops;
use JetApplication\Content_InfoPage;

/**
 *
 */
#[DataModel_Definition(
	name: 'content_info_page',
	database_table_name: 'content_info_page',
)]
class Core_Content_InfoPage extends Entity_WithShopRelation
{

	/**
	 * @var ?Form
	 */ 
	protected ?Form $_form_edit = null;

	/**
	 * @var ?Form
	 */ 
	protected ?Form $_form_add = null;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Page id:',
		is_required: true,
		validation_regexp: '/^[a-zA-Z0-9\-]{2,}$/i',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter valid page id',
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter valid page id',
			'page_id_is_not_unique' => 'Page with the identifier already exists',
		]
	)]
	protected string $page_id = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Title:'
	)]
	protected string $title = '';

	/**
	 * @var string
	 */ 
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
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Text:'
	)]
	protected string $text = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Custom layout script:'
	)]
	protected string $custom_layout_script = '';

	/**
	 * @var string
	 */ 
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

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Menu item title:'
	)]
	protected string $menu_title = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Breadcrumb title:'
	)]
	protected string $breadcrumb_title = '';

	/**
	 * @var bool
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active'
	)]
	protected bool $is_active = false;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal description:'
	)]
	protected string $internal_description = '';

	/**
	 * @return Form
	 */
	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->createForm('edit_form');

			$shop_field = new Form_Field_Select('shop', 'Shop:');
			$shop_field->setDefaultValue( $this->getShopKey() );
			$shop_field->setSelectOptions(Shops::getScope());
			$shop_field->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select shop',
				Form_Field_Select::ERROR_CODE_EMPTY => 'Please select shop'
			]);
			$shop_field->setIsReadonly(true);
			$this->_form_edit->addField($shop_field);

			$this->_form_edit->field('page_id')->setIsReadonly( true );
			$this->_form_edit->field('relative_path_fragment')->setValidator( function( Form_Field_Input $field ) {
				return $this->_pathFragmentValidator( $field );
			} );

		}
		
		return $this->_form_edit;
	}

	public function _pathFragmentValidator( Form_Field_Input $field) : bool
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

		$shop = Shops::get($field->getForm()->field('shop')->getValueRaw());
		$parent = MVC::getBase($shop->getBaseId())->getHomepage($shop->getLocale());


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

	public function afterAdd(): void
	{
		$this->publish();
	}

	public function afterUpdate(): void
	{
		$this->publish();
	}

	public function getPage() : ?MVC_Page_Interface
	{
		if($this->getIsNew()) {
			return null;
		}
		$shop = $this->getShop();

		return MVC::getPage(
			$this->getPageId(),
			$shop->getLocale(),
			$shop->getBaseId()
		);

	}

	public function publish() : void
	{
		$shop = $this->getShop();

		$page = MVC::getPage(
			$this->getPageId(),
			$shop->getLocale(),
			$shop->getBaseId()
		);

		if(!$page) {
			$page = Factory_MVC::getPageInstance();
			$page->setBaseId( $shop->getBaseId() );
			$page->setLocale( $shop->getLocale() );
			$page->setId( $this->getPageId() );
			$page->setParentId( MVC::HOMEPAGE_ID );
		}

		$page->setName( $this->getTitle() );
		$page->setTitle( $this->getTitle() );
		$page->setMenuTitle( $this->getMenuTitle() );
		$page->setBreadcrumbTitle( $this->getBreadcrumbTitle() );
		$page->setLayoutScriptName( 'default' );

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


		if($this->custom_layout_script) {
			$page->setLayoutScriptName( $this->custom_layout_script );
		}
		$page->setRelativePathFragment( $this->getRelativePathFragment() );

		foreach($page->getContent() as $i=>$content) {
			$page->removeContent( $i );
		}

		$content = Factory_MVC::getPageContentInstance();
		$content->setOutput( $this->getText() );
		
		$page->addContent( $content );

		$page->setIsActive( $this->getIsActive() );

		try {
			$page->saveDataFile();
		} catch( Exception $e ) {
			UI_messages::danger( $e->getMessage() );
		}
	}

	/**
	 * @return bool
	 */
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}

	/**
	 * @return Form
	 */
	public function getAddForm() : Form
	{
		if(!$this->_form_add) {
			$this->_form_add = $this->createForm('add_form');

			$shop_field = new Form_Field_Select('shop', 'Shop:');
			$shop_field->setSelectOptions(Shops::getScope());
			$shop_field->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select shop',
				Form_Field_Select::ERROR_CODE_EMPTY => 'Please select shop'
			]);
			$shop_field->setFieldValueCatcher(function($value) {
				$this->setShop( Shops::get($value) );
			});
			$this->_form_add->addField($shop_field);

			$this->_form_add->field('page_id')->setValidator(function( $page_id_field ) {
				$shop = Shops::get($this->_form_add->field('shop')->getValueRaw());

				$page_id = $page_id_field->getValue();

				if( MVC::getPage($page_id, $shop->getLocale(), $shop->getBaseId()) ) {
					$page_id_field->setError('page_id_is_not_unique');

					return false;
				}
				return true;
			});

			$this->_form_add->field('relative_path_fragment')->setValidator( function( Form_Field_Input $field ) {
				return $this->_pathFragmentValidator( $field );
			} );

		}
		
		return $this->_form_add;
	}

	/**
	 * @return bool
	 */
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}

	/**
	 * @param int|string $id
	 * @return static|null
	 */
	public static function get( int|string $id ) : static|null
	{
		return static::load( $id );
	}

	/**
	 * @return Content_InfoPage[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
	}

	/**
	 * @return int
	 */
	public function getId() : int
	{
		return $this->id;
	}

	/**
	 * @param string $value
	 */
	public function setPageId( string $value ) : void
	{
		$this->page_id = $value;
	}

	/**
	 * @return string
	 */
	public function getPageId() : string
	{
		return $this->page_id;
	}

	/**
	 * @param string $value
	 */
	public function setTitle( string $value ) : void
	{
		$this->title = $value;
	}

	/**
	 * @return string
	 */
	public function getTitle() : string
	{
		return $this->title;
	}

	/**
	 * @param string $value
	 */
	public function setMetaKeywords( string $value ) : void
	{
		$this->meta_keywords = $value;
	}

	/**
	 * @return string
	 */
	public function getMetaKeywords() : string
	{
		return $this->meta_keywords;
	}

	/**
	 * @param string $value
	 */
	public function setMetaDescription( string $value ) : void
	{
		$this->meta_description = $value;
	}

	/**
	 * @return string
	 */
	public function getMetaDescription() : string
	{
		return $this->meta_description;
	}

	/**
	 * @param string $value
	 */
	public function setText( string $value ) : void
	{
		$this->text = $value;
	}

	/**
	 * @return string
	 */
	public function getText() : string
	{
		return $this->text;
	}

	/**
	 * @param string $value
	 */
	public function setCustomLayoutScript( string $value ) : void
	{
		$this->custom_layout_script = $value;
	}

	/**
	 * @return string
	 */
	public function getCustomLayoutScript() : string
	{
		return $this->custom_layout_script;
	}

	/**
	 * @param string $value
	 */
	public function setRelativePathFragment( string $value ) : void
	{
		$this->relative_path_fragment = $value;
	}

	/**
	 * @return string
	 */
	public function getRelativePathFragment() : string
	{
		return $this->relative_path_fragment;
	}

	/**
	 * @param string $value
	 */
	public function setMenuTitle( string $value ) : void
	{
		$this->menu_title = $value;
	}

	/**
	 * @return string
	 */
	public function getMenuTitle() : string
	{
		return $this->menu_title;
	}

	/**
	 * @param string $value
	 */
	public function setBreadcrumbTitle( string $value ) : void
	{
		$this->breadcrumb_title = $value;
	}

	/**
	 * @return string
	 */
	public function getBreadcrumbTitle() : string
	{
		return $this->breadcrumb_title;
	}

	/**
	 * @param bool $value
	 */
	public function setIsActive( bool $value ) : void
	{
		$this->is_active = $value;
	}

	/**
	 * @return bool
	 */
	public function getIsActive() : bool
	{
		return $this->is_active;
	}

	/**
	 * @param string $value
	 */
	public function setInternalDescription( string $value ) : void
	{
		$this->internal_description = $value;
	}

	/**
	 * @return string
	 */
	public function getInternalDescription() : string
	{
		return $this->internal_description;
	}
}
