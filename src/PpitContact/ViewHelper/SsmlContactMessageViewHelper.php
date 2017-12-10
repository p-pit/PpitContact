<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitContact\ViewHelper;

use PpitCore\Model\Context;

class SsmlContactMessageViewHelper
{
	public static function formatXls($workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');

		$title = $context->getConfig('contactMessage/search')['title'][$context->getLocale()];
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-Pit')
			->setLastModifiedBy('P-Pit')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();
		
		$i = 0;
		$colNames = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T');

		foreach($context->getConfig('contactMessage/update') as $propertyId => $unused) {
			$property = $context->getConfig('contactMessage')['properties'][$propertyId];
			if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
			$i++;
			$sheet->setCellValue($colNames[$i].'1', $property['labels'][$context->getLocale()]);
			$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
		}

		$j = 1;
		foreach ($view->contactMessages as $contactMessage) {
			$j++;
			$i = 0;
		foreach($context->getConfig('contactMessage/update') as $propertyId => $unused) {
			$property = $context->getConfig('contactMessage')['properties'][$propertyId];
			if ($property['type'] == 'repository') $property = $context->getConfig($property['definition']);
				$i++;
				if ($property['type'] == 'date') $sheet->setCellValue($colNames[$i].$j, $contactMessage->properties[$propertyId]);
				elseif ($property['type'] == 'number') {
					$sheet->setCellValue($colNames[$i].$j, $contactMessage->properties[$propertyId]);
					$sheet->getStyle($colNames[$i].$j)->getNumberFormat()->setFormatCode('### ##0.00');
				}
				elseif ($property['type'] == 'select') $sheet->setCellValue($colNames[$i].$j, (array_key_exists($contactMessage->properties[$propertyId], $property['modalities'])) ? $property['modalities'][$contactMessage->properties[$propertyId]][$context->getLocale()] : $contactMessage->properties[$propertyId]);
				elseif ($property['type'] == 'array') $sheet->setCellValue($colNames[$i].$j, implode(', ', $contactMessage->properties[$propertyId]));
				else $sheet->setCellValue($colNames[$i].$j, $contactMessage->properties[$propertyId]);
		}
		}
		$i = 0;
		foreach($context->getConfig('contactMessage/update') as $propertyId => $property) {
			$i++;
			$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		}
	}
}