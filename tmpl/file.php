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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Language\Text;

if ($field->value == '')
{
	return;
}

$value  = $field->rawvalue;

if ($value)
{
	
	// Data
	$filepath	= str_replace('%20', ' ', $value);
	$filename	= basename($filepath);
	$mime		= MediaHelper::getMimeType($filepath);
	$size		= $this->get_file_size($filepath);
	$id			= $this->get_randomizer(8,4);

	$html		= '';
	$inline = '<object width="100%" height="500px" type="'.$mime.'" data="'.$value.'"></object>';
	$modalId = 'fileModal_'.$id;
	
	
	//Filename
	if( $fieldParams->get('filename') == '1')
	{	
		$html .='<span style="margin-right: 0.5rem;"><strong>'.$filename.' ('.$size.')'.'</strong></span>';
	}
	
	// Modal - Preview
	if( $fieldParams->get('preview') == '1')
	{
		HTMLHelper::_('bootstrap.modal', '.selector', []);
		$title = $filename;
		$modalId = 'fileModal_'.$id;
		
		
		$html  .= '
			<button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#'.$modalId.'"><span class="icon-search" aria-hidden="true"></span> '. Text::_('PREVIEW') .' </button>
		';
		
		$html .='
			<div id="'.$modalId.'" class="modal fade" tabindex="-1" aria-labelledby="ModalLabel'.$id.'" aria-hidden="true">
				<div class="modal-dialog modal-lg modal-fullscreen-lg-down">
					<div class="modal-content">
						<div class="modal-header">
							<h5 id="ModalLabel'.$id.'" class="modal-title">'.$title.'</h5>
							<button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							'.$inline.'
						</div>
						<div class="modal-footer">
							<button class="btn btn-secondary" type="button" data-bs-dismiss="modal">'. Text::_('JCLOSE') .'</button>
						</div>
					</div>
				</div>
			</div>
		';
	}
	
	//Download	
	if( $fieldParams->get('download') == '1')
	{
		$html .= '<a href="'.$value.'" download="" class="btn btn-primary btn-sm"><span class="icon-file" aria-hidden="true"></span> '. Text::_('DOWNLOAD') .'</a>';	
	}
	
	//rawvalue
	if( $fieldParams->get('rawvalue') == '1')
	{
		$html .= $value;
	}
	
	echo $html;
}
