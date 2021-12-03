<?php


class Art_hub_db
{
    function __construct()
    {
        ee()->load->dbforge();
    }

    function uninstall()
    {
        ee()->dbforge->drop_table('messages');
        ee()->dbforge->drop_table('message_conversations');
        ee()->dbforge->drop_table('message_conversation_members');
        
    }

    function install()
    {
        $fields = array(
            'message_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'conversation_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ),
            'sender_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ),
//             'recipient_id' => array(
//                 'type' => 'INT',
//                 'constraint' => 11,
//                 'unsigned' => true,
//                 'null' => false
//             ),
            'message_date' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0
            ),
            'message_status' => array(
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => false,
                'default' => 'sent',
            ),
            'message_subject' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
            ),
            'message_body' => array(
                'type' => 'TEXT',
                'null' => false,
            ),
        );
        
        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('message_id', true);
        ee()->dbforge->add_key('conversation_id');
        ee()->dbforge->add_key('sender_id');
//         ee()->dbforge->add_key('recipient_id');
        ee()->dbforge->create_table('messages', true);
        
        $fields = array(
            'conversation_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ),
            'total_messages' => array(
                'type' => 'INT',
                'constraint' => 11,
                // 'null' => false,
                'default' => 0
            ),
            'last_message_date' => array(
                'type' => 'INT',
                'constraint' => 11,
                // 'null' => false,
                'default' => 0
            ),
        );
        
        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('conversation_id', true);
        ee()->dbforge->create_table('message_conversations', true);
        
        $fields = array(
            'conversation_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                // 'null' => false,
                'default' => 0
            ),
            'member_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                // 'null' => false,
                'default' => 0
            ),
        );
        
        ee()->dbforge->add_field($fields);
        // ee()->dbforge->add_key('message_conversation_id', true);
        ee()->dbforge->create_table('message_conversation_members', true);
        
    }


    function data()
    {
        $member_1 = ee('Model')->get('ee:Member', 1)->first();
        $member_2 = ee('Model')->get('ee:Member', 2)->first();
        $member_3 = ee('Model')->get('ee:Member', 3)->first();
        
        $conversation = ee('Model')->make('art_hub:MessageConvesation', ['total_messages' => 0, 'last_message_date' => 0]);
        $conversation->Members->add($member_1);
        $conversation->Members->add($member_2);
        
        $conversation->save();
        $conversation->Members->save();
        
        $message = ee('Model')->make('art_hub:Message', [
        //             'sender_id' => 1,
        //             'recipient_id' => 2,
            'message_date' => 1637956800,
            'message_status' => 'sent',
            'message_subject' => 'My First Message to Erin Scheunemann',
            'message_body' => 'Message to Erin Scheunemann'
        ]);
        $message->Sender = $member_1;
        $message->Conversation = $conversation;
        $message->save();
        
        
        $message = ee('Model')->make('art_hub:Message', [
            //             'sender_id' => 1,
            //             'recipient_id' => 2,
            'message_date' => 1637956920,
            'message_status' => 'sent',
            'message_subject' => 'Erin Scheunemann\'s reply to River Kelly\'s message',
            'message_body' => 'Erin Scheunemann message reply'
        ]);
        $message->Sender = $member_2;
        $message->Conversation = $conversation;
        $message->save();
        
        
        $message = ee('Model')->make('art_hub:Message', [
            'message_date' => 1637957920,
            'message_status' => 'sent',
            'message_subject' => 'River Kelly\'s reply',
            'message_body' => 'message reply info...'
        ]);
        $message->Sender = $member_1;
        $message->Conversation = $conversation;
        $message->save();
        
        
        
        
        
        
        $conversation = ee('Model')->make('art_hub:MessageConvesation', ['total_messages' => 0, 'last_message_date' => 0]);
        $conversation->Members->add($member_1);
        $conversation->Members->add($member_3);
        
        $conversation->save();
        $conversation->Members->save();
        
        $message = ee('Model')->make('art_hub:Message', [
            'message_date' => 1637956860,
            'message_status' => 'sent',
            'message_subject' => 'My First Message to Garrett Keith',
            'message_body' => 'Message to Garrett Keith'
        ]);
        $message->Sender = $member_1;
        $message->Conversation = $conversation;
        $message->save();
        
        
        
        
        
        
        
        
        $conversation = ee('Model')->make('art_hub:MessageConvesation', ['total_messages' => 0, 'last_message_date' => 0]);
        $conversation->Members->add($member_2);
        $conversation->Members->add($member_3);
        
        $conversation->save();
        $conversation->Members->save();
        
        $message = ee('Model')->make('art_hub:Message', [
            'message_status' => 'sent',
            'message_date' => 1637957520,
            'message_subject' => 'Erin Scheunemann to Garrett Keith',
            'message_body' => 'Erin Scheunemann\'s first message to Garrett Keith'
        ]);
        $message->Sender = $member_2;
        $message->Conversation = $conversation;
        $message->save();
        
        
        $message = ee('Model')->make('art_hub:Message', [
            'message_status' => 'sent',
            'message_date' => 1637957520,
            'message_subject' => 'Garrett Keith\'s reply to Erin Scheunemann',
            'message_body' => 'Garrett Keith reply message to Erin Scheunemann'
        ]);
        $message->Sender = $member_3;
        $message->Conversation = $conversation;
        $message->save();
        
//         ee()->db->insert('messages', array(
//             'sender_id' => 1,
//             'recipient_id' => 2,
//             'message_date' => 1637956800,
//             'message_subject' => 'My First Message to Erin Scheunemann',
//             'message_body' => 'Message to Erin Scheunemann'
//         ));
        
//         ee()->db->insert('messages', array(
//             'sender_id' => 1,
//             'recipient_id' => 3,
//             'message_date' => 1637956860,
//             'message_subject' => 'My First Message to Garrett Keith',
//             'message_body' => 'Message to Garrett Keith'
//         ));
        
//         ee()->db->insert('messages', array(
//             'sender_id' => 2,
//             'recipient_id' => 1,
//             'message_date' => 1637956920,
//             'message_subject' => 'Erin Scheunemann\'s reply to River Kelly\'s message',
//             'message_body' => 'Erin Scheunemann message reply'
//         ));
        
        
//         ee()->db->insert('messages', array(
//             'sender_id' => 3,
//             'recipient_id' => 1,
//             'message_date' => 1637957400,
//             'message_subject' => 'Garrett Keith\'s reply to River Kelly\'s first message',
//             'message_body' => 'Garrett Keith message reply'
//         ));
        
        
//         ee()->db->insert('messages', array(
//             'sender_id' => 2,
//             'recipient_id' => 3,
//             'message_date' => 1637957520,
//             'message_subject' => 'Erin Scheunemann to Garrett Keith',
//             'message_body' => 'Erin Scheunemann\'s first message to Garrett Keith'
//         ));
        
//         ee()->db->insert('messages', array(
//             'sender_id' => 3,
//             'recipient_id' => 2,
//         ));
    }

}