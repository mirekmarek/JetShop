<?php

/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\ProductReview_Event;

#[DataModel_Definition]
abstract class Core_ProductReview_Event_Rejected extends ProductReview_Event {

}