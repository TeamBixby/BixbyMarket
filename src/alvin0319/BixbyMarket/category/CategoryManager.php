<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\category;

final class CategoryManager{
	/** @var Category[] */
	protected array $categories = [];

	public function __construct(array $categories){
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
}