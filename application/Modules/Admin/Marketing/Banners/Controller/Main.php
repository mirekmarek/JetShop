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
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Navigation_Breadcrumb;
use Jet\Tr;
use Jet\UI_messages;
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
	
	
	public function resolve() : bool|string
	{
		$GET = Http_Request::GET();
		
		if(
			($id = $GET->getInt('id')) &&
			($banner=Marketing_Banner::get($id))
		) {
			$this->current_item = $banner;
			$this->current_item->setEditable( Main::getCurrentUserCanEdit() );
			
			$this->selected_eshop = $banner->getEshop();
			$this->selected_group = Marketing_BannerGroup::load( $banner->getGroupId() );
			
			$groups = Marketing_BannerGroup::getScope();
			$selected_group_id = $this->selected_group->getId();
			
			$this->tabs = new UI_tabs($groups, function( $group ) {
				return '';
			}, $selected_group_id);
			$this->view->setVar('selected_eshop', $this->selected_eshop );
			$this->view->setVar('tabs', $this->tabs);
			
			
			if(
				$GET->getString('action')=='delete' &&
				Main::getCurrentUserCanDelete()
			) {
				return 'delete';
			}
			
			
			return 'edit';
		}
		
		
		
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
		
		if(
			Main::getCurrentUserCanCreate() &&
			$GET->exists('create')
		) {
			return 'add';
		}
		
		
		
		return true;
	}
	
	public function default_Action() : void
	{
		Marketing_Banner::handleTimePlan();
		
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
	
	public function add_Action() : void
	{
		$this->current_item = new Marketing_Banner();
		$this->current_item->setEshop( $this->selected_eshop );
		$this->current_item->setGroupId( $this->selected_group->getId() );
		$this->current_item->setPosition( count(Marketing_Banner::getByGroup( $this->selected_eshop, $this->selected_group ))+1 );
		
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
		Marketing_Banner::handleTimePlan();
		
		/**
		 * @var Marketing_Banner $banner
		 */
		$banner = $this->current_item;
		
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
	
}