<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShopModule\Admin\UI;

use Jet\Application_Module;
use Jet\Navigation_Breadcrumb;
use Jet\MVC;
use Jet\UI;


/**
 *
 */
class Main extends Application_Module
{

	/**
	 *
	 */
	public static function initBreadcrumb()
	{
		$page = MVC::getPage();

		Navigation_Breadcrumb::reset();

		Navigation_Breadcrumb::addURL(
			UI::icon( $page->getIcon() ).'&nbsp;&nbsp;'.$page->getBreadcrumbTitle(),
			$page->getURL()
		);

	}
}