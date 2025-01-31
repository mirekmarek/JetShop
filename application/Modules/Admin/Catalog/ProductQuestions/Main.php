<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;


use JetApplication\Admin_Managers_ProductQuestions;
use JetApplication\EShopEntity_Basic;
use JetApplication\ProductQuestion;


class Main extends Admin_Managers_ProductQuestions
{
	public const ADMIN_MAIN_PAGE = 'product-questions';
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new ProductQuestion();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}
	
	public function getEMailTemplates(): array
	{
		$template = new EmailTemplate_Answer();
		
		return [
			$template
		];
	}
}