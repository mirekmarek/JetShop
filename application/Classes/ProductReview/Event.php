<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\DataModel_Definition;
use JetShop\Core_ProductReview_Event;

#[DataModel_Definition]
abstract class ProductReview_Event extends Core_ProductReview_Event
{
}