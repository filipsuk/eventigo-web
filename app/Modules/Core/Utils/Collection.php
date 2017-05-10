<?php declare(strict_types=1);

namespace App\Modules\Core\Utils;

final class Collection
{
    /**
     * Get one level array of nested values.
     *
     * @param mixed[] $array
     * @return mixed[]
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

    /**
     * @param mixed[] $array
     * @return mixed[]
     */
    public static function prefix(array $array, string $prefix): array
    {
        foreach ($array as &$item) {
            $item = $prefix . $item;
        }

        return $array;
    }
}
