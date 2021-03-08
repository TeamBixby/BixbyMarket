<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use function count;
use function implode;

final class CategoryCreateCommand extends PluginCommand{

	public function __construct(){
		parent::__construct("categorycreate", BixbyMarket::getInstance());
		$this->setDescription("Creates a market category");
		$this->setPermission("bixbymarket.command.category_create");
		$this->setAliases(["cc"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$this->testPermission($sender)){
			return false;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage(BixbyMarket::$prefix . "You can't run this command on console.");
			return false;
		}
		if(count($args) < 1){
			$sender->sendMessage(BixbyMarket::$prefix . "Usage: /{$commandLabel} <category name>");
			return false;
		}
		$categoryName = implode(" ", $args);
		if(BixbyMarket::getInstance()->getCategoryManager()->getCategory($categoryName) !== null){
			$sender->sendMessage(BixbyMarket::$prefix . "Category name {$categoryName} is already in use.");
			return false;
		}
		$item = $sender->getInventory()->getItemInHand();
		if($item->isNull()){
			$sender->sendMessage(BixbyMarket::$prefix . "Category item is invalid.");
			return false;
		}
		BixbyMarket::getInstance()->getCategoryManager()->addCategory(BixbyMarket::getInstance()->getCategoryManager()->getAvailableIndex(), new Category($categoryName, [], $item->setCount(1)));
		$sender->sendMessage(BixbyMarket::$prefix . "Success!");
		return true;
	}
}