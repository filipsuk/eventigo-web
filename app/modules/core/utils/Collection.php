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
			if (is_array($value)) {
				foreach ($value as $item) {
					$values[] = $item;
				}
			} else {
				$values[] = $value;
			}
		}
		return $values;
	}


	/**
	 * @param array $array
	 * @param string $prefix
	 * @return array
	 */
	public static function prefix(array $array, $prefix)
	{
		foreach ($array as &$item) {
			$item = $prefix . $item;
		}

		return $array;
	}
}
