<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Art_hub
{
    
    private $util;
    private $msg;
    private $file;

    public function __construct($param = [])
    {
        ee()->lang->loadfile('art_hub', 'art_hub');
        
        if(ee()->load->is_loaded('art_hub_library') == false) {
            ee()->load->library('Art_hub_library', [], 'art_hub');
        }
        // TODO: remove for more specific dependency injection
        if(ee()->load->is_loaded('art_hub_file') == false) {
            ee()->load->library('Art_hub_file');
        }
        if(ee()->load->is_loaded('art_hub_messages') == false) {
            ee()->load->library('Art_hub_messages');
        }
        if(ee()->load->is_loaded('art_hub_tmpl') == false) {
            ee()->load->library('Art_hub_tmpl');
        }
        
        $this->util = ee()->art_hub;
        $this->file = ee()->art_hub_file;
        $this->msg = ee()->art_hub_messages;
        
        ee()->javascript->set_global('action_ids.get_view', ee()->art_hub->fetch_action_id('Art_hub', 'get_view'));
        
    }
    
    public function compose_message()
    {
        return $this->msg->compose();
    }
    
    public function send_message()
    {
        return $this->msg->send();
    }
    
    public function messages()
    {
        return $this->msg->messages();
    }
    
    public function message_conversations()
    {
        return $this->msg->conversations();
    }
    
    
    public function file()
    {
        return $this->file->file_input();
    }
    
    
    public function upload_file()
    {
        return $this->file->do_upload();
    }


    public function rand()
    {
        return substr(md5(microtime()),rand(0,26),8);
    }


    public function load_package_js()
    {
        $name = ee()->TMPL->fetch_param('name', null);

        if(! is_null($name)) {
            ee()->art_hub->load_package_js($name);
        }
    }


    public function lang()
    {
        $name = ee()->TMPL->fetch_param('name', null);
        return lang($name);
    }
    
    
    public function page_alert()
    {
        $status = ee()->session->flashdata('alert_status');
        $title = ee()->session->flashdata('alert_title');
        $body = ee()->session->flashdata('alert_body');
        
        if($title == false && $body == false) {
            return '';
        }
        
        $vars = [
            'body' => lang($body)
        ];
        
        if($title != false) {
            $vars['title'] = lang($title);
        }
        
        if($status != false) {
            $vars['status'] = lang($status);
        }
        
        return ee('View')->make('art_hub:alert')->render($vars);
    }
    
    
    public function flashdata_error()
    {
        $error_name = ee()->TMPL->fetch_param('name', 'error');
        $message = ee()->session->flashdata('error');
        return "Error: " . $message;
    }
    
    
    public function set_flashdata()
    {
        $name = ee()->TMPL->fetch_param('name', null);
        $value = ee()->TMPL->fetch_param('value', null);
        
        // ee()->session->set_flashdata("return", "test");
        
        if(!is_null($name)) {
            if(is_null($value)) {
                $value = ee()->TMPL->tagdata;
            }
            if(!is_null($value) && !empty($value)) {
                ee()->session->set_flashdata($name, $value);
            }
        }
        
        
    }
    
    public function flashdata()
    {
        $name = ee()->TMPL->fetch_param('name', null);
        
//         if(is_null($name)) {
//             return 'null name';
//         }
        
        $value = ee()->session->flashdata("return");
        
        if(!empty($value) && $value != false) {
            return $value;
        }
        
        return '';
    }
    
    
    public function author_entry_count() 
    {
        $member_id = ee()->TMPL->fetch_param('member_id', ee()->session->userdata('member_id'));
        $channel = ee()->TMPL->fetch_param('channel', 'post');
        
        if(!is_null($member_id)) {
            $member_id = str_replace("CFORM-ENCODE-LEFT-BRACKET", "{", $member_id);
            $member_id = str_replace("CFORM-ENCODE-RIGHT-BRACKET", "}", $member_id);
        }
        
        return ee('Model')->get('ChannelEntry as e')
        ->with('Channel as ch')
        ->fields('e.entry_id')
        ->filter('author_id', $member_id)
        ->filter('ch.channel_name', $channel)
        ->count();
        
    }


    public function cat_name() 
    {
        $cat_id = ee()->TMPL->fetch_param('id', null);
        $cat_model = ee('Model')->get('Category', $cat_id)->first();
        
        if(!$cat_model) {
            return "INVALID CATEGORY ID: " . json_encode($cat_id);
        }
        
        return $cat_model->cat_name;
    }


    


    public function head_html()
    {
        $items = $this->util->get_head();
        $str = '';
        foreach($items as $i) {
            $str .= $i;
        }
        return $str;
    }


    public function script_foot()
    {
        $this->util->_seal_combo_loader();
        $this->util->add_js_script('file', 'cp/global_end');
        return ee()->javascript->get_global() . $this->util->render_footer_js();
    }
    
    
    public function footer_html()
    {
        return $this->util->render_foot_html();
    }


    
    public function get_view()
    {
//         if(ee()->load->is_loaded('art_hub_file') == false) {
//             ee()->load->library('Art_hub_file');
//         }
        
        $meta = ee()->input->post('meta');
        $meta = json_decode(ee('Encrypt')->decode($meta), TRUE);
        
        if(empty($meta)) {
            ee()->output->set_status_header('401');
            return ee()->output->send_ajax_response([
                'error' => 'Invalid META data.'
            ]);
        }
        
        
        $requested_view = ee()->input->post('view', true);
        
        if(empty($requested_view) || $requested_view == false) {
            ee()->output->set_status_header('400');
            return ee()->output->send_ajax_response([
                'error' => 'A "view" is rquired to be requested.'
            ]);
        }
        
        
        if($requested_view == "choose_existing_file") {
            return $this->file->chooseExsitingFileView($meta);
        } else if ($requested_view == "upload_file") {
            return $this->file->uploadFileView($meta);
        } else {
            ee()->output->set_status_header('400');
            return ee()->output->send_ajax_response([
                'error' => 'Invalid view request: ' . $requested_view
            ]);
        }

    }

    private function removeParamValueEncodings(&$value)
    {
        if(! is_string($value)) {
            return;
        }
        
        $value = str_replace("CFORM-ENCODE-LEFT-BRACKET", "{", $value);
        $value = str_replace("CFORM-ENCODE-RIGHT-BRACKET", "}", $value);
    }


    public function fluid_field()
    {
        $site_id = ee()->session->userdata('site_id');
        $member_id = ee()->session->userdata('member_id');
        
        
        $field_name = ee()->TMPL->fetch_param('name', null);
        $entry_id = ee()->TMPL->fetch_param('entry_id', null);
        
        if(! is_null($entry_id)) {
            $this->removeParamValueEncodings($entry_id);
        }

        $field = ee('Model')->get('ChannelField')
        ->filter('field_name', $field_name)
        ->filter('field_type', 'fluid_field')
        ->first();

        if(!$field) {
            return "";
            return json_encode($field->field_settings);
        }

        $field_id = $field->getId();
        // {"field_channel_fields":["1"]}
        $field_settings = $field->field_settings;
        
        $vars = [
            'field_label' => $field->field_label,
            'field_name' => $field_name, //'field_id_' . $field_id,
            'field_instructions' => $field->field_instructions,
            'field_required' => $field->field_required
        ];
        
        if(! empty($field_settings['field_channel_fields'])) {
            $child_fields = ee('Model')->get('ChannelField')
            ->filter('field_id', 'IN', $field_settings['field_channel_fields'])
            ->all();
            
            $child_field_data = [];
            
            if($child_fields->count() > 0) {
                foreach($child_fields as $child_ft) {
                    $child_field_type = $child_ft->field_type;
                    $child_field_settings = $child_ft->field_settings;
                    $child_field_meta = [
                        'site_id'               => $site_id,
                        'member_id'             => $member_id,
                    ];
                    
                    if($child_field_type == 'file') {
                        
                        $upload_destination = ee('Model')->get('UploadDestination', $child_field_settings['allowed_directories'])->first();
                        
                        $child_field_meta += [
                            'upload_directory'      => $upload_destination->getId(),
                            'upload_directory_name' => $upload_destination->name,
                            'default_modal_view'    => $upload_destination->default_modal_view,
                            'field_content_type'    => $child_field_settings['field_content_type'],
                            'num_existing'          => $child_field_settings['num_existing'],
                        ];
                    }
                    
                    $child_field_meta = ee('Encrypt')->encode(json_encode($child_field_meta));
                    
                    $child_field_data[] = [
                        'id' => $child_ft->getId(),
                        'type' => $child_field_type,
                        'label' => $child_ft->field_label,
                        'name' => $child_ft->field_name,
                        'instructions' => $child_ft->field_instructions,
                        'required' => $child_ft->field_required,
                        'meta' => $child_field_meta,
                        //http://art-hub.com/images/uploads/art_posts/4246485_web1_gtr-BobRossWkshp3-091721.jpg'settings' => $child_field_settings,
                    ];
                }
            }
            
            $vars['child_fields'] = $child_field_data;
        }

        
        ee()->art_hub->load_package_js([
            'app/controller/fluid-field',
        ]);
        
        
        return ee('View')->make('art_hub:fluid_field')->render($vars);
    }

}

// END OF
