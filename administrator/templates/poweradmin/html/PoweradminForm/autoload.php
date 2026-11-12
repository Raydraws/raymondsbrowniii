<?php
/**
 * Simple PSR-4 style autoloader for the Poweradmin form namespace.
 *
 * @package     Poweradmin.Administrator
 */

defined('_JEXEC') or die;

spl_autoload_register(
	static function ($class) {
		$prefix  = 'Poweradmin\\Admin\\Form\\';
		$baseDir = __DIR__ . '/';

		if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
			return;
		}

		$relativeClass = substr($class, strlen($prefix));
		$file          = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

		if (is_file($file)) {
			require_once $file;
		}
	},
	true,
	true
);

