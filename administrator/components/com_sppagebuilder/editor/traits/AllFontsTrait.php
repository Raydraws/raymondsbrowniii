<?php

/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2026 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Http\Http;

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Get all installed fonts
 *
 * @since 5.0.0
 */
trait AllFontsTrait
{
	public function allFonts()
	{
		$method = $this->getInputMethod();
		$this->checkNotAllowedMethods(['POST', 'DELETE', 'PUT', 'PATCH'], $method);

		if ($method === 'GET')
		{
			$this->getInstalledFonts();
		}
	}

	private function getInstalledFonts()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__sppagebuilder_fonts'))
			->where($db->quoteName('published') . ' = 1');

		$db->setQuery($query);

		try
		{
			$response = $db->loadObjectList();

			if (isset($response))
			{
				foreach ($response as $key => $value)
				{
					if (isset($value->data))
					{
						$value->data = json_decode($value->data);

						if (
							$value->type === 'local'
							&& empty($value->data->variants_migrated)
							&& !empty($value->data->variants)
							&& $value->data->variants === ['regular']
						)
						{
							$fontDirectory = JPATH_ROOT . '/media/com_sppagebuilder/assets/custom-fonts/' . $value->family_name;

							if (is_dir($fontDirectory))
							{
								$stylesheets = Folder::files($fontDirectory, '\\.css$', false, true);

								if (!empty($stylesheets))
								{
									$variants = [];

									foreach ($stylesheets as $sheet)
									{
										$variants = array_merge($variants, $this->extractFontVariantsFromPath($sheet, $value->family_name));
									}

									$variants = array_values(array_unique($variants));

									if (!empty($variants))
									{
										$value->data->variants = $variants;
										$value->data->variants_migrated = true;
										$fontData = (object) [
											'id' => $value->id,
											'data' => json_encode($value->data),
										];

										$db->updateObject('#__sppagebuilder_fonts', $fontData, 'id', true);
									}
								}
							}
						}
					}
				}
			}
		}
		catch (\Exception $e)
		{
			$this->sendResponse(['message' => $e->getMessage()], 500);
		}

		$this->sendResponse($response);
	}
}
