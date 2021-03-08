<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\util;

use BadMethodCallException;
use InvalidArgumentException;
use function count;
use function get_class;
use function strtolower;
use function strtoupper;

/**
 * @method static MarketSellResult SUCCESS()
 * @method static MarketSellResult NO_ITEM()
 * @method static MarketSellResult NOT_SELLABLE()
 * @method static MarketSellResult PLUGIN_CANCEL()
 */

final class MarketSellResult{
	/** @var MarketSellResult[] */
	private static array $registries = [];

	private static function lazyInit() : void{
		if(count(self::$registries) === 0){
			self::registerAll(
				new self("success"),
				new self("no_item"),
				new self("not_sellable"),
				new self("plugin_cancel")
			);
		}
	}

	private static function register(MarketSellResult $result) : void{
		self::$registries[$result->name()] = $result;
	}

	private static function registerAll(MarketSellResult ...$results) : void{
		foreach($results as $result)
			self::register($result);
	}

	public static function __callStatic(string $name, array $arguments = []) : MarketSellResult{
		self::lazyInit();
		if(!isset(self::$registries[strtolower($name)])){
			throw new BadMethodCallException("Call to undefined static method " . get_class(self::class) . "::" . strtoupper($name) . "()");
		}
		if(count($arguments) > 0){
			throw new InvalidArgumentException("Invalid argument given");
		}
		return self::$registries[strtolower($name)];
	}

	protected string $name;

	public function __construct(string $name){
		$this->name = $name;
	}

	public function name() : string{
		return $this->name;
	}

	public function equals(MarketSellResult $that) : bool{
		return $this->name === $that->name;
	}
}