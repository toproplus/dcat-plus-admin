<?php

namespace Dcat\Admin\Models;

use Dcat\Admin\Admin;
use Illuminate\Support\Facades\Cache;

trait MenuCache
{
    protected $cacheKey = 'dcat-admin-menus-%d-%s';

    protected static $_app = 'admin';

    public static function _setApp($name)
    {
        self::$_app = $name;
    }

    protected static function _config($key)
    {
        return config(sprintf('%s.%s',self::$_app, $key));
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     *
     * @param  \Closure  $builder
     * @return mixed
     */
    protected function remember(\Closure $builder)
    {
        if (! $this->enableCache()) {
            return $builder();
        }

        return $this->getStore()->remember($this->getCacheKey(), null, $builder);
    }

    /**
     * @return bool|void
     */
    public function flushCache()
    {
        if (! $this->enableCache()) {
            return;
        }

        return $this->getStore()->delete($this->getCacheKey());
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return sprintf($this->cacheKey, (int) static::withPermission(), Admin::app()->getName());
    }

    /**
     * @return bool
     */
    public function enableCache()
    {
        return self::_config('menu.cache.enable');
    }

    /**
     * Get cache store.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getStore()
    {
        return Cache::store(self::_config('menu.cache.store', 'file'));
    }
}
