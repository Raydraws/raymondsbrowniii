<?php
/**
 * @package     Poweradmin
 * @subpackage  Templates.Poweradmin
 *
 * @copyright   (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

/**
 * Parse HSL color string
 *
 * @param   string  $hsl  HSL color string (e.g., "hsl(207, 61, 26)" or "hsla(207, 61, 26, 1)")
 *
 * @return  array|null  Array with 'h', 's', 'l' keys or null on failure
 *
 * @since  4.0.0
 */
function parseHsl($hsl)
{
	// Remove "hsl(" or "hsla(" prefix
	$hsl = preg_replace('/^hsla?\(/i', '', trim($hsl));
	// Remove closing ")"
	$hsl = rtrim($hsl, ')');
	
	// Split by comma
	$parts = array_map('trim', explode(',', $hsl));
	
	if (count($parts) < 3)
	{
		return null;
	}
	
	return [
		'h' => (float) trim($parts[0]),
		's' => (float) rtrim(trim($parts[1]), '%'),
		'l' => (float) rtrim(trim($parts[2]), '%')
	];
}

/**
 * Parse RGB color string
 *
 * @param   string  $rgb  RGB color string (e.g., "rgb(255, 128, 0)")
 *
 * @return  array|null  Array with 'r', 'g', 'b' keys or null on failure
 *
 * @since  4.0.0
 */
function parseRgb($rgb)
{
	// Remove "rgb(" and closing ")"
	$rgb = preg_replace('/^rgb\(/i', '', trim($rgb));
	$rgb = rtrim($rgb, ')');
	
	// Split by comma
	$parts = array_map('trim', explode(',', $rgb));
	
	if (count($parts) < 3)
	{
		return null;
	}
	
	return [
		'r' => (int) trim($parts[0]),
		'g' => (int) trim($parts[1]),
		'b' => (int) trim($parts[2])
	];
}

/**
 * Convert hex color to RGB
 *
 * @param   string  $hex  Hex color string (e.g., "#ff8000" or "#f80")
 *
 * @return  array|null  Array with 'r', 'g', 'b' keys or null on failure
 *
 * @since  4.0.0
 */
