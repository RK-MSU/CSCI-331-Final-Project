<?php

namespace User\Addons\ArtHub\Model;

use ExpressionEngine\Service\Model\Model;

class MessageConvesation extends Model
{

    protected static $_primary_key = 'conversation_id';
    protected static $_table_name = 'message_conversations';

    protected static $_relationships = [
        'Members' => [
            'type'      => 'hasAndBelongsToMany',
            'model'     => 'ee:Member',
            'pivot'     => [
                'table'     => 'message_conversation_members',
                'left'      => 'conversation_id',
                'right'     => 'member_id'
            ],
            'inverse'   => array(
                'name'      => 'Conversations',
                'type'      => 'hasAndBelongsToMany'
            )
        ],
        'Messages' => array(
            'type'      => 'hasMany',
            'model'     => 'art_hub:Message',
            'from_key'  => 'conversation_id',
            'to_key'    => 'conversation_id',
        )
    ];

    protected static $_typed_columns = [
        'conversation_id' => 'int',
        'total_messages' => 'int',
        'last_message_date' => 'timestamp',
        // 'message_date'      => 'timestamp',
        // 'message_status'    => 'string',
        // 'message_subject'   => 'string',
        // 'message_body'      => 'string',
    ];

    protected static $_validation_rules = array(
        // 'sender_id'         => 'required',
    );

//    protected static $_events = array('beforeSave');

    protected $conversation_id;
    protected $total_messages;
    protected $last_message_date;
    
//     public function onBeforeSave()
//     {
//         if($this->getProperty('conversation_id') == 0) {
//             $this->setProperty('total_messages', 0);
//             $this->setProperty('last_message_date', 0);
//         }
//     }

}
// END CLASS

// EOF
