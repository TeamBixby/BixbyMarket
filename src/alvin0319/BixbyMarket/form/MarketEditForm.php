<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\form;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\market\Market;
use pocketmine\form\Form;
use pocketmine\Player;
use function count;
use function is_array;
use function is_numeric;

final class MarketEditForm implements Form{

	protected Market $market;

	public function __construct(Market $market){
		$this->market = $market;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "custom_form",
			"title" => "Market edit",
			"content" => [
				[
					"type" => "input",
					"text" => "Buy price: " . $this->market->getBuyPrice() . "\nSet the price as -1 if you want make this market as non-buyable"
				],
				[
					"type" => "input",
					"text" => "Sell price: " . $this->market->getSellPrice() . "\nSet the price as -1 if you want make this market as non-sellable"
				]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_array($data) || count($data) !== 2){
			return;
		}
		[$buyPrice, $sellPrice] = $data;

		if(!is_numeric($buyPrice) || !is_numeric($sellPrice) || ($buyPrice = (int) $buyPrice) < -1 || ($sellPrice = (int) $sellPrice) < -1){
			$player->sendMessage(BixbyMarket::$prefix . "Invalid price given.");
			return;
		}
		$this->market->setBuyPrice($buyPrice);
		$this->market->setSellPrice($sellPrice);
		$player->sendMessage(BixbyMarket::$prefix . "Success!");
	}
}