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
	 *
	 * @return  array  The checked value
	 *
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
	 *
	 * @return  string  The filesize with unit
	 *
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

	/**
	 * Get randomizer
	 *
	 * @param   int  $length The total length of the return
	 * @param   int  $typ The type of return
	 * @param   int  $l The minimum length lowercase
	 * @param   int  $u The minimum length uppercase
	 * @param   int  $i The minimum length integer
	 * @param   int  $s The minimum length special characters
	 *
	 * Typ 0 = only uppercase
	 * Typ 1 = only lowercase
	 * Typ 2 = upper+lower
	 * Typ 3 = upper+integer
	 * Typ 4 = lower+integer
	 * Typ 5 = upper+lower+integer
	 * Typ 6 = upper+lower+integer+special characters
	 * Typ 7 = only integer
	 * 
	 * @return  string  Random code according to the specifications
	 *
	 * @since   4.0.0
	 */	
	function get_randomizer($length=8, $typ=5, $l=2,$u=2, $i=2, $s=0){

			$l_chars = "abcdefghijklmnopqrstuvwxyz";
			$u_chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$i_chars = "0123456789";
			$s_chars = "[]-#+*$&()%!=?";
			
			switch ($typ) {
				case 0:
					// only uppercase
					$l=0;
					$i=0;
					$s=0;
					$n_chars = $u_chars;
					break;
				case 1:
					// only lowercase
					$u=0;
					$i=0;
					$s=0;
					$n_chars = $l_chars;			
					break;
				case 2:
					// upper+lower
					$i=0;
					$s=0;
					$n_chars = $l_chars.$u_chars;			
					break;
				case 3:
					// upper+integer
					$l=0;
					$s=0;
					$n_chars = $u_chars.$i_chars;			
					break;
				case 4:
					// lower+integer
					$u=0;
					$s=0;
					$n_chars = $l_chars.$i_chars;				
					break;
				case 5:
					// upper+lower+integer
					$s=0;
					$n_chars = $l_chars.$u_chars.$i_chars;			
					break;
				case 6:
					// upper+lower+integer+special characters
					$n_chars = $l_chars.$u_chars.$i_chars.$s_chars;
					break;	
				case 7:
					// only integer
					$l=0;
					$u=0;
					$s=0;			
					$n_chars = $i_chars;
					break;				
			}
				
			$chars = '';
			// create the characters with the minimum number
			$res_l = ($l>0)?substr(str_shuffle($l_chars),0,$l):'';
			$res_u = ($u>0)?substr(str_shuffle($u_chars),0,$u):'';
			$res_i = ($i>0)?substr(str_shuffle($i_chars),0,$i):'';
			$res_s = ($s>0)?substr(str_shuffle($s_chars),0,$s):'';		
			$chars .= $res_l.$res_u.$res_i.$res_s;
			
			// create the remaining characters
			if(strlen($chars) < $length){
				$n = $length - strlen($chars);
				$chars .= substr(str_shuffle($n_chars),0,$n);
			}
			
			// shuffle the characters
			$result = substr(str_shuffle($chars),0,$length);
		
			return $result;
		}	
	
}
