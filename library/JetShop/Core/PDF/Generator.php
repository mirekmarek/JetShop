<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Manager_MetaInfo;
use JetApplication\PDF;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'PDF Generator',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_PDF_Generator extends Application_Module {

	abstract public function generate( PDF $pdf ) : string;
}