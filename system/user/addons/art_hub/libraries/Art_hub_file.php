<?php

class Art_hub_file
{
    private $errors = array();
    
    public function __construct($param = []) 
    {
        ee()->load->helper('form');
        
        ee()->art_hub->load_package_js([
            'app/service/file-field',
            'app/controller/file-picker',
            'app/controller/file-input-field',
        ]);
    }
    
    /**
     * 
     * see: EE/Service/File/Upload/uploadTo()
     */
    public function do_upload()
    {
        $errors = [];
        $upload_directory_id = null;
        $meta = ee()->input->post('meta');
        $meta = json_decode(ee('Encrypt')->decode($meta), TRUE);
        
        if(empty($meta)) {
            $errors[] = [
                'title' => 'Invalid Meta Data.'
            ];
        } else {
            
            $upload_directory_id = $meta['upload_directory'];
            
            if(!isset($_FILES['userFile']) || $_FILES['userFile']['size'] == 0) {
                $errors[] = [
                    'title' => 'File required.'
                ];
            }
            
        }
        
        // $field_content_type = $meta['field_content_type'];
        
        $dir = ee('Model')->get('UploadDestination', $upload_directory_id)
        ->filter('site_id', ee()->config->item('site_id'))
        ->first(); 
        
        // TODO: upload destination validation
        
        if (empty($errors) && ! $dir) {
            $errors[] = [
                'title' => lang('no_upload_destination')
            ];
        }

        // if (! $dir->memberHasAccess(ee()->session->getMember())) {
        //     show_error(lang('unauthorized_access'), 403);
        // }
        
        if (empty($errors) && ! $dir->exists()) {
            $output['status'] = 'error';
            $output['errors'][] = [
                'title' => lang('file_not_found'),
                'body' => sprintf(lang('directory_not_found'), $dir->server_path)
            ];
//             $upload_edit_url = ee('CP/URL')->make('files/uploads/edit/' . $dir->id);
//             ee('CP/Alert')->makeStandard()
//             ->asIssue()
//             ->withTitle(lang('file_not_found'))
//             ->addToBody(sprintf(lang('directory_not_found'), $dir->server_path))
//             ->addToBody(sprintf(lang('check_upload_settings'), $upload_edit_url))
//             ->now();
            
//             show_404();
        }

        // $posted = false;
        
        // Check permissions on the directory
        if (empty($errors) && ! $dir->isWritable()) {
            $errors[] = [
                'title' => lang('dir_not_writable'),
                'body' => sprintf(lang('dir_not_writable_desc'), $dir->server_path)
            ];
//             ee('CP/Alert')->makeInline('shared-form')
//             ->asIssue()
//             ->withTitle(lang('dir_not_writable'))
//             ->addToBody(sprintf(lang('dir_not_writable_desc'), $dir->server_path))
//             ->now();
        }
        
        
        if(!empty($errors)) {
            ee()->output->set_status_header('404');
            if(AJAX_REQUEST) {
                return ee()->output->send_ajax_response(['errors' => $errors]);
            }
            return json_encode($errors);
        }
        
        $config['upload_path']   = rtrim($dir->server_path, '/') . '/';
        $config['max_size']      = $dir->max_size;
        $config['max_width']     = $dir->max_width;
        $config['max_height']    = $dir->max_height;
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['file_ext_tolower'] = true; // If set to TRUE, the file extension will be forced to lower case
        $config['overwrite']     = false;
        $config['max_filename_increment'] = 1000;
        $config['remove_spaces'] = true;
        $config['detect_mime'] = true;

        
        ee()->load->library('upload');
        ee()->upload->initialize($config);
        
        if ( ! ee()->upload->do_upload('userFile')) {
            $errors = ee()->upload->display_errors();
            
            ee()->output->set_status_header('404');
            if(AJAX_REQUEST) {
                return ee()->output->send_ajax_response(['errors' => $errors]);
            }
            return json_encode($errors);
        }
        
        $file = ee('Model')->make('File');
        $file->UploadDestination = $dir;
        
        $file_data = ee()->upload->data();
        
        $file->set([
            'site_id' => ee()->session->userdata('site_id'),
            'title' => $file_data['file_name'],
            'file_name' => $file_data['file_name'],
            'mime_type' => $file_data['file_type'],
            'file_size' => $file_data['file_size'],
            'upload_date' => ee()->localize->now,
            'modified_date' => ee()->localize->now,
            'uploaded_by_member_id' => ee()->session->userdata('member_id'),
            'modified_by_member_id' => ee()->session->userdata('member_id'),
            'file_hw_original' => ($file_data['is_image']) ? $file_data['image_height'] . ' ' . $file_data['image_width'] : '',
        ]);
        
        // form info
        $file_title = ee()->input->post('title', true);
        $file_title = (is_string($file_title)) ? trim($file_title) : $file_title;
        if(!is_null($file_title) && !empty($file_title) && $file_title != false) {
            $file->title = $file_title;
        }
        
        $file_desc = ee()->input->post('desc', true);
        $file_desc = (is_string($file_desc)) ? trim($file_desc) : $file_desc;
        if(!is_null($file_desc) && !empty($file_desc) && $file_desc != false) {
            $file->description = $file_desc;
        }
        
        $file_credit = ee()->input->post('credit', true);
        $file_credit = (is_string($file_credit)) ? trim($file_credit) : $file_credit;
        if(!is_null($file_credit) && !empty($file_credit) && $file_credit != false) {
            $file->credit = $file_credit;
        }
        
        $file_location = ee()->input->post('location', true);
        $file_location = (is_string($file_location)) ? trim($file_location) : $file_location;
        if(!is_null($file_location) && !empty($file_location) && $file_location != false) {
            $file->location = $file_location;
        }
        
        $result = $file->validate();
        
        if($result->isNotValid()) {
            $errors = $result->getAllErrors();
            ee()->output->set_status_header('404');
            if(AJAX_REQUEST) {
                return ee()->output->send_ajax_response(['errors' => $errors]);
            }
            return json_encode($errors);
        }
        
        $file->save();
        
        if(AJAX_REQUEST) {
            $output = [
                'status' => 'success',
                'file' => [
                    'title' => $file->title,
                    'src' => $file->getAbsoluteURL(),
                ],
                'field' => [
                    'value' => "{filedir_${upload_directory_id}}" . $file->file_name,
                ]
            ];
            
            return ee()->output->send_ajax_response($output);
        }
        
        return "File Upload Successful!";
        
    }
    
