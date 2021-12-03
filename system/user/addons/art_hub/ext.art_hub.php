<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

use ExpressionEngine\Service\Addon\Installer as EE_Installer;

class Art_hub_ext extends EE_Installer
{
    public $methods = [
//         [
//             'method' => 'boot_art_hub', // will default to same as hook if not defined
//             'hook' => 'core_boot', // required
//             'priority' => "10",
//             'enabled' => "y" // y/n
//         ]
    ];
    
    /**
     * Notice that for extensions you must include $settings
     * as a parameter in the constructor
     */
    public function __construct($settings = [])
    {
        parent::__construct($settings);
    }

    function boot()
    {
        //echo json_encode($_POST); die();
    }
    
    
    function custom_menu($menu)
    {
        
        $sub = $menu->addSubmenu('Addons');
        $sub->addItem('Comments', ee('CP/URL')->make('cp/publish/comments'));
        $sub->addItem('Discussion Forum', ee('CP/URL')->make('cp/addons/settings/forum'));
        $sub->addItem('Pages', ee('CP/URL')->make('cp/addons/settings/pages'));
        
        $sub = $menu->addSubmenu('CP Settings/Tools');
        $sub->addItem('Database Backup', ee('CP/URL')->make('cp/utilities/db-backup'));
        $sub->addItem('Cache Manager', ee('CP/URL')->make('cp/utilities/cache'));
        $sub->addItem('Menu Manager', ee('CP/URL')->make('cp/settings/menu-manager/edit-set/1'));
        
        
//         $sub = $menu->addSubhttps://csci331.cs.montana.edu/~b62v473/admin.php?/cp/members/roles/edit/6menu('Artwork Files');
//         $sub->addItem('Art Images', ee('CP/URL')->make('cp/files/directory/6&viewtype=thumb&perpage=100'));
//         $sub->withAddLink('Upload New', ee('CP/URL')->make('cp/files/upload/6'));
        
        
        
//         $member_id = ee()->session->userdata('member_id');
//         $mbr_model = ee('Model')->get('Member', $member_id)->first();
        
//         $post_channel = ee('Model')->get('Channel')->filter('channel_name', 'post')->first();
//         $ch_id = $post_channel->getId();
        
//         $authored_entry_models = $mbr_model->AuthoredChannelEntries->filter('channel_id', $ch_id)->count();
        
//         if($authored_entry_models) {
//             $sub = $menu->addSubmenu('Posts');
// //             $sub->addItem('Entries', ee('CP/URL')->make('cp/publish/edit&filter_by_channel=' . $ch_id));
//             $sub->addItem('Author: ' . $mbr_model->username, ee('CP/URL')->make('cp/publish/edit&filter_by_channel=' . $ch_id . '&filter_by_author=' . $member_id));
//             $sub->withAddLink('New', ee('CP/URL')->make('cp/publish/create/' . $ch_id));
//         }
        
        
//         $sub->withAddLink('Title', ee('CP/URL')->make('addons/settings/myaddon/create'));

        // call withFilter to add a create fuzzy filter searchbox inside the menu
        // the first and only parameter is the input's placeholder text
//         $sub->withFilter('find entries ...');
    }
    
}

// END OF
