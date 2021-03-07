<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket;

use alvin0319\BixbyMarket\category\CategoryManager;
use alvin0319\BixbyMarket\lang\PluginLang;
use alvin0319\BixbyMarket\market\MarketManager;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use function array_filter;
use function pathinfo;
use function scandir;

final class BixbyMarket extends PluginBase{
	use SingletonTrait;

	public static string $prefix = "§b§l[Market] §r§7";

	protected MarketManager $marketManager;

	protected CategoryManager $categoryManager;

	protected PluginLang $lang;

	public function onLoad() : void{
		self::setInstance($this);
	}

	public function onEnable() : void{
		$this->saveDefaultConfig();
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
		$this->marketManager = new MarketManager();
		$this->categoryManager = new CategoryManager([]);
		foreach(array_filter(scandir($this->getFile() . "resources/"), function(string $path) : bool{
			return pathinfo($this->getFile() . "resources/" . $path, PATHINFO_EXTENSION) === "ini";
		}) as $file){
			$this->saveResource($file);
		}
		$this->lang = new PluginLang($this->getConfig()->get("lang", "eng"), $this->getDataFolder(), "eng");
	}

	public function getMarketManager() : MarketManager{
		return $this->marketManager;
	}

	public function getCategoryManager() : CategoryManager{
		return $this->categoryManager;
	}

	public function getLanguage() : PluginLang{
		return $this->lang;
	}
}