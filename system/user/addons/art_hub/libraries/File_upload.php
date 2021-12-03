<?php


class File_upload
{

    var $errors = null;
    var $upload_data = null;

    function __construct($params = [])
    {
        ee()->load->library('upload');
    }


    function do_upload()
    {
        $upload_path = ee()->config->item('base_path') . 'images/test/';

        $config = array(
            'upload_path' => $upload_path
        );

        ee()->upload->initialize($config);


        if ( ! ee()->upload->do_upload()) {
            $this->errors = ee()->upload->display_errors();
        } else {
            $this->upload_data = ee()->upload->data();
        }

    }
    
}