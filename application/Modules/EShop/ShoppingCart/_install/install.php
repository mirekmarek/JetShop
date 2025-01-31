<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ShoppingCart;


use Jet\DataModel_Helper;

DataModel_Helper::create( Storage::class );
DataModel_Helper::create( Storage_SelectedGifts::class );