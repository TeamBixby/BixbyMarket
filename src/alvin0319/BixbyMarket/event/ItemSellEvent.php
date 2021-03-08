<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\event;

use alvin0319\BixbyMarket\market\Market;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

final class ItemSellEvent extends PlayerEvent implements Cancellable{

	protected Market $market;

	protected int $sellAmount;

	public function __construct(Player $player, Market $market, int $sellAmount){
		$this->player = $player;
		$this->market = $market;
		$this->sellAmount = $sellAmount;
	}

	public function getMarket() : Market{
		return $this->market;
	}

	public function getSellAmount() : int{
		return $this->sellAmount;
	}
}