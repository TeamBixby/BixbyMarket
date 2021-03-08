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

final class CategoryEditCommand extends PluginCommand{

	public function __construct(){
		parent::__construct("categoryedit", BixbyMarket::getInstance());
		$this->setDescription("Edits the category");
		$this->setPermission("bixbymarket.command.edit_category");
		$this->setAliases(["ce"]);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		if(!$this->testPermission($sender)){
			return false;
		}
		if(!$sender instanceof Player){
			return false;
		}
		InventoryListener::getInstance()->sendCategoryEdit($sender);
		return true;
	}
}