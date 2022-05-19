<?php

class Format
{
    /**
     * Converts coins to som.
     * @param int|string $coins coins.
     * @return float coins converted to som.
     */
    public static function toSom($coins)
    {
        return 1 * $coins / 100;
    }

    /**
     * Converts som to coins.
     * @param float $amount
     * @return int
     */
    public static function toCoins($amount)
    {
        return round(1 * $amount * 100);
    }

    /**
     * Get current timestamp in seconds or milliseconds.
     * @param bool $milliseconds true - get timestamp in milliseconds, false - in seconds.
     * @return int current timestamp value
     */
    public static function timestamp($milliseconds = false)
    {
        if ($milliseconds) {
            return round(microtime(true) * 1000); // milliseconds
        }

        return time(); // seconds
    }

    /**
     * Converts timestamp value from milliseconds to seconds.
     * @param int $timestamp timestamp in milliseconds.
     * @return int timestamp in seconds.
     */
    public static function timestamp2seconds($timestamp)
    {
        // is it already as seconds
        if (strlen((string)$timestamp) == 10) {
            return $timestamp;
        }

        return floor(1 * $timestamp / 1000);
    }

    /**
     * Converts timestamp value from seconds to milliseconds.
     * @param int $timestamp timestamp in seconds.
     * @return int timestamp in milliseconds.
     */
    public static function timestamp2milliseconds($timestamp)
    {
        // is it already as milliseconds
        if (strlen((string)$timestamp) == 13) {
            return $timestamp;
        }

        return $timestamp * 1000;
    }

    /**
     * Converts timestamp to date time string.
     * @param int $timestamp timestamp value as seconds or milliseconds.
     * @return string string representation of the timestamp value in 'Y-m-d H:i:s' format.
     */
    public static function timestamp2datetime($timestamp)
    {
        // if as milliseconds, convert to seconds
        if (strlen((string)$timestamp) == 13) {
            $timestamp = self::timestamp2seconds($timestamp);
        }

        // convert to datetime string
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * Converts date time string to timestamp value.
     * @param string $datetime date time string.
     * @return int timestamp as seconds.
     */
    public static function datetime2timestamp($datetime)
    {
        if ($datetime) {
            return strtotime($datetime);
        }

        return $datetime;
    }

}