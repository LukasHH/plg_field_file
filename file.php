<?php
/**
 * Plugin Field - File based on Media Manger
 * ------------------------------------------------------------------------
 * Author    it-conserv.de
 * Copyright (C) 2021 it-conserv.de All Rights Reserved.
 * License - http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * Websites: it-conserv.de
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;

/**
 * Fields File Plugin
 *
 * @since  3.7.0
 */
class PlgFieldsFile extends \Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin
{
	/**
	 * Transforms the field into a DOM XML element and appends it as a child on the given parent.
	 *
	 * @param   stdClass    $field   The field.
	 * @param   DOMElement  $parent  The field node parent.
	 * @param   Form        $form    The form.
	 *
	 * @return  DOMElement
	 *
	 * @since   4.0.0
	 */
	public function onCustomFieldsPrepareDom($field, DOMElement $parent, Form $form)
	{
		$fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

		if (!$fieldNode)
		{
			return $fieldNode;
		}

		// see administrator/components/com_media/src/Model/ApiModel.php
		$fieldNode->setAttribute('type', 'media');
		$fieldNode->setAttribute('types', 'documents,videos,audios');
		$fieldNode->setAttribute('preview', 'false');

		return $fieldNode;
	}

	/**
	 * Before prepares the field value.
	 *
	 * @param   string     $context  The context.
	 * @param   \stdclass  $item     The item.
	 * @param   \stdclass  $field    The field.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function onCustomFieldsBeforePrepareField($context, $item, $field)
	{
		// Check if the field should be processed by us
		
		if (!$this->isTypeSupported($field->type))
		{
			return;
		}		

		// Check if the field value is an old (string) value		
		$field->value = $this->checkValue($field->value);
	}

	/**
	 * Before prepares the field value.
	 *
	 * @param   string  $value  The value to check.
	 * @return  array  The checked value
	 * @since   4.0.0
	 */
	private function checkValue($value)
	{
		json_decode($value);

		if (json_last_error() === JSON_ERROR_NONE)
		{
			return (array) json_decode($value, true);
		}

		return array('file' => $value, 'alt_text' => '');
	}
		
	/**
	 * Check the filesize
	 *
	 * @param   string  $rawvalue
	 * @return  string  The filesize with unit
	 * @since   4.0.0
	 */	
	function get_file_size($file)
	{		
		$filepath	= str_replace('%20', ' ', $file);		
		$size = filesize($filepath);
		$units = array("Byte","KB","MB","GB","TB","PB","EB","ZB","YB");

		foreach($units as $pow => $unit) {
			if ($size / pow(1024, $pow) < 1024)
			return number_format($size / pow(1024, $pow), 1,  ",", ".") .' ' . $unit;
		}
	}
}
