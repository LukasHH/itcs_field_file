<?php
/**
 * ITCS Fields File - based on Media Manger
 * ------------------------------------------------------------------------
 * @package     ITCS.Plugin
 * @subpackage  Fields.file
 *
 * @author      it-conserv.de
 * @copyright   Copyright (C) 2025 it-conserv.de All Rights Reserved.
 * @license     License - GNU/GPLv3 <http://www.gnu.org/licenses/gpl-3.0.de.html>
 * @link        it-conserv.de
 * ------------------------------------------------------------------------
 */
 
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;

if ($field->value == '')
{
    return;
}

$value  = $field->rawvalue;

if(!File::exists(str_replace('%20', ' ', $value)))
{
    $value = "";
    echo 'File not found';
}

if ($value)
{
    
    // Data
    $filepath	= str_replace('%20', ' ', $value);
    $filename	= basename($filepath);
    $mime		= MediaHelper::getMimeType($filepath);
    $size		= $this->get_file_size($filepath);
    $id			= rand(1,29999);

    $html		= '';
    $matchcount = preg_match_all('/(audio|text|video|pdf)/m', $mime, $mime_match, PREG_SET_ORDER, 0);
    $mime_typ = ($matchcount > 0) ? $mime_match[0][0] : $mime;

    switch($mime_typ){
        case 'text':
            $inline ='<p>';
            $lines = file($value);
            foreach ($lines as $line) {
                $inline .= htmlspecialchars($line) . "<br />";
            }

            $inline .='</p>';
            break;
        case 'video': 
            $inline = '<video controls width="100%" type="'.$mime.'" src="'.$value.'"></video>';
            break;
        case 'audio': 
            $inline = '<audio controls width="100%" height="100px" type="'.$mime.'" src="'.$value.'"></audio>';
            break;
        case 'pdf': 
            $inline = '<object width="100%" style="height: 70vh" type="'.$mime.'" data="'.$value.'">
            <embed src="'.$value.'" type="application/pdf" width="100%"></embed>
            ' . Text::sprintf('PLG_FIELDS_FILE_PDF_VIEWER_MISSING', $value) . '
            </object>';
            break;
        default:
            $inline = '<p class="text-center"><i style="font-size: 3rem;" class="fa fa-file-download"></i><br/>Not Preview</p>';
    }

    $modalId = 'fileModal_'.$id;
    
    
    //Filename
    if( $fieldParams->get('filename') == '1')
    {	
        $html .='<span style="margin-right: 0.5rem;"><strong>'.$filename.' ('.$size.')'.'</strong></span>';
    }
    
    // Modal - Preview	
    if( $fieldParams->get('preview') == '1')
    {
        HTMLHelper::_('bootstrap.renderModal', '.selector', []);
        $modalId = 'fileModal_'.$id;
                
        $html  .= '
            <button 
                class="btn btn-primary btn-sm"
                data-bs-toggle="modal" 
                data-bs-target="#'.$modalId.'" 
                data-bs-title="'.Text::_('PREVIEW').' '.$filename.'"
                data-bs-action="showPreviewFile" 
                onclick="return false;"
            >
            <span class="icon-search" aria-hidden="true"></span> '. Text::_('PREVIEW') .' 
            </button>
        ';

        $html .= HTMLHelper::_(
            'bootstrap.renderModal',
            $modalId,
            array(
                'modal-dialog-scrollable' => true,
                'title'  => Text::_('PREVIEW') .' <span class="lead">'.Text::sprintf('ITCS_FIELDS_FILE_LABEL',$filename).'</span>',
                'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>'
            ),
                '<div id="modal-body">'.$inline.'</div>'
        );
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