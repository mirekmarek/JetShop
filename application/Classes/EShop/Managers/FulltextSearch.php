<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use JetShop\Core_EShop_Managers_FulltextSearch;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Fulltext Search',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_FulltextSearch extends Core_EShop_Managers_FulltextSearch
{

}