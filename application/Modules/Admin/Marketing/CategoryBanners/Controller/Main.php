<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\CategoryBanners;

use Jet\AJAX;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Marketing_Banner;
use JetApplication\Marketing_CategoryBanner;

class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function getCustomTabs() : array
	{
		$tabs = [];
		
		$tabs['banners'] = Tr::_('Banners');
		
		return $tabs;
	}
	public function setupRouter( string $action, string $selected_tab ) : void
	{
		Marketing_Banner::handleTimePlan();
		
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('edit_banners', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='banners' && $action=='';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'banners'], ['action'] );
			});
		
	}
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Categories() );
		$this->listing_manager->addColumn( new Listing_Column_Banners() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'eshop',
			'active_state',
			'internal_name',
			'internal_code',
			'valid_from',
			'valid_till',
			'internal_notes',
			Listing_Column_Categories::KEY
		]);
	}
	
	
	public function edit_banners_Action() : void
	{
		$this->getEditorManager();
		
		/**
		 * @var Marketing_CategoryBanner $banner
		 */
		$banner = $this->current_item;
		
		$this->setBreadcrumbNavigation( Tr::_('Banners') );
		
		$this->view->setVar( 'item', $this->current_item );
		
		if($banner->isEditable()) {
			
			$delete_media = Http_Request::GET()->getString('delete_media');
			if($delete_media) {
				switch($delete_media) {
					case 'image_main':
						$banner->deleteImageMain();
						AJAX::snippetResponse( $this->view->render('edit/banners/image-main') );
						break;
					case 'image_mobile':
						$banner->deleteImageMobile();
						AJAX::snippetResponse( $this->view->render('edit/banners/image-mobile') );
						break;
					case 'video_main':
						$banner->deleteVideoMain();
						AJAX::snippetResponse( $this->view->render('edit/banners/video-main') );
						break;
					case 'video_mobile':
						$banner->deleteVideoMobile();
						AJAX::snippetResponse( $this->view->render('edit/banners/video-mobile') );
						break;
				}
			}
			
			if(
				$banner->catchUploadForm_MainImage()
			) {
				AJAX::operationResponse(true, snippets: [
					'image-main' => $this->view->render('edit/banners/image-main')
				]);
			}
			
			if(
				$banner->catchUploadForm_MobileImage()
			) {
				AJAX::operationResponse(true, snippets: [
					'image-mobile' => $this->view->render('edit/banners/image-mobile')
				]);
			}
			
			
			if(
				$banner->catchUploadForm_MainVideo()
			) {
				AJAX::operationResponse(true, snippets: [
					'video-main' => $this->view->render('edit/banners/video-main')
				]);
			}
			
			if(
				$banner->catchUploadForm_MobileVideo()
			) {
				AJAX::operationResponse(true, snippets: [
					'video-mobile' => $this->view->render('edit/banners/video-mobile')
				]);
			}
		}
		
		
		$this->output('edit/banners');
		
	}
	
}