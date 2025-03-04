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

final class CategoryCommand extends PluginCommand{

	public function __construct(){
		parent::__construct("mcategory", BixbyMarket::getInstance());
		$this->setDescription("Opens the Category GUI");
		$this->setPermission("bixbymarket.command.open_category");
		$this->setAliases(["mca"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$this->testPermission($sender)){
			return false;
		}
		if(!$sender instanceof Player){
			return false;
		}
		if(count($args) > 0){
			$categoryName = implode(" ", $args);
			$category = BixbyMarket::getInstance()->getCategoryManager()->getCategory($categoryName);
			if($category === null){
				$sender->sendMessage(BixbyMarket::$prefix . "Unknown category name \"{$categoryName}\"!");
				return false;
			}
			InventoryListener::getInstance()->sendCategoryInventory($sender, $category);
			return true;
		}
		InventoryListener::getInstance()->sendCategorySelectInventory($sender);
		return true;
	}
}