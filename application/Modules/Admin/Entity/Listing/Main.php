<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Entity\Listing;

use Closure;
use Jet\Application_Module;
use Jet\DataListing_Column;
use Jet\DataListing_Export;
use Jet\DataListing_Filter;
use Jet\Factory_MVC;
use Jet\Http_Request;
use Jet\Translator;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Entity_Basic;
use JetApplication\Admin_Entity_Common_Manager_Interface;
use JetApplication\Admin_Entity_Common_Interface;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Entity_Listing
{
	protected Admin_Entity_Common_Interface|Entity_Basic $entity;
	protected Admin_Entity_Common_Manager_Interface $entity_manager;
	
	protected Listing $listing;
	
	/**
	 * @var callable|null
	 */
	protected $delete_uri_creator = null;
	
	/**
	 * @var callable|null
	 */
	protected $create_uri_creator = null;
	
	/**
	 * @var callable|null
	 */
	protected $create_btn_renderer = null;
	
	public function setUp(
		Admin_Entity_Common_Manager_Interface|Application_Module $entity_manager
	): void
	{
		$this->entity_manager = $entity_manager;
		$this->entity = $entity_manager::getEntityInstance();
		
		Translator::setCurrentDictionaryTemporary(
			dictionary: $this->getModuleManifest()->getName(),
			action: function() {
				$this->listing = new Listing( $this );
			}
		);
		
	}
	
	public function getEntity(): Admin_Entity_Common_Interface|Entity_Basic
	{
		return $this->entity;
	}

	public function getEntityManager(): Admin_Entity_Common_Manager_Interface
	{
		return $this->entity_manager;
	}
	
	public function renderListing() : string
	{
		$listing = $this->listing;
		$listing->handle();
		
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		$view->setVar( 'listing', $listing );
		
		Translator::setCurrentDictionaryTemporary(
			dictionary: $this->getModuleManifest()->getName(),
			action: function() use (&$res, $view) {
				$res = $view->render( 'list' );
			}
		);
		
		return $res;
	}
	

	public function getDeleteUriCreator(): callable
	{
		if(!$this->delete_uri_creator) {
			$this->delete_uri_creator = function( int $id ) : string {
				return Http_Request::currentURI( [
					'id' => $id,
					'action' => 'delete'
				] );
			};
		}
		
		return $this->delete_uri_creator;
	}
	

	public function setDeleteUriCreator( callable $delete_uri_creator ): void
	{
		$this->delete_uri_creator = $delete_uri_creator;
	}
	

	public function getCreateUriCreator(): callable
	{
		if(!$this->create_uri_creator) {
			$this->create_uri_creator = function() : string
			{
				return Http_Request::currentURI( ['action' => 'add'], ['id'] );
			};
		}
		
		return $this->create_uri_creator;
	}
	

	public function setCreateUriCreator( callable $create_uri_creator ): void
	{
		$this->create_uri_creator = $create_uri_creator;
	}
	
	
	public function addColumn( DataListing_Column $column ): void
	{
		$this->listing->addColumn( $column );
	}
	
	public function addFilter( DataListing_Filter $filter ): void
	{
		$this->listing->addFilter( $filter );
	}
	
	public function addExport( DataListing_Export $export ): void
	{
		$this->listing->addExport( $export );
	}
	
	public function setDefaultColumnsSchema( array $schema ) : void
	{
		$this->listing->setDefaultColumnsSchema( $schema );
	}
	
	public function getCreateBtnRenderer(): ?callable
	{
		return $this->create_btn_renderer;
	}
	
	public function setCreateBtnRenderer( callable $renderer ): void
	{
		$this->create_btn_renderer = $renderer;
	}
	
	public function getPrevEditUrl( int $current_id ): string
	{
		return $this->listing->getPrevEditUrl( $current_id );
	}
	
	public function getNextEditUrl( int $current_id ): string
	{
		return $this->listing->getNextEditUrl( $current_id );
	}
	
	public function setSearchWhereCreator( Closure $creator ): void
	{
		$this->listing->filter( Listing_Filter_Search::KEY )->setWhereCreator( $creator );
	}
	
	public function renderListingFilter(
		DataListing_Filter $filter,
		string $title,
		array $form_fields,
		bool $is_active,
		callable $renderer,
		string $reset_value = ''
	) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('filter', $filter);
		$view->setVar('title', $title);
		$view->setVar('form_fields', $form_fields);
		$view->setVar('is_active', $is_active);
		$view->setVar('$renderer', $renderer);
		$view->setVar('reset_value', $reset_value);
		
		return $view->render('filter');
	}
}