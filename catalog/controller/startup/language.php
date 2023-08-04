<?php
namespace Opencart\Catalog\Controller\Startup;
class Language extends \Opencart\System\Engine\Controller {
	private static array $languages = [];

	public function index(): void {
		if (isset($this->request->get['language'])) {
			$code = (string)$this->request->get['language'];
		} else {
			$code = $this->config->get('config_language');
		}

		$this->load->model('localisation/language');

		self::$languages = $this->model_localisation_language->getLanguages();

		if (isset(self::$languages[$code])) {
			$language_info = self::$languages[$code];

			// If extension switch add language directory
			if ($language_info['extension']) {
				$this->language->addPath('extension/' . $language_info['extension'], DIR_EXTENSION . $language_info['extension'] . '/catalog/language/');
			}

			// Set the config language_id key
			$this->config->set('config_language_id', $language_info['language_id']);
			$this->config->set('config_language', $language_info['code']);

			$this->load->language('default');
		}
	}
	
	// Override the language default values
	public function after(&$route, &$prefix, &$code, &$output): void {
		if (!$code) {
			$code = $this->config->get('config_language');
		}

		// Use $this->language->load so it's not triggering infinite loops
		$this->language->load($route, $prefix, $code);

		if (isset(self::$languages[$code])) {
			$language_info = self::$languages[$code];

			$path = '';

			if ($language_info['extension']) {
				$extension = 'extension/' . $language_info['extension'];

				if (oc_substr($route, 0, strlen($extension)) != $extension) {
					$path = $extension . '/';
				}
			}

			// Use $this->language->load so it's not triggering infinite loops
			$this->language->load($path . $route, $prefix, $code);
		}
	}
}
