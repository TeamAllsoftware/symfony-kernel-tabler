<?php

namespace Allsoftware\SymfonyKernelTabler\Helper;

use DateTime;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

class GlobalHelper
{
    const CST_Date_Format_Fr = "d/m/Y";
    const CST_Date_Format_En = "m/d/Y";

    const CST_DateTime_Format_Fr = self::CST_Date_Format_Fr . " " . self::CST_Time_Format;
    const CST_DateTime_Format_En = self::CST_Date_Format_En . " " . self::CST_Time_Format;

    const CST_Time_Format = "H:i:s";

    /**
     * @throws Exception
     */
    #[ArrayShape(['min' => "\DateTime", 'max' => "\DateTime"])]
    public static function dateTime_fromDateRangePicker(string $dateRangeFormData): array
    {

        $string_dateTimes_fr = explode(' - ', $dateRangeFormData);

        $min = new DateTime();
        $max = new DateTime();

        foreach ($string_dateTimes_fr as $index => $string_dateTime_fr) {
            $dateTime_en = self::str_toDateTime($string_dateTime_fr);
            if ($index === 0) {
                $min = $dateTime_en;
            } else {
                $time    = $dateTime_en->format(self::CST_Time_Format);
                $hasTime = false;
                foreach (explode(':', $time) as $int) {
                    if (intval($int) > 0) $hasTime = true;
                }
                if (!$hasTime) {
                    $max = $dateTime_en->add(new \DateInterval('PT23H59M59S'));
                } else {
                    $max = $dateTime_en;
                }
            }
        }

        return ['min' => $min, 'max' => $max];
    }

    /**
     * @throws Exception
     */
    private static function str_toDateTime(string $string_dateTime, string $from = "fr"): DateTime
    {
        $explode_dateTime = explode(' ', $string_dateTime);

        $format = match ($from) {
            "fr" => self::CST_DateTime_Format_Fr,
            "en" => self::CST_DateTime_Format_En,
            default => throw new Exception("Unhandled case"),
        };

        $string_date = $explode_dateTime[0];
        $string_time = $explode_dateTime[1] ?? '00:00:00';

        return DateTime::createFromFormat($format, $string_date . ' ' . $string_time);
    }

    public static function percent_add(float|int $percent, float|int $from): float|int
    {
        return $from + ($from * $percent / 100);
    }

    public static function percent_subtract(float|int $percent, float|int $from): float|int
    {
        return $from - ($from * $percent / 100);
    }

    public static function percent_getDiff(float|int $from, float|int $to): float|int
    {
        return $from * 100 / $to;
    }

    public static function str_stripAccents(string $str): string
    {
        return strtr(
            utf8_decode($str),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
        );
    }

    /**
     * @return string|string[]
     */
    public static function str_pascalize(string $str, string $separator = '_')
    {
        // Ex : PascalCase
        return str_replace($separator, '', ucwords($str, $separator));
    }

    /**
     * @return array|string|string[]
     */
    public static function str_camelize(string $str, string $separator = '_')
    {
        // Ex : camelCase
        return str_replace($separator, '', lcfirst(ucwords($str, $separator)));
    }

    public static function camelCase_To_UnderscoreCase(string $str): string
    {
        // Ex : camelCase => camel_case
        return self::camelCase_To($str, '_');
    }

    public static function camelCase_To_KebabCase(string $str): string
    {
        // Ex : camelCase => camel-case
        return self::camelCase_To($str, '-');
    }

    private static function camelCase_To(string $str, string $replacement)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', $replacement . '$0', $str)), $replacement);
    }


    public static function zeroIfNull($val): mixed
    {
        if ($val === null) return 0;
        else return $val;
    }

    public static function str_cleanSpecialCharacters(string $string): string
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    public static function array_mergeRecursiveOverride(array ...$arrays): array
    {
        $last_merged = [];
        foreach ($arrays as $index => $array) {
            if ($index === 0) {
                $last_merged = $array;
                continue;
            }

            $last_merged = self::_array_mergeRecursiveOverrideDouble($last_merged, $array);
        }

        return $last_merged;
    }

    private static function _array_mergeRecursiveOverrideDouble(array $array1, array $array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset ($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = self::_array_mergeRecursiveOverrideDouble($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    public static function array_findKeyFromValue(array $array, $value): string|int|null
    {
        $key = array_search($value, $array);

        return $key !== false ? $key : null;
    }
}
