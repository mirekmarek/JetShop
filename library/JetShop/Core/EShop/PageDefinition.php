<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\BaseObject;
use Jet\Factory_MVC;
use Jet\Locale;
use Jet\MVC;
use Jet\MVC_Page_Interface;
use Jet\Tr;
use JetApplication\EShop;
use JetApplication\EShop_PageDefinition_ContentDefinition;
use JetApplication\EShop_Pages;
use JetApplication\EShops;
use JetApplication\MVC_Page;

abstract class Core_EShop_PageDefinition extends BaseObject {
	
	protected string $parent_page_key = 'homepage';
	
	protected string $key = '';
	
	protected string $id = '';
	protected string $name = '';
	protected string $URI_path_fragment = '';
	protected string $title = '';
	protected string $icon = '';
	protected bool $is_secrtet = false;
	protected string $layout_script_name = 'default';
	
	/**
	 * @var EShop_PageDefinition_ContentDefinition[]
	 */
	protected array $content = [];
	
	public function getParentPageKey(): string
	{
		return $this->parent_page_key;
	}
	
	public function getParentDefinition() : ?static
	{
		if(!$this->parent_page_key) {
			return null;
		}
		
		return EShop_Pages::getPageDefinition( $this->parent_page_key );
	}
	
	public function setParentPageKey( string $parent_page_key ): void
	{
		$this->parent_page_key = $parent_page_key;
	}
	
	public function getKey(): string
	{
		return $this->key;
	}
	
	public function setKey( string $key ): void
	{
		$this->key = $key;
	}
	
	
	
	public function getId(): string
	{
		return $this->id;
	}
	
	public function setId( string $id ): void
	{
		$this->id = $id;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function getTitle( ?Locale $locale=null ): string
	{
		if(!$locale) {
			return $this->title;
		}
		
		return Tr::_( $this->title, dictionary: 'EShop.Pages', locale: $locale );
	}
	
	public function setTitle( string $title ): void
	{
		$this->title = $title;
	}
	
	public function getIcon(): string
	{
		return $this->icon;
	}
	
	public function setIcon( string $icon ): void
	{
		$this->icon = $icon;
	}
	
	public function getIsSecrtet(): bool
	{
		return $this->is_secrtet;
	}
	
	public function setIsSecrtet( bool $is_secrtet ): void
	{
		$this->is_secrtet = $is_secrtet;
	}
	
	public function getLayoutScriptName(): string
	{
		return $this->layout_script_name;
	}
	
	public function setLayoutScriptName( string $layout_script_name ): void
	{
		$this->layout_script_name = $layout_script_name;
	}
	
	public function getURIPathFragment(): string
	{
		return $this->URI_path_fragment;
	}
	
	public function getURIPath(): string
	{
		$path = [ $this->URI_path_fragment ];
		
		$parent = $this;
		
		while( ($parent=$parent->getParentDefinition()) ) {
			if($parent->getURIPathFragment()) {
				array_unshift( $path, $parent->getURIPathFragment() );
			}
		}
		
		return implode( '/', $path );
	}
	
	public function setURIPathFragment( string $URI_path_fragment ): void
	{
		$this->URI_path_fragment = $URI_path_fragment;
	}
	
	
	
	
	/**
	 * @return EShop_PageDefinition_ContentDefinition[]
	 */
	public function getContent(): array
	{
		return $this->content;
	}
	
	/**
	 * @param EShop_PageDefinition_ContentDefinition[] $content
	 * @return void
	 */
	public function setContent( array $content ): void
	{
		$this->content = $content;
	}
	
	
	
	public static function fromArray( array $data ) : static
	{
		$i = new static();
		
		$i->setParentPageKey( $data['parent_page_key']??'' );
		
		$i->setId( $data['id'] );
		$i->setName( $data['name']??$data['id'] );
		$i->setTitle( $data['title'] );
		$i->setURIPathFragment( $data['URI_path_fragment']??'' );
		
		if(!empty($data['icon'])) {
			$i->setIcon( $data['icon'] );
		}
		
		if(isset($data['is_secrtet'])) {
			$i->setIsSecrtet( $data['is_secret'] );
		}
		
		if(!empty($data['layout_script_name'])) {
			$i->setLayoutScriptName( $data['layout_script_name'] );
		}
		
		$i->content = [];
		foreach($data['content'] as $content) {
			$i->content[] = EShop_PageDefinition_ContentDefinition::fromArray( $content );
		}
		
		return $i;
	}
	
	public function toArray(): array
	{
		$content = [];
		foreach( $this->content as $c ) {
			$content[] = $c->toArray();
		}
		
		return [
			'parent_page_key' => $this->parent_page_key,
			
			'id' => $this->id,
			'URI_path_fragment' => $this->URI_path_fragment,
			'name' => $this->name,
			'title' => $this->title,
			'icon' => $this->icon,
			'is_secrtet' => $this->is_secrtet,
			'layout_script_name' => $this->layout_script_name,
			
			'content' => $content,
		];
	}
	
	public function getPage( ?EShop $eshop=null ): ?MVC_Page_Interface
	{
		if( !$eshop ) {
			$eshop = EShops::getCurrent();
		}
		
		return MVC::getPage( $this->id, $eshop->getLocale(), $eshop->getBaseId() );
	}
	
	
	public function createPageDefinition( EShop $eshop ) : MVC_Page_Interface
	{
		
		/**
		 * @var MVC_Page $page
		 */
		$page = Factory_MVC::getPageInstance();
		
		$page->initByDefinition( $eshop, $this );
		
		return $page;
	}
}