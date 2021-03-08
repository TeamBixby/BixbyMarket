<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\form;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\market\Market;
use alvin0319\BixbyMarket\util\MarketBuyResult;
use alvin0319\BixbyMarket\util\MarketSellResult;
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
		$str = "§fBuy price: " . ($this->market->getBuyPrice() >= 0 ? "\$" . $this->market->getBuyPrice() : "§c-") . "\n§fSell price: " . ($this->market->getSellPrice() >= 0 ? "\$" . $this->market->getSellPrice() : "§c-");
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
			return;
		}
		if($buyOrSell === 0){
			$result = $this->market->buy($player, $amount);
			if($result->equals(MarketBuyResult::SUCCESS())){
				$price = $this->market->getBuyPrice() * $amount;
				$player->sendMessage(BixbyMarket::$prefix . "You bought {$this->market->getItem()->getName()} x{$amount} for \${$price}");
				return;
			}
			switch($result->name()){
				case MarketBuyResult::NOT_BUYABLE()->name():
					$player->sendMessage(BixbyMarket::$prefix . "This market can't be bought.");
					break;
				case MarketBuyResult::NOT_ENOUGH_INV():
					$player->sendMessage(BixbyMarket::$prefix . "You don't have enough inventory slot to buy this item.");
					break;
				case MarketBuyResult::NOT_ENOUGH_MONEY():
					$player->sendMessage(BixbyMarket::$prefix . "You don't have enough money to buy this item.");
					break;
				case MarketBuyResult::PLUGIN_CANCEL():
					$player->sendMessage(BixbyMarket::$prefix . "Your process has cancelled.");
					break;
			}
		}else{
			$result = $this->market->sell($player, $amount);
			if($result->equals(MarketSellResult::SUCCESS())){
				$price = $this->market->getBuyPrice() * $amount;
				$player->sendMessage(BixbyMarket::$prefix . "You sold {$this->market->getItem()->getName()} x{$amount} for \${$price}");
				return;
			}
			switch($result->name()){
				case MarketSellResult::NOT_SELLABLE()->name():
					$player->sendMessage(BixbyMarket::$prefix . "This market can't be sold.");
					break;
				case MarketSellResult::NO_ITEM():
					$player->sendMessage(BixbyMarket::$prefix . "You don't have enough item to sell this item.");
					break;
				case MarketBuyResult::PLUGIN_CANCEL():
					$player->sendMessage(BixbyMarket::$prefix . "Your process has cancelled.");
					break;
			}
		}
	}
}