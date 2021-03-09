<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\listener\InventoryListener;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use function count;
use function implode;

final class CategoryEditItemCommand extends PluginCommand{

	public function __construct(){
		parent::__construct("marketedit", BixbyMarket::getInstance());
		$this->setDescription("Edits the market category");
		$this->setPermission("bixbymarket.command.edit_market");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$this->testPermission($sender)){
			return false;
		}
		if(!$sender instanceof Player){
			return false;
		}
		if(count($args) < 1){
			$sender->sendMessage(BixbyMarket::$prefix . "Usage: /{$commandLabel} <category name>");
			return false;
		}
		$categoryName = implode(" ", $args);
		$category = BixbyMarket::getInstance()->getCategoryManager()->getCategory($categoryName);
		if($category === null){
			$sender->sendMessage(BixbyMarket::$prefix . "Unknown category name \"{$categoryName}\"!");
			return false;
		}
		InventoryListener::getInstance()->sendCategoryItemEdit($sender, $category);
		return true;
	}
}