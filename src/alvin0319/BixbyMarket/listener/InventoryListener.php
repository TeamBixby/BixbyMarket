<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\listener;

use muqsit\invmenu\InvMenu;
use pocketmine\Player;
use pocketmine\utils\SingletonTrait;

final class InventoryListener{
	use SingletonTrait;
	/** @var InvMenu[] */
	protected array $menus = [];

	public function sendMainInventory(Player $player) : void{

	}
}