<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\form\MarketEditForm;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

final class MarketEditCommand extends PluginCommand{

	public function __construct(){
		parent::__construct("medit", BixbyMarket::getInstance());
		$this->setDescription("Edits the market");
		$this->setPermission("bixbymarket.command.edit_market");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$this->testPermission($sender)){
			return false;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(BixbyMarket::$prefix . "You can't run this command on console.");
			return false;
		}
		$item = $sender->getInventory()->getItemInHand();
		$market = BixbyMarket::getInstance()->getMarketManager()->getMarketByItem($item);
		if($item->isNull() || $market === null){
			$sender->sendMessage(BixbyMarket::$prefix . "You can't edit the market which is not exist.");
			return false;
		}
		$sender->sendForm(new MarketEditForm($market));
		return true;
	}
}