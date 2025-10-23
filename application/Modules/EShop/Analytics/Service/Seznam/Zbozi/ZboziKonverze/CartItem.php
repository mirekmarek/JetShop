<?php
namespace JetApplicationModule\EShop\Analytics\Service\Seznam\Zbozi;

/**
 *
 * @author Zbozi.cz <zbozi@firma.seznam.cz>
 */


class ZboziKonverze_CartItem {
	/**
	 * Item name
	 */
	public string $productName;

	/**
	 * Item identifier
	 */
	public string $itemId;

	/**
	 * Price per one item (in CZK)
	 */
	public float $unitPrice;

	/**
	 * Number of items ordered
	 */
	public int $quantity;
}

