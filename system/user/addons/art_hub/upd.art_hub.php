<?php

use ExpressionEngine\Service\Addon\Installer;

class Art_hub_upd extends Installer
{

    public $has_cp_backend = 'n';
    public $has_publish_fields = 'n';
    
    // defines the module's actions that should be installed.
    public $actions = [
        [
            'class' => 'Art_hub',
            'method' => 'get_view',
        ],
        [
            'class' => 'Art_hub',
            'method' => 'upload_file',
        ],
        [
            'class' => 'Art_hub',
            'method' => 'send_message',
        ]
    ];
    
    
    /**
     * Constructor alone will install module and actions.
     */
    public function __construct()
    {
        parent::__construct();
        ee()->load->library('Art_hub_db');
    }
    
    /**
     * install() and uninstall() are optional functions.
     * Only use if additional install or uninstall functionality is needed.
     * If needed, must include parent::__construct();
     */
    
    public function install()
    {
        ee()->art_hub_db->install();
        return parent::install();
    }
    
    public function uninstall()
    {
        //ee()->art_hub_db->uninstall();
        return parent::uninstall();
    }

}

// END OF
