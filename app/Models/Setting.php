<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Tunable platform parameters an administrator manages, as key/value rows.
 * Reads are cached so the exam engine and generators can call get() freely.
 *
 * @property string $key
 * @property string $value
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /**
     * The raw string value for a key, or the default when it is not set.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = Cache::rememberForever(
            "setting.{$key}",
            fn (): ?string => static::query()->where('key', $key)->value('value'),
        );

        return $value ?? $default;
    }

    /**
     * The value for a key as an integer.
     */
    public static function int(string $key, int $default): int
    {
        $value = static::get($key);

        return $value === null ? $default : (int) $value;
    }

    /**
     * Store a value and drop its cache entry.
     */
    public static function set(string $key, string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }
}
