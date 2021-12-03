<?php

use ExpressionEngine\Library\Data\Collection;

class Art_hub_messages
{
    private $base_url;
    
    public function __construct($param = [])
    {
        ee()->load->helper('form');
        ee()->lang->loadfile('messages', 'art_hub');
        $this->base_url = ee()->config->item('site_url') . ee()->config->item('site_index');
        ee()->art_hub->load_package_js('messages');
    }
    
    public function compose()
    {
        $member_id = ee()->session->userdata('member_id');
        $sender = ee('Model')->get('ee:Member', $member_id)->first();
        $recipient = null;
        
        $convo_ids = [];
        $sql = "SELECT DISTINCT convo.conversation_id FROM exp_message_conversations AS convo JOIN exp_message_conversation_members AS convo_mbr ON convo.conversation_id=convo_mbr.conversation_id WHERE convo_mbr.member_id=?";
        $result = ee()->db->query($sql, array($member_id));
        
        if($result->num_rows() > 0) {
            $results_collection = new Collection($result->result_array());
            $convo_ids = $results_collection->pluck('conversation_id');
        }
        
        
        // params
        $conversation_id = ee()->TMPL->fetch_param('conversation_id', null);
        $recipient_id = ee()->TMPL->fetch_param('recipient_id', null);
        $return = ee()->TMPL->fetch_param('return', null);
        
        $conversation = ee('Model')->get('art_hub:MessageConvesation')
        ->filter('conversation_id', 'IN', $convo_ids)
        ->filter('conversation_id', $conversation_id)
        ->first();
        
        if(! $conversation) {
            if(is_null($recipient_id)) {
                show_error("Missing required parameter: {exp:art_hub:compose_message}" . BR . BR . "'recipient_id' OR 'conversation_id' is required");
            } else {
                if(strval($member_id) == strval($recipient_id)) {
                    show_error('Cannot send message to yourself');
                }
                
                $recipient = ee('Model')->get('ee:Member', $recipient_id)->first();
                
                if(! $recipient) {
                    show_error("Invalid recipient_id: " . json_encode($recipient_id));
                }
                
                
                $conversation = ee('Model')->get('art_hub:MessageConvesation')
                ->with('Members as member')
                ->filter('conversation_id', 'IN', $convo_ids)
                ->filter('member.member_id', $recipient->getId())
                ->first();
                
                if(! $conversation) {
                    $conversation = ee('Model')->make('art_hub:MessageConvesation',  ['total_messages' => 0, 'last_message_date' => 0])->save();
                    $conversation->Members->add($recipient);
                    $conversation->Members->add($sender);
                    $conversation->Members->save();
                }
            }
        }
        
        $tagdata = ee()->TMPL->tagdata;
        
//         ee()->art_hub_tmpl->parse_single_vars($tagdata, [
//             'recipient_id' => $recipient_id,
//             'recipient_username' => $recipient->username,
//             'subject' => '',
//             'message' => ''
//         ]);
        
        // form open multipart
        $meta = [
            'sender_id' => ee()->session->userdata('member_id'),
            'conversation_id' => $conversation->getId(),
            'current_url' => ee()->functions->fetch_current_uri()
        ];
        if(! is_null($return) && ! empty($return)) {
            $meta['return'] = $return;
        }
        $hidden = array(
            'ACT' => ee()->art_hub->fetch_action_id('Art_hub', 'send_message'),
            'meta' => ee('Encrypt')->encode(json_encode($meta))
        );
        $attrs = array(
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        );
        $open_form_tag = form_open_multipart($this->base_url, $attrs, $hidden);
        
        return $open_form_tag . $tagdata . "</form>";
    }
    
