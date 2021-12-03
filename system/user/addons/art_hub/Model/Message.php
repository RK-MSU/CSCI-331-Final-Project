<?php

namespace User\Addons\ArtHub\Model;

use ExpressionEngine\Service\Model\Model;

class Message extends Model
{

    protected static $_primary_key = 'message_id';
    protected static $_table_name = 'messages';

    protected static $_relationships = [
        'Conversation' => [
            'type'      => 'belongsTo',
            'model'     => 'art_hub:MessageConvesation',
            'from_key'  => 'conversation_id',
            'to_key'    => 'conversation_id',
        ],
        'Sender' => [
            'type'      => 'belongsTo',
            'model'     => 'ee:Member',
            'from_key'  => 'sender_id',
            'to_key'    => 'member_id',
            'weak'      => TRUE,
            'inverse'   => array(
                'name'      => 'SentMessages',
                'type'      => 'hasMany'
            )
        ],
//         'Recipient' => [
//             'type'      => 'belongsTo',
//             'model'     => 'ee:Member',
//             'from_key'  => 'recipient_id',
//             'to_key'    => 'member_id',
//             'weak'      => TRUE,
//             'inverse'   => array(
//                 'name'      => 'SentMessages',
//                 'type'      => 'hasMany'
//             )
//         ],
    ];

    protected static $_typed_columns = [
        'message_id'        => 'int',
        'sender_id'         => 'int',
//         'recipient_id'      => 'int',
        'message_date'      => 'timestamp',
        'message_status'    => 'string',
        'message_subject'   => 'string',
        'message_body'      => 'string',
    ];

    protected static $_validation_rules = array(
        'sender_id' => 'required',
        'conversation_id' => 'required',
//         'recipient_id'      => 'required',
        'message_date'      => 'required',
        'message_status'    => 'required',
//         'message_subject'   => 'required',
        'message_body'      => 'required',
    );

    protected static $_events = array('beforeValidate', 'afterInsert');

    protected $message_id;
    protected $conversation_id;
    protected $sender_id;
    protected $message_date;
    protected $message_status;
    protected $message_subject;
    protected $message_body;
    
    
//     protected function set__message_date($value)
//     {
//         echo json_encode(get_class($value)); die();
//     }


    private function trimValue($value_name)
    {
        $value = $this->getRawProperty($value_name);
        $value = trim($value);
        $value = (empty($value)) ? null : $value;
        $this->setRawProperty($value_name, $value);
    }

    public function onBeforeValidate()
    {
        $this->trimValue('message_subject');
        $this->trimValue('message_body');
    }
    
    public function onAfterInsert()
    {
        $convo = $this->Conversation;
        
        $msg_count = $convo->total_messages;
        $convo->total_messages = $msg_count + 1;
        $convo->last_message_date = $this->getProperty('message_date');
        
        $convo->save();
    }
    
    
    

}
// END CLASS

// EOF
