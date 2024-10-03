<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\Banners;

use Jet\AJAX;
use Jet\Application;
use Jet\Application_Module;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Navigation_Breadcrumb;
use Jet\Tr;
use Jet\UI_messages;
use Jet\UI_tabs;
use JetApplication\Admin_EntityManager_Marketing_Controller;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Entity_Edit;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\Marketing_BannerGroup;

class Controller_Main extends Admin_EntityManager_Marketing_Controller
{
	
	protected ?Shops_Shop $selected_shop = null;
	
	protected ?Marketing_BannerGroup $selected_group = null;
	
	
	public function resolve() : bool|string
	{
		$GET = Http_Request::GET();
		
		if(
			($id = $GET->getInt('id')) &&
			($banner=Banner::get($id))
		) {
			$this->current_item = $banner;
			$this->current_item->setEditable( Main::getCurrentUserCanEdit() );
			
			$this->selected_shop = $banner->getShop();
			$this->selected_group = Marketing_BannerGroup::load( $banner->getGroupId() );
			
			$groups = Marketing_BannerGroup::getScope();
			$selected_group_id = $this->selected_group->getId();
			
			$this->tabs = new UI_tabs($groups, function( $group ) {
				return '';
			}, $selected_group_id);
			$this->view->setVar('selected_shop', $this->selected_shop );
			$this->view->setVar('tabs', $this->tabs);
			
			
			if(
				$GET->getString('action')=='delete' &&
				Main::getCurrentUserCanDelete()
			) {
				return 'delete';
			}
			
			
			return 'edit';
		}
		
		
		
		$shop_key = $GET->getString('shop',
			default_value: Shops::getCurrentKey()
			,valid_values: array_keys(Shops::getList()));
		$this->selected_shop = Shops::get( $shop_key );
		
		$groups = Marketing_BannerGroup::getScope();
		$selected_group_id = Http_Request::GET()->getInt('group', array_keys($groups)[0]);
		
		$this->tabs = new UI_tabs($groups, function( $group ) {
			return Http_Request::currentURI(['group'=>$group]);
		}, $selected_group_id);
		
		$selected_group_id = $this->tabs->getSelectedTabId();
		$this->selected_group = Marketing_BannerGroup::load( $selected_group_id );
		
		$this->view->setVar('selected_shop', $this->selected_shop );
		$this->view->setVar('tabs', $this->tabs);
		$this->view->setVar('selected_group', $this->selected_group);
		
		if(
			Main::getCurrentUserCanCreate() &&
			$GET->exists('create')
		) {
			return 'add';
		}
		
		
		
		return true;
	}
	
	public function initBreadcrumb() : void
	{
		Admin_Managers::UI()->initBreadcrumb();
	}
	
	public function default_Action() : void
	{
		Banner::handleTimePlan();
		
		$this->initBreadcrumb();
		
		$list = Banner::getByGroup( $this->selected_shop, $this->selected_group );
		
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
	
	public function add_Action() : void
	{
		$this->current_item = new Banner();
		$this->current_item->setShop( $this->selected_shop );
		$this->current_item->setGroupId( $this->selected_group->getId() );
		$this->current_item->setPosition( count(Banner::getByGroup( $this->selected_shop, $this->selected_group ))+1 );
		
		$this->initBreadcrumb();
		
		Navigation_Breadcrumb::addURL( Tr::_('New banner') );
		
		
		$form = $this->current_item->getAddForm();
		
		if( $this->current_item->catchAddForm() ) {
			$this->current_item->save();
			
			
			UI_messages::success( $this->generateText_add_msg() );
			
			Http_Headers::reload(
				set_GET_params: ['id'=>$this->current_item->getId()],
				unset_GET_params: ['action','create']
			);
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $this->current_item );
		
		$this->output('add');
		
	}
	
	public function edit_Action() : void
	{
		Banner::handleTimePlan();
		
		/**
		 * @var Banner $banner
		 */
		$banner = $this->current_item;
		
		$this->initBreadcrumb();
		
		Navigation_Breadcrumb::addURL( Tr::_('Banner %b%', ['b'=>$banner->getAdminTitle()]) );
		
		$form = $banner->getEditForm();
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $this->current_item );
		
		if($banner->isEditable()) {
			
			$group = $banner->getGroup();
			
			$delete_media = Http_Request::GET()->getString('delete_media');
			if($delete_media) {
				switch($delete_media) {
					case 'image_main':
						$banner->deleteImageMain();
						echo $this->view->render('edit/image-main');
						Application::end();
						break;
					case 'image_mobile':
						$banner->deleteImageMobile();
						echo $this->view->render('edit/image-mobile');
						Application::end();
						break;
					case 'video_main':
						$banner->deleteVideoMain();
						echo $this->view->render('edit/video-main');
						Application::end();
						break;
					case 'video_mobile':
						$banner->deleteVideoMobile();
						echo $this->view->render('edit/video-mobile');
						Application::end();
						break;
				}
			}
			
			if(
				$group->getHasMainImage() &&
				$banner->catchUploadForm_MainImage()
			) {
				AJAX::operationResponse(true, snippets: [
					'image-main' => $this->view->render('edit/image-main')
				]);
			}
			
			if(
				$group->getHasMobileImage() &&
				$banner->catchUploadForm_MobileImage()
			) {
				AJAX::operationResponse(true, snippets: [
					'image-mobile' => $this->view->render('edit/image-mobile')
				]);
			}
			
			
			if(
				$group->getHasMainVideo() &&
				$banner->catchUploadForm_MainVideo()
			) {
				AJAX::operationResponse(true, snippets: [
					'video-main' => $this->view->render('edit/video-main')
				]);
			}
			
			if(
				$group->getHasMobileVideo() &&
				$banner->catchUploadForm_MobileVideo()
			) {
				AJAX::operationResponse(true, snippets: [
					'video-mobile' => $this->view->render('edit/video-mobile')
				]);
			}
			
			
			if( $banner->catchEditForm() ) {
				$banner->save();
				
				UI_messages::success( $this->generateText_edit_main_msg() );
				Http_Headers::reload();
			}
		}
		
		
		$this->output('edit');
		
	}
	
	
	protected function createEntityEditorModule(): Application_Module|Admin_Managers_Entity_Edit
	{
		return Admin_Managers::EntityEdit_Common();
	}
}