    private function removeParamValueEncodings(&$value)
    {
        if(! is_string($value)) {
            return;
        }
        
        $value = str_replace("CFORM-ENCODE-LEFT-BRACKET", "{", $value);
        $value = str_replace("CFORM-ENCODE-RIGHT-BRACKET", "}", $value);
    }
    
    
    function fileDataFromFieldValue($value) 
    {
        
        if(is_null($value) || empty($value)) {
            return [null, null];
        }
        
        
        $matches = [];
        $pattern = "/\{filedir_(\d+)\}(.+)/";
        
        if(!preg_match($pattern, strval($value), $matches)) {
            return [null, null];
        }
        
        $upload_destination_id = $matches[1];
        $file_name = $matches[2];
        
        return [
            $upload_destination_id,
            $file_name
        ];
    }
    
    
    
    public function file_input()
    {
        $site_id = ee()->session->userdata('site_id');
        $member_id = ee()->session->userdata('member_id');
        
        $vars = [
            'site_id' => $site_id,
            'member_id' => $member_id,
        ];

        $entry_id = ee()->TMPL->fetch_param('entry_id', null);
        $field_name = ee()->TMPL->fetch_param('field_name', null);
        $field_value = ee()->TMPL->fetch_param('value', null);
        $is_grid_field = ee()->TMPL->fetch_param('grid_field', 'no');
        $is_fluid_field = ee()->TMPL->fetch_param('fluid_field', 'no');
        
        $field_value = (empty($field_value)) ? null: $field_value;
        
        $this->removeParamValueEncodings($field_value);
        
        
        // field_name is required
        if(empty($field_name) || is_null($field_name)) {
            return "ERROR: field_name is required.";
        }
        
        $field = ee('Model')->get('ChannelField')
        ->filter('field_name', $field_name)
        ->first();
        
        if(!$field) {
            return "ERROR: Invalid field_name: '${field_name}'";
        }
        
        
        // {"field_content_type":"image","allowed_directories":"5","show_existing":"y","num_existing":"50","field_fmt":"none"}
        $field_settings = $field->field_settings;
        
        $upload_destination = null;
        
        if($field_settings['allowed_directories'] != 'all') {
            $upload_destination = ee('Model')->get('UploadDestination', $field_settings['allowed_directories'])->first();
        }
        
        if(is_null($upload_destination)) {
            return "ERROR: need to enabled support for mulitple upload destinations";
        }
        
        list($upload_destination_id, $file_name) = $this->fileDataFromFieldValue($field_value);
        $upload_destination_id = $upload_destination->getId();

        $file_title = $file_name;
        $file_url = null;
        $file_is_image = $field_settings['field_content_type'] == 'image' ? true: false;

        if(!is_null($upload_destination_id) && !is_null($file_name)) {
            $file = ee('Model')->get('File')
            ->with('UploadDestination')
            ->filter('File.site_id', $site_id)
            ->filter('File.file_name', $file_name)
            ->filter('UploadDestination.id', $upload_destination_id)
            ->first();
        }
        
        if(isset($file) && $file) {
            $file_title = $file->title;
            $file_url = $file->getAbsoluteURL();
            // TODO: file not image
            // $file_is_image = $file->isImage();
        }
        
        
        $vars += array(
            'upload_directory' => $upload_destination_id,
            'upload_directory_name' => $upload_destination->name,
            'default_modal_view' => $upload_destination->default_modal_view,
            'num_existing' => $field_settings['num_existing'],
            'field_content_type' => $field_settings['field_content_type'],
            'field_name' => $field_name,
            'field_value' => $field_value,
            'file_title' => $file_title,
            'file_src' => $file_url,
            'file_is_image' => $file_is_image,
        );
        
        return ee('View')->make('art_hub:file/entry_field')->render($vars);
        
    }
    
   
    public function chooseExsitingFileView($meta)
    {
        $errors = [];
        
        $output = [
            'status' => 'success'
        ];
        
        $vars = [];
        $vars += $meta;
        
        $vars += [
            'base_url' => ee()->config->item('site_url') . ee()->config->item('site_index'),
            
        ];
        
        // TODO: validations
        // TODO: validate member_id?
        $site_id = $meta['site_id'];
        $member_id = $meta['member_id'];
        
        $upload_directory_id = $meta['upload_directory'];
        $upload_directory_name = $meta['upload_directory_name'];
        $default_modal_view = $meta['default_modal_view'];
        $field_content_type = $meta['field_content_type'];
        $num_existing = $meta['num_existing'];
        
        
        $orderBy = ee()->input->get_post('orderBy');
        $sort = ee()->input->get_post('sort');
        $search = ee()->input->get_post('search', true);
        
        $orderBy = (!is_null($orderBy) && in_array($orderBy, ['upload_date', 'name'])) ? $orderBy : 'upload_date';
        $sort = (!is_null($sort) && in_array($sort, ['ASC', 'DESC'])) ? $sort : 'DESC';
        $search = (!is_null($search) && $search !== false) ? $search : null;
        
        $vars['filters'] = [
            'orderBy'   => $orderBy,
            'sort'      => $sort,
            'search'    => $search
        ];
        
        $files = ee('Model')->get('File')
            ->filter('upload_location_id', $upload_directory_id);
        
        
        // TODO: order
        $files->order('upload_date', 'DESC');
        
        
        // TODO: limit & pagination
        //$files->limit($num_existing);
        
        $files = $files->all();
        
        foreach($files as $file) {
            $vars['files'][] = [
                'id' => $file->getId(),
                'src' => $file->getAbsoluteURL(),
                'value' => "{filedir_${upload_directory_id}}" . $file->file_name,
                'title' => $file->title,
                'name' => $file->file_name,
                'type' => $file->mime_type,
                //'desc' => $file->description,
                'upload_date' => ee()->localize->format_date('%n/%j/%o', $file->upload_date, true), // %n/%j/%o %g:%i %a
            ];
        }
        
        $output['modal'] = ee('View')->make('art_hub:modal')->render([
            'size' => 'xl',
            'centered' => true,
            'scrollable' => true,
            'fullscreen' => false,
            'title' => "Files in <strong>${upload_directory_name}</strong>",
            'body' => ee('View')->make('art_hub:file/picker')->render($vars),
            'footer' => "<button type=\"button\" class=\"btn btn-primary\" data-ng-click=\"modalAction('upload_file')\">Upload File</button>"
        ]);
        
        return ee()->output->send_ajax_response($output);

    }
    
    
    public function uploadFileView($meta)
    {
        $errors = [];
        
        $output = [
            'status' => 'success'
        ];
        
        $vars = [];
        $vars += $meta;
        
        $upload_directory_id = $meta['upload_directory'];
        $upload_directory_name = $meta['upload_directory_name'];
        
        $output['modal'] = ee('View')->make('art_hub:modal')->render([
            'size' => 'xl',
            'centered' => true,
            'scrollable' => true,
            'fullscreen' => false,
            'title' => "Upload File: <strong>${upload_directory_name}</strong>",
            'body' => ee('View')->make('art_hub:file/upload')->render($vars),
            'footer' => "<button type=\"button\" class=\"btn btn-primary\" data-ng-click=\"modalAction('upload_file')\">Upload File</button>"
        ]);
        
        return ee()->output->send_ajax_response($output);

    }
}




