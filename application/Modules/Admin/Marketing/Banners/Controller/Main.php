<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Marketing\Banners;


use Jet\AJAX;
use Jet\Application;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_tabs;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\Marketing_Banner;
use JetApplication\Marketing_BannerGroup;

class Controller_Main extends Admin_EntityManager_Controller
{
	
	protected ?EShop $selected_eshop = null;
	
	protected ?Marketing_BannerGroup $selected_group = null;
	
	public function getEntityNameReadable(): string
	{
		return 'Banner';
	}
	
	public function getTabs() : array
	{
		$tabs = parent::getTabs();
		
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
	
	
	public function listing_Action() : void
	{
		$GET = Http_Request::GET();
		
		
		
		$eshop_key = $GET->getString('eshop',
			default_value: EShops::getCurrentKey()
			,valid_values: array_keys(EShops::getList()));
		$this->selected_eshop = EShops::get( $eshop_key );
		
		$groups = Marketing_BannerGroup::getScope();
		$selected_group_id = Http_Request::GET()->getInt('group', array_keys($groups)[0]);
		
		$this->tabs = new UI_tabs($groups, function( $group ) {
			return Http_Request::currentURI(['group'=>$group]);
		}, $selected_group_id);
		
		$selected_group_id = $this->tabs->getSelectedTabId();
		$this->selected_group = Marketing_BannerGroup::load( $selected_group_id );
		
		$this->view->setVar('selected_eshop', $this->selected_eshop );
		$this->view->setVar('tabs', $this->tabs);
		$this->view->setVar('selected_group', $this->selected_group);
		
		
		$list = Marketing_Banner::getByGroup( $this->selected_eshop, $this->selected_group );
		
		if(Main::getCurrentUserCanEdit()) {
			$GET = Http_Request::GET();

			if(($sort=$GET->getString('sort'))) {
				$p = 0;
				$sort = explode(',', $sort);
				foreach($sort as $id) {
					$id = (int)$id;
					$p++;
					$list[$id]->setPosition( $p );
					$list[$id]->save();
				}
				Application::end();
			}
			
		}
		
		$this->view->setVar('list', $list);
		
		$this->output('list');
	}
	
	public function newItemFactory() : mixed
	{
		$GET = Http_Request::GET();
		
		$item = parent::newItemFactory();
		
		$eshop_key = $GET->getString('eshop',
			default_value: EShops::getCurrentKey()
			,valid_values: array_keys(EShops::getList()));
		$selected_eshop = EShops::get( $eshop_key );
		
		$groups = Marketing_BannerGroup::getScope();
		$selected_group_id = Http_Request::GET()->getInt('group', array_keys($groups)[0]);
		
		$item->setGroupId( $selected_group_id );
		$item->setEshop( $selected_eshop );
		
		
		return $item;
	}
	
	public function edit_banners_Action() : void
	{
		$banner = $this->current_item;
		
		$this->setBreadcrumbNavigation( Tr::_('Banners') );
		
		$this->view->setVar( 'item', $this->current_item );
		
		if($banner->isEditable()) {
			
			$group = $banner->getGroup();
			
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
				$group->getHasMainImage() &&
				$banner->catchUploadForm_MainImage()
			) {
				AJAX::operationResponse(true, snippets: [
					'image-main' => $this->view->render('edit/banners/image-main')
				]);
			}
			
			if(
				$group->getHasMobileImage() &&
				$banner->catchUploadForm_MobileImage()
			) {
				AJAX::operationResponse(true, snippets: [
					'image-mobile' => $this->view->render('edit/banners/image-mobile')
				]);
			}
			
			
			if(
				$group->getHasMainVideo() &&
				$banner->catchUploadForm_MainVideo()
			) {
				AJAX::operationResponse(true, snippets: [
					'video-main' => $this->view->render('edit/banners/video-main')
				]);
			}
			
			if(
				$group->getHasMobileVideo() &&
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