    public function send()
    {
        $meta = ee()->input->post('meta');
        $meta = ee('Encrypt')->decode($meta);
        $meta = json_decode($meta, TRUE);
        
        if(empty($meta)) {
            show_error("Send Message Error." .BR.BR. "Invalid meta data.");
        }
        
        $member_id = ee()->session->userdata('member_id');
        
        $convo_ids = [];
        $sql = "SELECT DISTINCT convo.conversation_id FROM exp_message_conversations AS convo JOIN exp_message_conversation_members AS convo_mbr ON convo.conversation_id=convo_mbr.conversation_id WHERE convo_mbr.member_id=?";
        $result = ee()->db->query($sql, array($member_id));
        
        if($result->num_rows() > 0) {
            $results_collection = new Collection($result->result_array());
            $convo_ids = $results_collection->pluck('conversation_id');
        }
        
        $conversation = ee('Model')->get('art_hub:MessageConvesation')
        ->filter('conversation_id', 'IN', $convo_ids)
        ->filter('conversation_id', $meta['conversation_id'])
        ->first();
        
        if(! $conversation) {
            show_error("Send Message Error." .BR.BR. "Invalid meta data." . BR. "Bad conversation_id");
        }
        
        $message = ee('Model')->make('art_hub:Message', [
            'sender_id' => $member_id,
            'message_date' => ee()->localize->now,
            'message_status' => 'sent',
            'message_subject' => ee()->input->post('subject', true),
            'message_body' => ee()->input->post('message', true),
        ]);
        $message->Conversation = $conversation;
        
        $result = $message->validate();
        if($result->isNotValid()) {
            ee()->session->set_flashdata('alert_status', 'danger');
            ee()->session->set_flashdata('alert_title', 'Sending Message Error');
            $errors = $result->getAllErrors();
            foreach($errors as $field => $rules) {
                foreach($rules as $rule => $msg) {
                    ee()->session->set_flashdata('alert_body', $field.": " . $msg);
                    break;
                }
                break;
            }
            
            return ee()->functions->redirect($meta['current_url']);
            echo json_encode($result->getAllErrors()); die();
            ee()->session->set_flashdata('message_error', 'SOME ERROR');
            ee()->session->set_flashdata('error', 'SOME ERROR');
            return ee()->functions->redirect($this->base_url . '/messages/error/sending');
        }
        
        $message->save();
        
        ee()->session->set_flashdata('alert_body', 'sent_message_success');
        
        if(isset($meta['return'])) {
            $redirect_url = $this->base_url . '/' . $meta['return'];
        } else if (isset($meta['current_url'])) {
            $redirect_url = $meta['current_url'];
        } else {
            $redirect_url = $this->base_url . '/messages/success';
        }
        
        return ee()->functions->redirect($redirect_url);

    }

