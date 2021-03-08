<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\listener;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use alvin0319\BixbyMarket\form\MarketBuySellForm;
use Closure;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\BlockIds;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\utils\SingletonTrait;
use function explode;

final class InventoryListener{
	use SingletonTrait;

	/** @var InvMenu[] */
	protected array $menus = [];

	public function sendCategorySelectInventory(Player $player) : void{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Choose a category");
		$menu->setListener(Closure::fromCallable([$this, "handleCategorySelect"]));

		foreach(BixbyMarket::getInstance()->getCategoryManager()->getCategories() as $categoryIndex => $category){
			$item = $category->getItem();
			$item->setCustomName($category->getName());
			$item->setNamedTagEntry(new StringTag("category", $category->getName()));
			$menu->getInventory()->setItem($categoryIndex, $item);
		}
		$menu->setInventoryCloseListener(function(Player $player) : void{
			if(isset($this->menus[$player->getName()])){
				unset($this->menus[$player->getName()]);
			}
		});
		$menu->send($player);
		$this->menus[$player->getName()] = $menu;
	}

	public function handleCategorySelect(InvMenuTransaction $action) : InvMenuTransactionResult{
		$player = $action->getPlayer();
		$menu = $this->menus[$player->getName()] ?? null;
		if($menu === null){
			return $action->discard();
		}
		$item = $action->getOut();

		if($item->getNamedTagEntry("category") !== null){
			return $action->discard();
		}

		$category = BixbyMarket::getInstance()->getCategoryManager()->getCategory($item->getNamedTagEntry("category")->getValue());
		if($category === null){
			return $action->discard();
		}
		$this->sendCategoryInventory($player, $category);
		return $action->discard();
	}

	public function sendCategoryInventory(Player $player, Category $category) : void{
		$send = !isset($this->menus[$player->getName()]);
		$menu = $this->menus[$player->getName()] ?? null;
		if($send){
			$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
			$this->menus[$player->getName()] = $menu;
			$menu->setInventoryCloseListener(function(Player $player) : void{
				if(isset($this->menus[$player->getName()])){
					unset($this->menus[$player->getName()]);
				}
			});
		}
		$menu->getInventory()->clearAll();

		foreach($category->getMarkets() as $index => $market){
			$item = $market->getItem();
			$item->setCustomName(BixbyMarket::getInstance()->getLanguage()->translateString("market.item.name", [$item->getName()]));

			$buyPrice = $market->getBuyPrice() >= 0 ? "\$" . $market->getBuyPrice() : "§c-";
			$sellPrice = $market->getSellPrice() >= 0 ? "\$" . $market->getSellPrice() : "§c-";

			$lore = BixbyMarket::getInstance()->getLanguage()->translateString("market.item.lore", [$buyPrice, $sellPrice]);

			$item->setLore(explode("(n)", $lore));

			$item->setNamedTagEntry(new IntTag("market", $market->getId()));

			$menu->getInventory()->setItem($index, $item);
		}

		$item = ItemFactory::get(BlockIds::BED_BLOCK, 0, 1)
			->setCustomName(BixbyMarket::getInstance()->getLanguage()->translateString("market.item.sellall.name"));
		$item->setNamedTagEntry(new StringTag("sellall", ""));
		$menu->getInventory()->setItem(53, $item);

		if($send){
			$menu->send($player);
		}

		$menu->setListener(Closure::fromCallable([$this, "handleCategory"]));
	}

	public function handleCategory(InvMenuTransaction $action) : InvMenuTransactionResult{
		$player = $action->getPlayer();

		$item = $action->getOut();

		$menu = $this->menus[$player->getName()] ?? null;
		if($menu === null){
			return $action->discard();
		}

		if($item->getNamedTagEntry("market") !== null){
			$market = BixbyMarket::getInstance()->getMarketManager()->getMarketById($item->getNamedTagEntry("market")->getValue());
			if($market === null){
				return $action->discard();
			}
			$menu->onClose($player);
			return $action->discard()->then(function(Player $p) use ($market) : void{
				$p->sendForm(new MarketBuySellForm($market));
			});
		}
		if($item->getNamedTagEntry("sellall") !== null){
			// TODO: implement sellall
			if(!$player->hasPermission("bixbymarket.market.sellall")){
				return $action->discard();
			}
		}
		return $action->discard();
	}

	public function sendCategoryEdit(Player $player) : void{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Choose a category");
		$menu->setListener(Closure::fromCallable([$this, "handleCategoryEdit"]));

		foreach(BixbyMarket::getInstance()->getCategoryManager()->getCategories() as $categoryIndex => $category){
			$item = $category->getItem();
			$item->setCustomName($category->getName());
			$item->setNamedTagEntry(new StringTag("category", $category->getName()));
			$menu->getInventory()->setItem($categoryIndex, $item);
		}
		$menu->setInventoryCloseListener(function(Player $p) use ($menu) : void{
			if(isset($this->menus[$p->getName()])){
				unset($this->menus[$p->getName()]);
			}
			$res = [];
			foreach($menu->getInventory()->getContents(false) as $index => $item){
				if($item->getNamedTagEntry("category") !== null){
					$res[$index] = BixbyMarket::getInstance()->getCategoryManager()->getCategory($item->getNamedTagEntry("category")->getValue());
				}
			}
			BixbyMarket::getInstance()->getCategoryManager()->setCategories($res);
		});
		$menu->send($player);
		$this->menus[$player->getName()] = $menu;
	}

	public function handleCategoryEdit(InvMenuTransaction $action) : InvMenuTransactionResult{
		$player = $action->getPlayer();
		$menu = $this->menus[$player->getName()] ?? null;
		$item = $action->getOut();

		if($menu === null){
			return $action->discard();
		}
		if($item->getNamedTagEntry("category") === null){
			return $action->discard();
		}
		return $action->continue();
	}

	public function sendCategoryItemEdit(Player $player, Category $category) : void{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName($category->getName() . " Category edit");

		foreach($category->getMarkets() as $index => $market){
			$item = $market->getItem();

			$menu->getInventory()->setItem($index, $item);
		}

		$item = ItemFactory::get(BlockIds::BED_BLOCK, 0, 1)
			->setCustomName(BixbyMarket::getInstance()->getLanguage()->translateString("market.item.sellall.name"));
		$item->setNamedTagEntry(new StringTag("sellall", ""));
		$menu->getInventory()->setItem(53, $item);

		$menu->setInventoryCloseListener(function(Player $p) use ($menu, $category) : void{
			if(isset($this->menus[$p->getName()])){
				unset($this->menus[$p->getName()]);
			}
			$res = [];
			foreach($menu->getInventory()->getContents(false) as $index => $item){
				$market = BixbyMarket::getInstance()->getMarketManager()->getMarketByItem($item);
				if($market === null){
					$market = BixbyMarket::getInstance()->getMarketManager()->registerMarket($item, -1, -1);
				}
				$res[$index] = $market;
			}
			$category->setMarkets($res);
		});

		$menu->send($player);

		$this->menus[$player->getName()] = $menu;
	}

	public function handleCategoryItemEdit(InvMenuTransaction $action) : InvMenuTransactionResult{
		$player = $action->getPlayer();
		$menu = $this->menus[$player->getName()] ?? null;

		if($menu === null){
			return $action->discard();
		}

		return $action->continue();
	}
}