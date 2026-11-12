<?php

/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2026 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

use Joomla\Archive\Archive;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Helper\MediaHelper;


// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Upload font trait
 *
 * @since 5.0.0
 */
trait UploadFontTrait
{
	public function uploadFont()
	{
		$method = $this->getInputMethod();
		$this->checkNotAllowedMethods(['GET', 'PUT', 'PATCH', 'DELETE'], $method);

		if ($method === 'POST')
		{
			$this->uploadFontFiles();
		}
	}

	private function uploadFontFiles()
	{
		$input = Factory::getApplication()->input;
		$fontFile = $input->files->get('font');

		$params = ComponentHelper::getParams('com_media');
		$contentLength = (int) $_SERVER['CONTENT_LENGTH'];
		$mediaHelper = new MediaHelper;
		$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));
		$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

		$config = Factory::getConfig();
		$tempPath = $config->get('tmp_path');

		$response = [
			'message' => '',
			'id' => null
		];

		if (empty($fontFile))
		{
			$response['message'] = 'No font file found.';
			$this->sendResponse($response, 400);
		}

		if ($fontFile['error'] !== UPLOAD_ERR_OK)
		{
			$response['message'] = 'Error uploading file.';
			$this->sendResponse($response, 500);
		}

		if (($postMaxSize > 0 && $contentLength > $postMaxSize) || ($memoryLimit !== -1 && $contentLength > $memoryLimit))
		{
			$response['message'] = 'Max size limit exceeded.';
			$this->sendResponse($response, 400);
		}

		$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

		if (($fontFile['error'] === 1) || ($uploadMaxSize > 0 && $fontFile['size'] > $uploadMaxSize) || ($uploadMaxFileSize > 0 && $fontFile['size'] > $uploadMaxFileSize))
		{
			$response['message'] = 'Max upload size limit exceeded.';
			$this->sendResponse($response, 400);
		}

		$fileExtension = strtolower(File::getExt($fontFile['name']));

		if ($fileExtension !== 'zip')
		{
			$response['message'] = 'Invalid file type.';
			$this->sendResponse($response, 400);
		}

		$zipPath = $tempPath . '/CustomFont.zip';

		if (File::exists($zipPath))
		{
			File::delete($zipPath);
		}

		if (File::upload($fontFile['tmp_name'], $zipPath, false, true))
		{
			$zip = new ZipArchive;

			if ($zip->open($zipPath) === true)
			{
				for ($i = 0; $i < $zip->numFiles; $i++)
				{
					$name = str_replace('\\', '/', $zip->getNameIndex($i));
					if (
						str_contains($name, '../') ||
						str_starts_with($name, '/') ||
						preg_match('#^[a-zA-Z]:#', $name)
					)
					{
						$zip->close();
						File::delete($zipPath);

						$response['message'] = 'Invalid ZIP structure.';
						$this->sendResponse($response, 400);
					}
				}

				$zip->close();
			}

			$unzipped = $this->unzip($zipPath);
			$fontName = '';
			$fontVariants = ['regular'];

			$fontFiles = $unzipped['files'];
			$extractDirectory = $unzipped['directory'];

			if (!empty($fontFiles))
			{
				foreach ($fontFiles as $file)
				{
					$extension = strtolower(File::getExt($file));

					if ($extension === 'css')
					{
						$content = file_get_contents($file);

						if (strpos($content, '@font-face') === false)
						{
							continue;
						}

						$fontName = $this->extractFontFamilyFromPath($file);

						if (empty($fontName))
						{
							$fontName = basename(dirname($file));
						}

						$fontName = preg_replace('/[^a-zA-Z0-9_-]/', '', $fontName);

						if (empty($fontName))
						{
							continue;
						}

						$this->normalizeFontStylesheet($file, $fontName);
						$fontVariants = $this->extractFontVariantsFromPath($file, $fontName);

						if (empty($fontVariants))
						{
							$fontVariants = ['regular'];
						}

						break;
					}
				}
			}

			if (empty($fontName))
			{
				File::delete($zipPath);
				Folder::delete($extractDirectory);
				$response['message'] = 'Invalid font package.';
				$this->sendResponse($response, 500);
			}

			if (!empty($fontName))
			{
				$mediaDirectory = JPATH_ROOT . '/media/com_sppagebuilder/assets/custom-fonts/' . $fontName;

				if (Folder::exists($mediaDirectory))
				{
					Folder::delete($mediaDirectory);
				}

				Folder::create($mediaDirectory, 0755);

				$base = realpath($extractDirectory);

				foreach ($fontFiles as $file)
				{
					$real = realpath($file);

					if ($real === false || $base === false)
					{
						continue;
					}

					if (!str_starts_with($real, $base . DIRECTORY_SEPARATOR))
					{
						continue;
					}

					File::move($real, $mediaDirectory . '/' . basename($real));
				}
			}

			File::delete($zipPath);
			Folder::delete($extractDirectory);

			$fontData = (object)[
				'id' => null,
				'family_name' => $fontName,
				'data' => (object) [
					'family' => $fontName,
					'variants' => $fontVariants,
					'variants_migrated' => true,
					'subsets' => ['latin'],
					'category' => '',
					'is_installed' => true
				],
				'type' => 'local',
				'created' => Factory::getDate()->toSql(),
				'created_by' => Factory::getUser()->id
			];

			$this->saveCustomFont($fontData);
		}

		$this->sendResponse($response);
	}

	private function unzip(string $filePath)
	{
		$config = Factory::getConfig();
		$tempPath = $config->get('tmp_path');

		$tempDirectory = uniqid('custom-font-');
		$extractDestination = Path::clean(dirname($filePath) . '/' . $tempDirectory);

		if (!Folder::exists($extractDestination))
		{
			Folder::create($extractDestination, 0755);
		}

		$archive = new Archive(['tmp_path' => $tempPath]);

		$extractedResult = $archive->extract(Path::clean($filePath), $extractDestination);

		$requiredFiles = [];

		if ($extractedResult)
		{
			$files = Folder::files($extractDestination, '.', true, true);

			if (!empty($files))
			{
				foreach ($files as $file)
				{
					$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

					if (in_array($extension, ['css', 'woff', 'woff2']))
					{
						$requiredFiles[] =  $file;
					}
				}
			}
		}

		return ['files' => $requiredFiles, 'directory' => $extractDestination];
	}

	private function extractFontFamilyFromPath(string $path)
	{
		if (!File::exists($path))
		{
			return null;
		}

		$content = file_get_contents($path);
		preg_match('/font-family:\s*["\'](.*?)["\']/', $content, $matches);


		return isset($matches[1]) ? $matches[1] : null;
	}

	/**
	 * Rewrite the uploaded stylesheet so it matches what the builder uses:
	 * - font-family becomes the sanitized family name (the one saved in the DB and used in page CSS),
	 *   otherwise the @font-face rules register a different name and the font never applies.
	 *   Faces that belong to a different family are left untouched, so they are not merged into this one.
	 * - font-weight/font-style are derived from the font file name (e.g. ExtraBold => 800, 700italic => 700 italic)
	 *   when recognizable, since generated kits often declare wrong weights (ExtraBold as "bold").
	 *   Variable-font ranges (e.g. "100 900") are never overwritten.
	 *
	 * @param   string  $path      The stylesheet path.
	 * @param   string  $fontName  The sanitized font family name.
	 *
	 * @return  void
	 */
	private function normalizeFontStylesheet(string $path, string $fontName): void
	{
		if (!File::exists($path))
		{
			return;
		}

		$content = file_get_contents($path);

		$content = preg_replace_callback('/@font-face\s*\{([^}]*)\}/i', function ($match) use ($fontName) {
			$block = $match[1];

			if (preg_match('/font-family\s*:\s*["\']?([^;"\'}]+)["\']?\s*;?/i', $block, $familyMatch))
			{
				$cssFamilyName = preg_replace('/[^a-zA-Z0-9_-]/', '', trim($familyMatch[1]));

				if ($cssFamilyName !== $fontName)
				{
					return $match[0];
				}

				$block = preg_replace('/font-family\s*:[^;}]+;?/i', "font-family: '" . $fontName . "';", $block, 1);
			}
			else
			{
				$block = "\n\tfont-family: '" . $fontName . "';" . $block;
			}

			$isRange = preg_match('/font-weight\s*:\s*\d+\s+\d+/i', $block);

			if (!$isRange && preg_match('/url\(\s*["\']?([^"\')?#]+)/i', $block, $urlMatch))
			{
				$resolved = $this->resolveFontWeightFromFileName(pathinfo(basename($urlMatch[1]), PATHINFO_FILENAME));

				if ($resolved !== null)
				{
					$block = preg_replace('/font-weight\s*:[^;}]+;?/i', '', $block);
					$block = preg_replace('/font-style\s*:[^;}]+;?/i', '', $block);
					$block = rtrim($block) . "\n\tfont-weight: " . $resolved[0] . ";\n\tfont-style: " . ($resolved[1] ? 'italic' : 'normal') . ";\n";
				}
			}

			return '@font-face {' . $block . '}';
		}, $content);

		if ($content !== null)
		{
			file_put_contents($path, $content);
		}
	}

	/**
	 * Resolve weight and style from a font file name, e.g. Font-ExtraBoldItalic, roboto-v30-latin-700italic.
	 *
	 * @param   string  $fileName  File name without extension.
	 *
	 * @return  array|null  [weight, isItalic] or null when the weight can't be determined.
	 */
	private function resolveFontWeightFromFileName(string $fileName): ?array
	{
		$words = [
			'thin' => 100, 'hairline' => 100,
			'extralight' => 200, 'ultralight' => 200,
			'light' => 300,
			'regular' => 400, 'normal' => 400, 'book' => 400,
			'medium' => 500,
			'semibold' => 600, 'demibold' => 600,
			'bold' => 700,
			'extrabold' => 800, 'ultrabold' => 800,
			'black' => 900, 'heavy' => 900,
		];

		$tokens = preg_split('/[-_.\s]+/', strtolower($fileName), -1, PREG_SPLIT_NO_EMPTY);

		// Join split modifiers, e.g. Extra-Bold => extrabold, Semi-Bold => semibold.
		$joined = [];

		foreach ($tokens as $token)
		{
			$last = count($joined) - 1;

			if ($last >= 0 && in_array($joined[$last], ['extra', 'semi', 'demi', 'ultra'], true))
			{
				$joined[$last] .= $token;
			}
			else
			{
				$joined[] = $token;
			}
		}

		$tokens = $joined;
		$hasItalicToken = false;

		// Scan from the end so suffixes like "webfont" or "latin" are skipped over.
		foreach (array_reverse($tokens) as $token)
		{
			// A separate style token (e.g. OpenSans-Bold-Italic) doesn't end the search, the weight comes from another token.
			if ($token === 'italic' || $token === 'oblique')
			{
				$hasItalicToken = true;
				continue;
			}

			if (!preg_match('/^(\d{3}|[a-z]+?)(italic|oblique)?$/', $token, $m))
			{
				continue;
			}

			$key = $m[1];
			$isItalic = $hasItalicToken || !empty($m[2]);

			if (preg_match('/^[1-9]00$/', $key))
			{
				return [(int) $key, $isItalic];
			}

			if (isset($words[$key]))
			{
				return [$words[$key], $isItalic];
			}
		}

		return $hasItalicToken ? [400, true] : null;
	}


	private function extractFontVariantsFromPath(string $path, string $fontName): array
	{
		if (!File::exists($path))
		{
			return [];
		}

		$content = file_get_contents($path);
		$variants = [];

		preg_match_all('/@font-face\s*\{([^}]*)\}/i', $content, $fontFaces);

		foreach ($fontFaces[1] ?? [] as $fontFace)
		{
			preg_match('/font-family\s*:\s*["\']?([^;"\']+)["\']?\s*;?/i', $fontFace, $familyMatch);

			$cssFamilyName = preg_replace('/[^a-zA-Z0-9_-]/', '', trim($familyMatch[1] ?? ''));

			if (!empty($cssFamilyName) && $cssFamilyName !== $fontName)
			{
				continue;
			}

			preg_match('/font-weight\s*:\s*([^;}]+);?/i', $fontFace, $weightMatch);
			preg_match('/font-style\s*:\s*([^;}]+);?/i', $fontFace, $styleMatch);

			$weight = strtolower(trim($weightMatch[1] ?? 'normal'));
			$weight = preg_replace('/[-_\s]+/', ' ', $weight);
			$style = strtolower(trim($styleMatch[1] ?? 'normal'));
			$weight = [
				'normal' => 'regular',
				'regular' => 'regular',
				'thin' => '100',
				'extra light' => '200',
				'extralight' => '200',
				'light' => '300',
				'medium' => '500',
				'semi bold' => '600',
				'semibold' => '600',
				'bold' => '700',
				'extra bold' => '800',
				'extrabold' => '800',
				'black' => '900',
				'100' => '100',
				'200' => '200',
				'300' => '300',
				'400' => 'regular',
				'500' => '500',
				'600' => '600',
				'700' => '700',
				'800' => '800',
				'900' => '900',
			][$weight] ?? 'regular';

			if ($style === 'italic' && $weight === 'regular')
			{
				$weight = 'italic';
			}
			elseif ($style === 'italic')
			{
				$weight .= 'italic';
			}

			$variants[] = $weight;
		}

		return !empty($variants) ? array_values(array_unique($variants)) : [];
	}

	private function saveCustomFont($data)
	{
		$db = Factory::getDbo();
		$data->data = \json_encode($data->data);
		$db->insertObject('#__sppagebuilder_fonts', $data, 'id');
		$response = (object) [
			'id' => $data->id,
			'message' => ''
		];

		$this->sendResponse($response);
	}
}