<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\category;

use alvin0319\BixbyMarket\BixbyMarket;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;

final class CategoryManager{
	/** @var Category[] */
	protected array $categories = [];

	public function __construct(){
		if(!file_exists($file = BixbyMarket::getInstance()->getDataFolder() . "categories.json")){
			return;
		}
		$categories = json_decode(file_get_contents($file), true);
		foreach($categories as $categoryIndex => $categoryData){
			$category = Category::jsonDeserialize($categoryData);
			$this->categories[$categoryIndex] = $category;
		}
	}

	public function addCategory(int $index, Category $category) : void{
		$this->categories[$index] = $category;
	}

	public function getCategory(string $name) : ?Category{
		foreach($this->categories as $index => $category){
			if($category->getName() === $name){
				return $category;
			}
		}
		return null;
	}

	public function getCategoryIndex(Category $category) : ?int{
		foreach($this->categories as $index => $c){
			if($category->getName() === $c->getName()){
				return $index;
			}
		}
		return null;
	}

	public function removeCategory(Category $category) : void{
		$index = $this->getCategoryIndex($category);
		if($index !== null){
			unset($this->categories[$index]);
		}
	}

	/** @param Category[] $categories */
	public function setCategories(array $categories) : void{
		$this->categories = $categories;
	}

	/** @return Category[] */
	public function getCategories() : array{
		return $this->categories;
	}

	public function getAvailableIndex() : ?int{
		for($i = 0; $i < 54; $i++){
			if(!isset($this->categories[$i])){
				return $i;
			}
		}
		return null;
	}

	public function save() : void{
		$res = [];
		foreach($this->categories as $index => $category){
			$res[$index] = $category->jsonSerialize();
		}
		file_put_contents(BixbyMarket::getInstance()->getDataFolder() . "categories.json", json_encode($res, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
	}
}