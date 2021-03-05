<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket;

use alvin0319\BixbyMarket\category\CategoryManager;
use alvin0319\BixbyMarket\market\MarketManager;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

final class BixbyMarket extends PluginBase{
	use SingletonTrait;

	protected MarketManager $marketManager;

	protected CategoryManager $categoryManager;

	public function onLoad() : void{
		self::setInstance($this);
	}

	public function onEnable() : void{
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
		$this->marketManager = new MarketManager();
		$this->categoryManager = new CategoryManager([]);
	}

	public function getMarketManager() : MarketManager{
		return $this->marketManager;
	}

	public function getCategoryManager() : CategoryManager{
		return $this->categoryManager;
	}
}