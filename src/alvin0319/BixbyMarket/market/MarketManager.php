<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\market;

use alvin0319\BixbyMarket\BixbyMarket;
use pocketmine\item\Item;
use function array_values;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;

final class MarketManager{
	/** @var Market[] */
	protected array $markets = [];

	public function __construct(){
		if(!file_exists($file = BixbyMarket::getInstance()->getDataFolder() . "markets.json")){
			return;
		}
		$data = json_decode(file_get_contents($file), true);
		foreach($data as $marketData){
			$market = Market::jsonDeserialize($marketData);
			$this->markets[$market->getId()] = $market;
		}
	}

	public function save() : void{
		$res = [];
		foreach($this->markets as $id => $market){
			$res[] = $market->jsonSerialize();
		}
		file_put_contents(BixbyMarket::getInstance()->getDataFolder() . "markets.json", json_encode($res, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
	}

	public function getMarketById(int $id) : ?Market{
		return $this->markets[$id] ?? null;
	}

	public function getMarketByItem(Item $item) : ?Market{
		foreach($this->markets as $id => $market){
			if($market->getItem()->equals($item)){
				return $market;
			}
		}
		return null;
	}

	/** @return Market[] */
	public function getMarkets() : array{
		return array_values($this->markets);
	}

	public function registerMarket(Item $item, int $buyPrice, int $sellPrice) : Market{
		$id = 0;
		while(isset($this->markets[$id]))
			$id++;
		$market = new Market($id, $item->setCount(1), $buyPrice, $sellPrice);
		return $this->markets[$market->getId()] = $market;
	}
}