<?php
namespace Opencart\System\Library\Cache;
class APCu {
	private int $expire;
	private bool $active;

	/**
	 * Constructor
	 *
	 * @param    int  $expire
	 */
	public function __construct(int $expire = 3600) {
		$this->expire = $expire;
		$this->active = function_exists('apcu_cache_info') && ini_get('apc.enabled');
	}

	/**
     * Get
     *
     * @param	 string	 $key
	 * 
	 * @return	 array|string|null
     */
	public function get(string $key): array|string|null {
		return $this->active ? apcu_fetch(CACHE_PREFIX . $key) : [];
	}

	/**
     * Set
     *
     * @param	 string	 $key
	 * @param	 array|string|null  $key
	 * 
	 * @return	 void
     */
	public function set(string $key, array|string|null $value, int $expire = 0): void {
		if (!$expire) {
			$expire = $this->expire;
		}

		if ($this->active) {
			apcu_store(CACHE_PREFIX . $key, $value, $expire);
		}
	}

	/**
     * Delete
     *
     * @param	 string	 $key
	 * 
	 * @return	 void
     */
	public function delete(string $key): void {
		if ($this->active) {
			$cache_info = apcu_cache_info();

			$cache_list = $cache_info['cache_list'];

			foreach ($cache_list as $entry) {
				if (strpos($entry['info'], CACHE_PREFIX . $key) === 0) {
					apcu_delete($entry['info']);
				}
			}
		}
	}

	/**
     * Delete all cache
     *
     * @param	 null
	 * 
	 * @return	 bool
     */
	public function flush(): bool {
		$status = false;

		if (function_exists('apcu_clear_cache')) {
			$status = apcu_clear_cache();
		}

		return $status;
	}
}