    public function messages()
    {
        $member_id = ee()->session->userdata('member_id');
        
        $sender_id = ee()->TMPL->fetch_param('sender_id', null);
        $recipient_id = ee()->TMPL->fetch_param('recipient_id', null);
        $conversation_id = ee()->TMPL->fetch_param('conversation_id', null);
        $orderBy = ee()->TMPL->fetch_param('orderby', 'message_date');
        $sort = ee()->TMPL->fetch_param('sort', 'DESC');
        
        
        $convo_ids = [];
        $sql = "SELECT DISTINCT convo.conversation_id FROM exp_message_conversations AS convo JOIN exp_message_conversation_members AS convo_mbr ON convo.conversation_id=convo_mbr.conversation_id WHERE convo_mbr.member_id=?";
        $result = ee()->db->query($sql, array($member_id));
        
        if($result->num_rows() > 0) {
            $results_collection = new Collection($result->result_array());
            $convo_ids = $results_collection->pluck('conversation_id');
        }
        
        $messages = ee('Model')->get('art_hub:Message')
        ->filter('conversation_id', 'IN', $convo_ids);
        
        if(! is_null($conversation_id)) {
            $messages->filter('conversation_id', $conversation_id);
        }
//         ->filterGroup()->filter('sender_id', $member_id)->orFilter('recipient_id', $member_id)->endFilterGroup();
        
        // ->filter('sender_id', $member_id);
        
        // if(is_null($sender_id) && is_null($recipient_id)) {
        //     $messages->filter('sender_id', $member_id)->orFilter('recipient_id', $member_id);
        // }
        
        $messages->order($orderBy, $sort);
        
        $messages = $messages->all();
        $total_results = $messages->count();
        $no_results = $total_results == 0 ? 1 : 0;
        $count = 1;
        
        $vars = [];
        foreach($messages as $msg) {
            $sender_id = $msg->sender_id;
            $sender = $msg->Conversation->Members->filter(function($member) use ($sender_id) { return ($member->getId() == $sender_id);})->first();
            $recipient = $msg->Conversation->Members->filter(function($member) use ($sender_id) { return ($member->getId() != $sender_id);})->first();
            $sender_avatar = $sender->getAvatarUrl();
            $vars[] = [
                'recipient_id' => $recipient->getId(),
                'sender_id' => $sender_id,
                'avatar' => $sender->getAvatarUrl(),
                'date' => $msg->message_date->getTimestamp(), // message_date->format('m/d/y H:i')
                'subject' => $msg->message_subject,
                'message' => nl2br($msg->message_body),
                'status' => $msg->message_status,
                'count' => $count++,
                'total_results' => $total_results,
                'no_results' => $no_results,
                'member_id' => $member_id
            ];
            
            if($msg->sender_id != $member_id) {
                $msg->message_status = 'read';
                $msg->save();
            }
        }
        
        $single_tmpl_vars['total_results'] = $messages->count();
        $single_tmpl_vars['no_results'] = ($single_tmpl_vars['total_results'] == 0) ? 1 : 0;
        
        $tagdata = ee()->TMPL->tagdata;
        
        ee()->art_hub_tmpl->parse_single_vars($tagdata, $single_tmpl_vars);
        
        return ee()->TMPL->parse_variables($tagdata, $vars);
    }

    
    public function conversations()
    {
        $member_id = ee()->session->userdata('member_id');
        
        $convo_ids = [];
        $sql = "SELECT DISTINCT convo.conversation_id FROM exp_message_conversations AS convo JOIN exp_message_conversation_members AS convo_mbr ON convo.conversation_id=convo_mbr.conversation_id WHERE convo_mbr.member_id=?";
        $result = ee()->db->query($sql, array($member_id));
        
        if($result->num_rows() > 0) {
            $results_collection = new Collection($result->result_array());
            $convo_ids = $results_collection->pluck('conversation_id');
        }
        
        $conversations = ee('Model')
        ->get('art_hub:MessageConvesation')
        ->with('Messages')
        ->with('Members')
        ->filter('conversation_id', 'IN', $convo_ids)
        ->filter('total_messages', '>', 0)
//         ->with('Members')
//         ->filter('Members.member_id', $member_id)
        ->order('last_message_date', 'DESC');
        
        $conversations = $conversations->all();
        
        if($conversations->count() == 0) {
            $alert = ee('View')->make('art_hub:alert')->render([
                'status' => 'secondary',
                'body' => 'No Messages'
            ]);
            return "<div class=\"list-group-item\">${alert}</div>";
        }
        
        $vars = [];
        foreach($conversations as $convo) {
            $convo_members = $convo->Members;
            $recipient = $convo_members->filter(function($member) use ($member_id) { return ($member->getId() != $member_id);})->first();
            $unread_count = $convo->Messages->filter(function($msg) use ($member_id) {
                if($member_id == $msg->sender_id) {
                    return false;
                }
                if($msg->message_status == 'read') {
                    return false;
                }
                return true;
            })->count();
            $var_data = [
                'conversation_id' => $convo->getId(),
                'last_message_date' => $convo->last_message_date->getTimestamp(), // message_date->format('m/d/y H:i')
                'total_messages' => $convo->total_messages,
                'recipient_id' => $recipient->getId(),
                'avatar' => $recipient->getAvatarUrl(),
                'unread_count' => $unread_count,
                'has_new_messages' => ($unread_count == 0) ? 0 : 1,
            ];
            
            $vars[] = $var_data;
        }
        
        $tagdata = ee()->TMPL->tagdata;
        return ee()->TMPL->parse_variables($tagdata, $vars);
    }
    
}
// END OF
