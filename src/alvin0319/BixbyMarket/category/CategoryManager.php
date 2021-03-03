<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\category;

use function array_map;

final class CategoryManager{
	/** @var Category[] */
	protected array $categories = [];

	public function __construct(array $categories){
		foreach($categories as $categoryData){
			$category = Category::jsonDeserialize($categoryData);
			$this->categories[$category->getName()] = $category;
		}
	}
}