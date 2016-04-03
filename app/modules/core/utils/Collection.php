<?php

namespace App\Modules\Core\Utils;


class Collection
{
	/**
	 * Get one level array of nested values
	 * @param array $array
	 * @return array
	 */
	public static function getNestedValues($array)
	{
		$values = [];
		foreach ($array as $value) {
			foreach ($value as $item) {
				$values[] = $item;
			}
		}
		return $values;
	}
}