<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\category;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\market\Market;
use JsonSerializable;
use pocketmine\item\Item;

final class Category implements JsonSerializable{

	protected string $name;
	/** @var Market[] */
	protected array $markets = [];

	public function __construct(string $name, array $markets){
		$this->name = $name;
		foreach($markets as $position => $marketId){
			$market = BixbyMarket::getInstance()->getMarketManager()->getMarketById($marketId);
			if($market !== null){
				$this->markets[$position] = $market;
			}
		}
	}

	public function getName() : string{
		return $this->name;
	}

	/** @return Market[] */
	public function getMarkets() : array{
		return $this->markets;
	}

	/** @param Item[] $items */
	public function setMarkets(array $items) : void{
		$this->markets = [];
		foreach($items as $index => $item){
			if($item->getNamedTagEntry("marketId") === null){
				continue;
			}
			$market = BixbyMarket::getInstance()->getMarketManager()->getMarketById($item->getNamedTagEntry("marketId")->getValue());
			if($market === null){
				continue;
			}
			$this->markets[$index] = $market;
		}
	}

	public function getMarketByIndex(int $index) : ?Market{
		return $this->markets[$index] ?? null;
	}

	public function getIndexByMarket(Market $market) : ?int{
		foreach($this->markets as $index => $m){
			if($m->getId() === $market->getId()){
				return $index;
			}
		}
		return null;
	}

	public function setMarketIndex(int $index, ?Market $market) : void{
		if($market === null){
			if(isset($this->markets[$index])){
				unset($this->markets[$index]);
			}
		}else{
			$this->markets[$index] = $market;
		}
	}

	public function jsonSerialize() : array{
		$res = [];
		foreach($this->markets as $index => $market){
			$res[$index] = $market->getId();
		}
		return [
			"name" => $this->name,
			"markets" => $res
		];
	}

	public static function jsonDeserialize(array $data) : Category{
		return new Category($data["name"], $data["markets"]);
	}
}