function hexToRgb($hex)
{
	$hex = ltrim($hex, '#');
	
	if (strlen($hex) == 3)
	{
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	
	if (strlen($hex) != 6 || !ctype_xdigit($hex))
	{
		return null;
	}
	
	return [
		'r' => hexdec(substr($hex, 0, 2)),
		'g' => hexdec(substr($hex, 2, 2)),
		'b' => hexdec(substr($hex, 4, 2))
	];
}

/**
 * Convert RGB to HSL
 *
 * @param   int  $r  Red value (0-255)
 * @param   int  $g  Green value (0-255)
 * @param   int  $b  Blue value (0-255)
 *
 * @return  array  Array with 'h', 's', 'l' keys
 *
 * @since  4.0.0
 */
function rgbToHsl($r, $g, $b)
{
	$r /= 255;
	$g /= 255;
	$b /= 255;
	
	$max = max($r, $g, $b);
	$min = min($r, $g, $b);
	$delta = $max - $min;
	
	$l = ($max + $min) / 2;
	
	if ($delta == 0)
	{
		$h = $s = 0;
	}
	else
	{
		$s = $l > 0.5 ? $delta / (2 - $max - $min) : $delta / ($max + $min);
		
		if ($max == $r)
		{
			$h = (($g - $b) / $delta + ($g < $b ? 6 : 0)) / 6;
		}
		elseif ($max == $g)
		{
			$h = (($b - $r) / $delta + 2) / 6;
		}
		else
		{
			$h = (($r - $g) / $delta + 4) / 6;
		}
	}
	
	return [
		'h' => round($h * 360),
		's' => round($s * 100),
		'l' => round($l * 100)
	];
}

/**
 * Convert HSL to RGB
 *
 * @param   float  $h  Hue (0-360)
 * @param   float  $s  Saturation (0-100)
 * @param   float  $l  Lightness (0-100)
 *
 * @return  array  Array with 'r', 'g', 'b' keys
 *
 * @since  4.0.0
 */
function hslToRgb($h, $s, $l)
{
	$h = $h / 360;
	$s = $s / 100;
	$l = $l / 100;
	
	if ($s == 0)
	{
		$r = $g = $b = $l;
	}
	else
	{
		$hue2rgb = function($p, $q, $t)
		{
			if ($t < 0) $t += 1;
			if ($t > 1) $t -= 1;
			if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
			if ($t < 1/2) return $q;
			if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
			return $p;
		};
		
		$q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
		$p = 2 * $l - $q;
		
		$r = $hue2rgb($p, $q, $h + 1/3);
		$g = $hue2rgb($p, $q, $h);
		$b = $hue2rgb($p, $q, $h - 1/3);
	}
	
	return [
		'r' => round($r * 255),
		'g' => round($g * 255),
		'b' => round($b * 255)
	];
}

/**
 * Convert RGB to hex
 *
 * @param   int  $r  Red value (0-255)
 * @param   int  $g  Green value (0-255)
 * @param   int  $b  Blue value (0-255)
 *
 * @return  string  Hex color string (e.g., "#ff8000")
 *
 * @since  4.0.0
 */
function rgbToHex($r, $g, $b)
{
	return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) .
		str_pad(dechex($g), 2, '0', STR_PAD_LEFT) .
		str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

/**
 * Lighten HSL color
 *
 * @param   float  $h       Hue (0-360)
 * @param   float  $s       Saturation (0-100)
 * @param   float  $l       Lightness (0-100)
 * @param   float  $amount  Amount to lighten (percentage)
 *
 * @return  array  Array with 'h', 's', 'l' keys
 *
 * @since  4.0.0
 */
function lightenHsl($h, $s, $l, $amount)
{
	$l = min(100, max(0, $l + $amount));
	return ['h' => $h, 's' => $s, 'l' => $l];
}

/**
 * Darken HSL color
 *
 * @param   float  $h       Hue (0-360)
 * @param   float  $s       Saturation (0-100)
 * @param   float  $l       Lightness (0-100)
 * @param   float  $amount  Amount to darken (percentage)
 *
 * @return  array  Array with 'h', 's', 'l' keys
 *
 * @since  4.0.0
 */
function darkenHsl($h, $s, $l, $amount)
{
	return lightenHsl($h, $s, $l, -$amount);
}

/**
 * Desaturate HSL color
 *
 * @param   float  $h       Hue (0-360)
 * @param   float  $s       Saturation (0-100)
 * @param   float  $l       Lightness (0-100)
 * @param   float  $amount  Amount to desaturate (percentage)
 *
 * @return  array  Array with 'h', 's', 'l' keys
 *
 * @since  4.0.0
 */
function desaturateHsl($h, $s, $l, $amount)
{
	$s = min(100, max(0, $s - $amount));
	return ['h' => $h, 's' => $s, 'l' => $l];
}

/**
 * Spin HSL color (adjust hue)
 *
 * @param   float  $h       Hue (0-360)
 * @param   float  $s       Saturation (0-100)
 * @param   float  $l       Lightness (0-100)
 * @param   float  $amount  Amount to spin (degrees)
 *
 * @return  array  Array with 'h', 's', 'l' keys
 *
 * @since  4.0.0
 */
function spinHsl($h, $s, $l, $amount)
{
	$h = fmod($h + $amount, 360);
	if ($h < 0) $h += 360;
	return ['h' => $h, 's' => $s, 'l' => $l];
}

/**
 * Extract hue from color string
 *
 * @param   string  $color  Color string in any format (hex, hsl, hsla, rgb)
 *
 * @return  float|null  Hue value (0-360) or null on failure
 *
 * @since  4.0.0
 */
function getColorHue($color)
{
	if (strpos($color, 'hsl(') !== false)
	{
		$hsl = parseHsl($color);
		return $hsl ? $hsl['h'] : null;
	}
	elseif (strpos($color, 'hsla(') !== false)
	{
		$hsl = parseHsl($color);
		return $hsl ? $hsl['h'] : null;
	}
	elseif (substr($color, 0, 1) === '#')
	{
		$rgb = hexToRgb($color);
		if (!$rgb) return null;
		$hsl = rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
		return $hsl['h'];
	}
	elseif (strpos($color, 'rgb(') !== false)
	{
		$rgb = parseRgb($color);
		if (!$rgb) return null;
		$hsl = rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
		return $hsl['h'];
	}
	
	return null;
}

/**
 * Convert hex color to HSL
 *
 * @param   string  $hex  Hex color string
 *
 * @return  array|null  Array with 'h', 's', 'l' keys or null on failure
 *
 * @since  4.0.0
 */
function hexToHsl($hex)
{
	$rgb = hexToRgb($hex);
	if (!$rgb) return null;
	return rgbToHsl($rgb['r'], $rgb['g'], $rgb['b']);
}

/**
 * Convert HSL to hex
 *
 * @param   float  $h  Hue (0-360)
 * @param   float  $s  Saturation (0-100)
 * @param   float  $l  Lightness (0-100)
 *
 * @return  string  Hex color string
 *
 * @since  4.0.0
 */
function hslToHex($h, $s, $l)
{
	$rgb = hslToRgb($h, $s, $l);
	return rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
}

/**
 * Lighten hex color
 *
 * @param   string  $hex     Hex color string
 * @param   float   $amount  Amount to lighten (percentage)
 *
 * @return  string  Lightened hex color string
 *
 * @since  4.0.0
 */
function lightenHex($hex, $amount)
{
	$hsl = hexToHsl($hex);
	if (!$hsl) return $hex;
	$hsl = lightenHsl($hsl['h'], $hsl['s'], $hsl['l'], $amount);
	return hslToHex($hsl['h'], $hsl['s'], $hsl['l']);
}

/**
 * Darken hex color
 *
 * @param   string  $hex     Hex color string
 * @param   float   $amount  Amount to darken (percentage)
 *
 * @return  string  Darkened hex color string
 *
 * @since  4.0.0
 */
function darkenHex($hex, $amount)
{
	return lightenHex($hex, -$amount);
}

/**
 * Template Poweradmin HTML Helper
 *
 * @since  4.0.0
 */
class JHtmlPoweradmin
{
	/**
	 * Render an inline SVG icon by name.
	 *
	 * @param   string       $name       Icon name (file name without .svg).
	 * @param   int|string   $size       Optional icon size (sets width + height).
	 * @param   string|null  $fill       Optional fill color to apply to non-none fills.
	 * @param   array        $attributes Optional additional SVG attributes.
	 *
	 * @return  string
	 *
	 * @since  4.0.0
	 */
	public static function icon(string $name, $size = null, ?string $fill = null, array $attributes = []): string
	{
		$icons = static::getIcons();
		$key   = static::normalizeIconName($name);

		if (!isset($icons[$key]))
		{
			return '';
		}

		$icon    = $icons[$key];
		$viewBox = $icon['viewBox'] ?? '0 0 24 24';
		$markup  = $icon['icon'] ?? '';

		if ($fill !== null && $fill !== '')
		{
			$markup = preg_replace('/fill="(?!none)[^"]*"/i', 'fill="' . htmlspecialchars($fill, ENT_QUOTES, 'UTF-8') . '"', $markup);
		}

		$svgAttributes = [
			'viewBox'  => $viewBox,
			'fill'     => 'none',
			'xmlns'    => 'http://www.w3.org/2000/svg',
			'aria-hidden' => 'true',
			'focusable' => 'false',
		];

		if ($size !== null && $size !== '')
		{
			$svgAttributes['width']  = (string) $size;
			$svgAttributes['height'] = (string) $size;
		}

		foreach ($attributes as $attribute => $value)
		{
			if ($value === null || $value === false)
			{
				continue;
			}

			$svgAttributes[(string) $attribute] = $value === true ? (string) $attribute : (string) $value;
		}

		return '<svg ' . static::buildAttributeString($svgAttributes) . '>' . $markup . '</svg>';
	}

	/**
	 * Returns icon registry keyed by normalized icon name.
	 *
	 * @return  array
	 */
	protected static function getIcons(): array
	{
		static $icons = null;

		if ($icons !== null)
		{
			return $icons;
		}

		$data = include __DIR__ . '/icons.php';
		$icons = [];

		foreach ($data as $name => $icon)
		{
			$icons[static::normalizeIconName((string) $name)] = $icon;
		}

		return $icons;
	}

	/**
	 * Normalize icon names so lookup supports mixed separators/case.
	 *
	 * @param   string  $name  Raw icon name.
	 *
	 * @return  string
	 */
	protected static function normalizeIconName(string $name): string
	{
		$name = strtolower($name);
		$name = str_replace(['_', ' '], '-', $name);

		return preg_replace('/-+/', '-', $name);
	}

	/**
	 * Build a sanitized HTML attribute string.
	 *
	 * @param   array  $attributes  Key/value attributes.
	 *
	 * @return  string
	 */
	protected static function buildAttributeString(array $attributes): string
	{
		$result = [];

		foreach ($attributes as $attribute => $value)
		{
			if ($value === null || $value === false)
			{
				continue;
			}

			$key = preg_replace('/[^a-zA-Z0-9_:-]/', '', (string) $attribute);

			if ($key === '')
			{
				continue;
			}

			$result[] = $key . '="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"';
		}

		return implode(' ', $result);
	}

	/**
	 * Active administrator template style params (current template).
	 *
	 * @return  Registry
	 *
	 * @since   4.0.0
	 */
	public static function getActiveTemplateParams(): Registry
	{
		$app = Factory::getApplication();

		if (!\is_callable([$app, 'getTemplate']))
		{
			return new Registry();
		}

		$tpl = $app->getTemplate(true);

		if (!\is_object($tpl))
		{
			return new Registry();
		}

		$params = $tpl->params ?? null;

		if ($params instanceof Registry)
		{
			return $params;
		}

		if (\is_string($params))
		{
			return new Registry($params);
		}

		if (\is_array($params))
		{
			return new Registry($params);
		}

		return new Registry();
	}

	/**
	 * Resolve dark mode value from template style params.
	 *
	 * `poweradmin_dark_mode` supports:
	 * - `1`: force dark mode
	 * - `0`: force light mode
	 * - `-1` (or unset): follow OS/browser scheme
	 *
	 * @param   Registry  $params  Template params.
	 *
	 * @return  bool
	 *
	 * @since   4.0.0
	 */
	protected static function resolveDarkMode(Registry $params): bool
	{
		$raw = $params->get('poweradmin_dark_mode', '-1');

		if (\is_bool($raw))
		{
			return $raw;
		}

		$rawString = trim((string) $raw);

		if ($rawString === '1')
		{
			return true;
		}

		if ($rawString === '0')
		{
			return false;
		}

		return static::prefersDarkSchemeFromRequest();
	}

	/**
	 * Best-effort dark scheme detection from current request.
	 *
	 * Uses (in order):
	 * 1. Client Hint header: `Sec-CH-Prefers-Color-Scheme`
	 * 2. Atum-compatible cookie fallback: `osColorScheme`
	 *
	 * @return  bool
	 *
	 * @since   4.0.0
	 */
	protected static function prefersDarkSchemeFromRequest(): bool
	{
		$app = Factory::getApplication();

		// if (!isset($app->input))
		// {
		// 	return false;
		// }

		$hint = strtolower((string) $app->input->server->get('HTTP_SEC_CH_PREFERS_COLOR_SCHEME', '', 'string'));

		if ($hint === 'dark')
		{
			return true;
		}

		if ($hint === 'light')
		{
			return false;
		}

		$cookie = strtolower((string) $app->input->cookie->get('osColorScheme', '', 'string'));

		if ($cookie === 'dark')
		{
			return true;
		}

		if ($cookie === 'light')
		{
			return false;
		}

		// Safe fallback when no OS hint is available in the request.
		return false;
	}

	/**
	 * Whether dark mode is enabled via template style param `poweradmin_dark_mode` (global for this style).
	 *
	 * @return  bool
	 *
	 * @since   4.0.0
	 */
	public static function isDarkMode(): bool
	{
		return static::resolveDarkMode(static::getActiveTemplateParams());
	}

	/**
	 * Calculates the different template colors and set the CSS variables
	 *
	 * @param   Registry   $params    Template parameters.
	 *
	 * @return void
	 *
	 * @since  4.0.0
	 */
	public static function rootcolors(Registry $params): void
	{
		$root       = [];
		$darkMode   = static::resolveDarkMode($params);

		if ($params->get('hue'))
		{
			$root = static::bgdarkcalc($params->get('hue'), $darkMode);
		}

		$bgLight = $params->get('bg-light');

		if (static::isHex($bgLight))
		{
			try
			{
				// In dark mode, use dark background from bgdarkcalc if available
				if ($darkMode && $params->get('hue'))
				{
					// Use --poweradmin-bg-dark-10 or similar for main background in dark mode
					// This will be set by bgdarkcalc, we just need to override bg-light
					// $root[] = '--poweradmin-bg-light: var(--poweradmin-bg-dark-10, ' . $bgLight . ');';
					$root[] = '--toolbar-bg: var(--poweradmin-bg-dark-20, ' . lightenHex($bgLight, 5) . ');';
					$root[] = '--white: var(--poweradmin-bg-dark-10, ' . lightenHex($bgLight, 5) . ');';
					// For RGB, we'll use the dark variant if available
					$rgb = hexToRgb($bgLight);
					if ($rgb)
					{
						$root[] = '--bg-rgb: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
					}
				}
				else
				{
					$root[] = '--poweradmin-bg-light: ' . $bgLight . ';';
					$root[] = '--toolbar-bg: ' . lightenHex($bgLight, 5) . ';';
					// Convert hex to RGB format (r, g, b) for --bg-rgb
					$rgb = hexToRgb($bgLight);
					if ($rgb)
					{
						$root[] = '--bg-rgb: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
					}
				}
			}
			catch (\Exception $ex)
			{
				// Just ignore exceptions
			}
		}

		$textDark = $params->get('text-dark');

		if (static::isHex($textDark))
		{
			try
			{
				// In dark mode, use light text color
				if ($darkMode)
				{
					$textLight = $params->get('text-light');
					if (static::isHex($textLight))
					{
						$root[] = '--poweradmin-text-dark: ' . $textLight . ';';
						$rgb = hexToRgb($textLight);
						if ($rgb)
						{
							$root[] = '--primary-text-rgb: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
						}
					}
					else
					{
						// Fallback: use lightened version of dark text
						$lightText = lightenHex($textDark, 80);
						$root[] = '--poweradmin-text-dark: ' . $lightText . ';';
						$rgb = hexToRgb($lightText);
						if ($rgb)
						{
							$root[] = '--primary-text-rgb: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
						}
					}
				}
				else
				{
					$root[]   = '--poweradmin-text-dark: ' . $textDark . ';';
					// Convert hex to RGB format (r, g, b) for --primary-text-rgb
					$rgb = hexToRgb($textDark);
					if ($rgb)
					{
						$root[] = '--primary-text-rgb: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
					}
				}
			}
			catch (\Exception $ex)
			{
				// Just ignore exceptions
			}
		}

		$textLight = $params->get('text-light');

		if (static::isHex($textLight))
		{
			try
			{
				$root[]    = '--poweradmin-text-light: ' . $textLight . ';';
			}
			catch (\Exception $ex)
			{
				// Just ignore exceptions
			}
		}

		$linkColor = $params->get('link-color');

		if (static::isHex($linkColor))
		{
			try
			{
				$root[] = '--poweradmin-link-color: ' . $linkColor . ';';

                $root[] = '--poweradmin-link-hover-color: ' . darkenHex($linkColor, 20) . ';';
				
				// Convert hex to RGB format (r, g, b) for --primary-rgb and --primary-text-rgb
				$rgb = hexToRgb($linkColor);
				if ($rgb)
				{
					// $root[] = '--primary-rgb: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
					$root[] = '--primary-rgb: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
					$root[] = '--primary-light-rgb: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
					$root[] = '--btn-gradient-1: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
					$root[] = '--btn-gradient-2: ' . $rgb['r'] . ', ' . $rgb['g'] . ', ' . $rgb['b'] . ';';
				}
			}
			catch (\Exception $ex)
			{
				// Just ignore exceptions
			}
		}

		$specialColor = $params->get('special-color');

		if (static::isHex($specialColor))
		{
			try
			{
				$root[]       = '--poweradmin-special-color: ' . $specialColor . ';';
			}
			catch (\Exception $ex)
			{
				// Just ignore exceptions
			}
		}

		if (count($root))
		{
			$css = ':root {' . implode($root) . '}';

			
			// Add dark mode specific overrides
			if ($darkMode)
			{
				$css .= ' .poweradmin-dark-mode {';
				$css .= ' --poweradmin-box-shadow: 1px 1px 1px 0px rgba(0, 0, 0, 0.25);';
				$css .= ' --white: var(--poweradmin-bg-dark-20, #0E0E10);';
				$css .= ' --bg-color: var(--poweradmin-bg-dark-10, #1c1c1e);';
				$css .= ' --body-bg: var(--poweradmin-bg-dark-20, #212124);';
				$css .= ' --bg-dark-color: var(--poweradmin-bg-dark-0, #222224);';
				$css .= ' --bg-light-color: var(--poweradmin-bg-dark-10, #2a2a2a);';
				$css .= ' --table-row-bg: #212124;';
				$css .= ' --table-row-selected-color: #3f3f46;';
				$css .= ' --table-striped-row-color: #3f3f46;';
				$css .= ' --toolbar-bg: #242222;';
				$css .= ' --primary-lighter-color: #262628;';
				$css .= ' --icon-color: #bdc5c8;';
				$css .= ' --input-border-color: #7c7e82;';
				$css .= ' --border-color: #7c7e82;';
				$css .= ' --bg-toolbar-color: var(--white);';
				$css .= ' --secondary-icon-color: #3f3f46;';
				$css .= ' --secondary-text-alt-color: rgba(255, 255, 255, 0.8);';
				$css .= ' --border-global-color: #7c7e82;';
				$css .= ' --primary-text-rgb: 212, 211, 219;';
				$css .= ' }';
			}
			
			Factory::getDocument()->addStyleDeclaration($css);
		}
	}

	/**
	 * Calculates the different template colors
	 *
	 * @param   string   $color         Template parameter color.
	 * @param   boolean  $darkMode     Whether dark mode is enabled.
	 *
	 * @return  array  An array of calculated color values and css variables
	 *
	 * @since  4.0.0
	 */
	protected static function bgdarkcalc($color, $darkMode = false): array
	{
		$hue = 207;

		try
		{
			$extractedHue = getColorHue($color);
			if ($extractedHue !== null)
			{
				$hue = $extractedHue;
			}
		}
		catch (\Exception $ex)
		{
			// Just ignore exceptions
		}

		$hue  = min(359, max(0, (int) $hue));
		$root = [];

		// No need to calculate if we have the default value
		// if ($hue === 207)
		// {
			return $root;
		// }

		try
		{
			// Base HSL color: hsl(hue, 61, 26)
			$baseH = $hue;
			$baseS = 61;
			$baseL = 26;
			
			// In dark mode, use darker base for backgrounds
			if ($darkMode)
			{
				$baseL = 15; // Darker base lightness for dark mode
			}

			// --poweradmin-bg-dark: base color
			$root[] = '--poweradmin-bg-dark: ' . hslToHex($baseH, $baseS, $baseL) . ';';

			// --poweradmin-contrast: spin(-40), lighten(18)
			$contrast = spinHsl($baseH, $baseS, $baseL, -40);
			$contrast = lightenHsl($contrast['h'], $contrast['s'], $contrast['l'], 18);
			$root[] = '--poweradmin-contrast: ' . hslToHex($contrast['h'], $contrast['s'], $contrast['l']) . ';';

			// --poweradmin-bg-dark-0: desaturate(86), lighten(71.4), spin(-6)
			$dark0 = desaturateHsl($baseH, $baseS, $baseL, 86);
			$dark0 = lightenHsl($dark0['h'], $dark0['s'], $dark0['l'], 71.4);
			$dark0 = spinHsl($dark0['h'], $dark0['s'], $dark0['l'], -6);
			$root[] = '--poweradmin-bg-dark-0: ' . hslToHex($dark0['h'], $dark0['s'], $dark0['l']) . ';';

			// --poweradmin-bg-dark-5: desaturate(85), lighten(65.1), spin(-6)
			$dark5 = desaturateHsl($baseH, $baseS, $baseL, 85);
			$dark5 = lightenHsl($dark5['h'], $dark5['s'], $dark5['l'], 65.1);
			$dark5 = spinHsl($dark5['h'], $dark5['s'], $dark5['l'], -6);
			$root[] = '--poweradmin-bg-dark-5: ' . hslToHex($dark5['h'], $dark5['s'], $dark5['l']) . ';';

			// --poweradmin-bg-dark-10: desaturate(80), lighten(59.4), spin(-6)
			$dark10 = desaturateHsl($baseH, $baseS, $baseL, 80);
			$dark10 = lightenHsl($dark10['h'], $dark10['s'], $dark10['l'], 59.4);
			$dark10 = spinHsl($dark10['h'], $dark10['s'], $dark10['l'], -6);
			$root[] = '--poweradmin-bg-dark-10: ' . hslToHex($dark10['h'], $dark10['s'], $dark10['l']) . ';';

			// --poweradmin-bg-dark-20: desaturate(75), lighten(47.3), spin(-6)
			$dark20 = desaturateHsl($baseH, $baseS, $baseL, 75);
			$dark20 = lightenHsl($dark20['h'], $dark20['s'], $dark20['l'], 47.3);
			$dark20 = spinHsl($dark20['h'], $dark20['s'], $dark20['l'], -6);
			$root[] = '--poweradmin-bg-dark-20: ' . hslToHex($dark20['h'], $dark20['s'], $dark20['l']) . ';';

			// --poweradmin-bg-dark-30: desaturate(55), lighten(34.3), spin(-5)
			$dark30 = desaturateHsl($baseH, $baseS, $baseL, 55);
			$dark30 = lightenHsl($dark30['h'], $dark30['s'], $dark30['l'], 34.3);
			$dark30 = spinHsl($dark30['h'], $dark30['s'], $dark30['l'], -5);
			$root[] = '--poweradmin-bg-dark-30: ' . hslToHex($dark30['h'], $dark30['s'], $dark30['l']) . ';';

			// --poweradmin-bg-dark-40: desaturate(40), lighten(21.4), spin(-3)
			$dark40 = desaturateHsl($baseH, $baseS, $baseL, 40);
			$dark40 = lightenHsl($dark40['h'], $dark40['s'], $dark40['l'], 21.4);
			$dark40 = spinHsl($dark40['h'], $dark40['s'], $dark40['l'], -3);
			$root[] = '--poweradmin-bg-dark-40: ' . hslToHex($dark40['h'], $dark40['s'], $dark40['l']) . ';';

			// --poweradmin-bg-dark-50: desaturate(15), lighten(10), spin(-1)
			$dark50 = desaturateHsl($baseH, $baseS, $baseL, 15);
			$dark50 = lightenHsl($dark50['h'], $dark50['s'], $dark50['l'], 10);
			$dark50 = spinHsl($dark50['h'], $dark50['s'], $dark50['l'], -1);
			$root[] = '--poweradmin-bg-dark-50: ' . hslToHex($dark50['h'], $dark50['s'], $dark50['l']) . ';';

			// --poweradmin-bg-dark-70: lighten(-6), spin(4)
			$dark70 = lightenHsl($baseH, $baseS, $baseL, -6);
			$dark70 = spinHsl($dark70['h'], $dark70['s'], $dark70['l'], 4);
			$root[] = '--poweradmin-bg-dark-70: ' . hslToHex($dark70['h'], $dark70['s'], $dark70['l']) . ';';

			// --poweradmin-bg-dark-80: lighten(-11.5), spin(7)
			$dark80 = lightenHsl($baseH, $baseS, $baseL, -11.5);
			$dark80 = spinHsl($dark80['h'], $dark80['s'], $dark80['l'], 7);
			$root[] = '--poweradmin-bg-dark-80: ' . hslToHex($dark80['h'], $dark80['s'], $dark80['l']) . ';';

			// --poweradmin-bg-dark-90: desaturate(-1), lighten(-17), spin(10)
			$dark90 = desaturateHsl($baseH, $baseS, $baseL, -1);
			$dark90 = lightenHsl($dark90['h'], $dark90['s'], $dark90['l'], -17);
			$dark90 = spinHsl($dark90['h'], $dark90['s'], $dark90['l'], 10);
			$root[] = '--poweradmin-bg-dark-90: ' . hslToHex($dark90['h'], $dark90['s'], $dark90['l']) . ';';
		}
		catch (\Exception $ex)
		{
			// Just ignore exceptions
		}

		return $root;
	}

	/**
	 * Determinates if the given string is a color hex value
	 *
	 * @param   string  $hex  The string to test
	 *
	 * @return boolean  True when color hex value otherwise false
	 *
	 * @since  4.0.0
	 */
	protected static function isHex($hex): bool
	{
		if (substr($hex, 0, 1) !== '#')
		{
			return false;
		}

		$value = ltrim($hex, '#');

		return (strlen($value) == 6 || strlen($value) == 3) && ctype_xdigit($value);
	}
}
