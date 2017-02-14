<?php declare(strict_types=1);

namespace App\Modules\Core\Utils;


class Collection
{
	/**
	 * Get one level array of nested values
	 */
	public static function getNestedValues(array $array): array
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


	public static function prefix(array $array, string $prefix): array
	{
		foreach ($array as &$item) {
			$item = $prefix . $item;
		}

		return $array;
	}
}
