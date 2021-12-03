<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Art_hub_mcp
{

    function index()
    {
        return "ArtHub";
        
        ee()->load->library('Art_hub_db');
        ee()->art_hub_db->uninstall();
        ee()->art_hub_db->install();
        // ee()->art_hub_db->data();
        return "ArtHub";
    }


    function do_upload() 
    {
        ee()->load->library('File_upload');
        $util = ee()->file_upload;

        $util->do_upload();

        if(is_null($util->errors)) {
            $redirect_url = ee('CP/URL', 'cp/addons/settings/art_hub')->compile();
            return ee()->functions->redirect($redirect_url);
        }

        return $util->errors;
        
    }
}

// END OF
