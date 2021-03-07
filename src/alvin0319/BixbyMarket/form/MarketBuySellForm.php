<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\form;

use alvin0319\BixbyMarket\market\Market;
use pocketmine\form\Form;
use pocketmine\Player;
use function count;
use function is_array;
use function is_numeric;

class MarketBuySellForm implements Form{

	protected Market $market;

	public function __construct(Market $market){
		$this->market = $market;
	}

	public function jsonSerialize() : array{
		$str = "§fBuy price: " . ($this->market->getBuyPrice() >= 0 ? "\$" . $this->market->getBuyPrice() : "§c-") . "§fSell price: " . ($this->market->getSellPrice() >= 0 ? "\$" . $this->market->getSellPrice() : "§c-");
		return [
			"type" => "custom_form",
			"title" => "Buy/Sell {$this->market->getItem()->getName()}",
			"content" => [
				[
					"type" => "label",
					"text" => $str
				],
				[
					"type" => "dropdown",
					"text" => "Buy/Sell",
					"options" => ["buy", "sell"]
				],
				[
					"type" => "input",
					"text" => "Amount to buy/sell item"
				]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_array($data) || count($data) !== 3){
			return;
		}
		[, $buyOrSell, $amount] = $data;

		if(!is_numeric($amount) || ($amount = (int) $amount) < 1){

		}
	}
}