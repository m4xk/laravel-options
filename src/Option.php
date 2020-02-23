<?php

namespace Appstract\Options;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{

    const SCOPE_DEFAULT = 'default';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var [type]
     */
    protected $fillable = [
        'key',
        'value',
        'scope'
    ];

    /**
     * Determine if the given option value exists.
     *
     * @param string $key
     * @param string $scope
     * @return bool
     */
    public function exists($key, $scope = self::SCOPE_DEFAULT)
    {
        return self::where('key', $key)->where('scope', $scope)->exists();
    }

    /**
     * Get the specified option value.
     *
     * @param string $key
     * @param string $scope
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null, $scope = self::SCOPE_DEFAULT)
    {
        if ($key === '*') {
            return self::where('scope', $scope)->get()->mapWithKeys(
                function ($item) {
                    return [$item['key'] => $item['value']];
                }
            )->toArray();
        }

        if ($option = self::where('key', $key)->where('scope', $scope)->first()) {
            return $option->value;
        }

        return $default;
    }

    /**
     * Set a given option value.
     *
     * @param array|string $key
     * @param mixed $value
     * @param string $scope
     * @return void
     */
    public function set($key, $value = null, $scope = self::SCOPE_DEFAULT)
    {
        if (is_array($key) && func_num_args() == 2) {
            foreach ($key as $keyName => $keyVal) {
                self::updateOrCreate(['key' => $key, 'scope' => $scope = $value], ['value' => $keyVal]);
            }
        } else {
            self::updateOrCreate(['key' => $key, 'scope' => $scope], ['value' => $value]);
        }
    }

    /**
     * Remove/delete the specified option value.
     *
     * @param string $key
     * @param string $scope
     * @return bool
     */
    public function remove(
        $key,
        $scope = self::SCOPE_DEFAULT
    ) {
        return (bool)self::where('key', $key)->where('scope', $scope)->delete();
    }
}
