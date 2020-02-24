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
        return self::where('key', $this->buildKey($key, $scope))->exists();
    }

    /**
     * Get the specified option value.
     *
     * @param string $key
     * @param string $scope
     * @param mixed $default
     * @return mixed
     */
    function get($key, $default = null, $scope = self::SCOPE_DEFAULT)
    {
        if ($key === '*') {
            return self::where('key', 'LIKE', "$scope.%")->get()->mapWithKeys(
                function ($item) use ($scope) {
                    return [preg_replace("/^$scope./", '', $item['key']) => unserialize($item['value'])];
                }
            )->toArray();
        }

        if ($option = Option::where('key', $this->buildKey($key, $scope)->first())) {
            return unserialize($option->value);
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
    function set($key, $value = null, $scope = self::SCOPE_DEFAULT)
    {
        if (is_array($key) && func_num_args() == 2) {
            foreach ($key as $keyName => $keyVal) {
                Option::updateOrCreate(
                    ['key' => $this->buildKey($keyName, $scope = $value)],
                    ['value' => serialize($keyVal)]
                );
            }
        } else {
            Option::updateOrCreate(['key' => $this->buildKey($key, $scope)], ['value' => serialize($value)]);
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
        return (bool)self::where('key', $this->buildKey($key, $scope))->delete();
    }


    /**
     * @param $key
     * @param string $scope
     * @return string
     */
    protected function buildKey($key, $scope = self::SCOPE_DEFAULT)
    {
        return $scope . '.' . $key;
    }

}
