<?php

/**
 * See: system/ee/legacy/libraries/Cp.php
 */


class Art_hub_library
{

    protected $its_all_in_your_head = array();
    protected $footer = [];

    public $requests = array();
    public $loaded = array();

    public $js_files = array(
        'ui' => array(),
        'plugin' => array(),
        'file' => array(),
        'package' => array(),
        'fp_module' => array()
    );

    private $base_url = '';

    /**
     * Constructor
     *
     */
    public function __construct() {

        if(REQ == 'CP') {
            $this->base_url = ee()->config->item('cp_url') .
            QUERY_MARKER;
        } else {
            $this->base_url = ee()->functions->fetch_site_index() .
            QUERY_MARKER .
            'ACT=' . ee()->functions->fetch_action_id('Channel', 'combo_loader');
        }
        
        ee()->lang->loadfile('cp');
//         ee()->lang->loadfile('members');

        ee()->load->library('javascript');

        $this->_set_js_globals();
        $this->_load_common_js();
        
        $this->add_to_foot("<div id=\"pageModalContainer\"></div>");
    }
    
    
    private function _set_js_globals() 
    {
        $js_lang_keys = array(
            'logout' => lang('logout'),
            'search' => lang('search'),
            'session_idle' => lang('session_idle'),
            'btn_fix_errors' => lang('btn_fix_errors'),
            'btn_fix_errors' => lang('btn_fix_errors'),
            'check_all' => lang('check_all'),
            'clear_all' => lang('clear_all'),
            'keyword_search' => lang('keyword_search'),
            'loading' => lang('loading'),
            'searching' => lang('searching'),
            'dark_theme' => lang('dark_theme'),
            'light_theme' => lang('light_theme'),
            'many_jump_results' => lang('many_jump_results'),
        );
        
        ee()->javascript->set_global(array(
            'site_id' => ee()->config->item('site_id'),
            'site_name' => ee()->config->item('site_name'),
            'site_url' => ee()->config->item('site_url'),
            'site_index' => ee()->config->item('site_index'),
            'CSRF_TOKEN' => (defined('CSRF_TOKEN')) ? CSRF_TOKEN : null,
            'lang' => $js_lang_keys,
            'logged_in' => (ee()->session->userdata('member_id') != 0) ? true : false,
            'member_id' => ee()->session->userdata('member_id'),
            'username' => ee()->session->userdata('username'),
        ));
        
    }
    
    
    private function _load_common_js()
    {
        $this->load_package_js([
            'vendor/bootstrap.bundle.min',
            'vendor/angular.min',
            'main',
            'clickable',
            'tooltips',
            'jquery-ui/datepicker',
            'page-alert',
        ]);

        // $this->_seal_combo_loader();
        
        $this->add_js_script(array(
            'ui' => array(
                'core',
                'widget',
                'mouse',
                'position',
                'sortable',
                'dialog',
                'button'
            ),
            'plugin' => array(
                'ee_interact.event',
                'ee_broadcast.event',
//                 'ee_notice',
//                 'ee_txtarea',
//                 'tablesorter',
//                 'ee_toggle_all',
//                 'nestable'
            ),
            'file' => array(
                'vendor/react/react.min',
                'vendor/react/react-dom.min',
//                 'vendor/popper',
//                 'vendor/focus-visible',
//                 'vendor/underscore',
//                 'cp/global_start',
//                 'cp/form_validation',
//                 'cp/sort_helper',
//                 'cp/form_group',
//                 'bootstrap/dropdown-controller',
//                 'cp/modal_form',
//                 'cp/confirm_remove',
//                 'cp/fuzzy_filters',
//                 'cp/jump_menu',
//                 'components/no_results',
//                 'components/loading',
//                 'components/filters',
//                 'components/dropdown_button',
//                 'components/filterable',
//                 'components/toggle',
//                 'components/select_list',
//                 'fields/select/select',
//                 'fields/select/mutable_select',
//                 'fields/dropdown/dropdown'
            )
        ));

        // $this->_seal_combo_loader();
        $this->load_package_js([
            'app/main',
            'app/filters/html-unsafe',
            'app/service/ee-data',
            'app/service/data-mod',
            'app/service/request',
            'app/service/bs-modal',
            'app/controller/page-modal',
        ]);
        
        // date field
        ee()->lang->loadfile('calendar');
        ee()->javascript->set_global('date.date_format', ee()->localize->get_date_format());
        ee()->javascript->set_global('lang.date.months.full', array(
            lang('cal_january'),
            lang('cal_february'),
            lang('cal_march'),
            lang('cal_april'),
            lang('cal_may'),
            lang('cal_june'),
            lang('cal_july'),
            lang('cal_august'),
            lang('cal_september'),
            lang('cal_october'),
            lang('cal_november'),
            lang('cal_december')
        ));
        ee()->javascript->set_global('lang.date.months.abbreviated', array(
            lang('cal_jan'),
            lang('cal_feb'),
            lang('cal_mar'),
            lang('cal_apr'),
            lang('cal_may'),
            lang('cal_june'),
            lang('cal_july'),
            lang('cal_aug'),
            lang('cal_sep'),
            lang('cal_oct'),
            lang('cal_nov'),
            lang('cal_dec')
        ));
        ee()->javascript->set_global('lang.date.days', array(
            lang('cal_su'),
            lang('cal_mo'),
            lang('cal_tu'),
            lang('cal_we'),
            lang('cal_th'),
            lang('cal_fr'),
            lang('cal_sa'),
        ));

//         $this->add_js_script(array(
//             'file' => array('cp/date_picker'),
//         ));

        // $this->_seal_combo_loader();
    }
    
    
    
    
    public function fetch_action_id($class, $method)
    {
        $where = [
            'class' => $class,
            'method' => $method
        ];
        
        ee()->db->select('action_id');
        $query = ee()->db->get_where('actions', $where);
        
        if($query->num_rows() > 0) {
            $row = $query->row(0);
        }
        
        if(isset($row)) {
            return $row->action_id;
        }
        
        return null;
    }
    
    public function add_to_foot($str)
    {
        $this->footer[] = $str;
    }
    
    public function render_foot_html()
    {
        return implode("", $this->footer);
    }
    
    
    /**
     * Load Package CSS
     *
     * Load a stylesheet from a package
     *
     * @param  string
     * @return void
     */
    public function load_package_css($file)
    {
        $current_top_path = ee()->load->first_package_path();
        $package = trim(str_replace(array(PATH_THIRD, 'views'), '', $current_top_path), '/');
        
        if (REQ == 'CP') {
            $url = EESELF . AMP . 'C=css' . AMP . 'M=third_party' . AMP . 'package=' . $package . AMP . 'file=' . $file;
        } else {
            $url = $this->base_url . AMP . 'type=css' . AMP . 'package=' . $package . AMP . 'file=' . $file;
        }
        
        $data = '<link type="text/css" rel="stylesheet" href="' . $url . '" />';
        
        $this->its_all_in_your_head[] = $data;
    }
    
    
    /**
     * Returns the array of items to be added in the header
     *
     * @return array The array of items to be added in the header
     */
    public function get_head()
    {
        return $this->its_all_in_your_head;
    }


    /**
     * Load Package JS
     *
     * Load a javascript file from a package
     *
     * @param  string
     * @return void
     */
    public function load_package_js($file)
    {
        if(is_array($file)) {
            foreach($file as $f) {
                $this->load_package_js($f);
            }
            return;
        }
        
        $current_top_path = ee()->load->first_package_path();
        $package = trim(str_replace(array(PATH_THIRD, 'views'), '', $current_top_path), '/');

        $this->add_js_script(array('package' => $package . ':' . $file));
    }


    /**
     * Add JS Script
     *
     * Adds a javascript file to the javascript combo loader
     *
     * @param array - associative array of
     */
    public function add_js_script($script = array(), $in_footer = true)
    {
        if (!is_array($script)) {
            if (is_bool($in_footer)) {
                return false;
            }

            $script = array($script => $in_footer);
            $in_footer = true;
        }

        if (!$in_footer) {
            return $this->its_all_in_your_head = array_merge($this->its_all_in_your_head, $script);
        }

        foreach ($script as $type => $file) {
            if (!is_array($file)) {
                $file = array($file);
            }

            if (array_key_exists($type, $this->js_files)) {
                $this->js_files[$type] = array_merge($this->js_files[$type], $file);
            } else {
                $this->js_files[$type] = $file;
            }
        }

        return $this->js_files;
    }


    /**
     * Render Footer Javascript
     *
     * @param bool Whether to include 'common.js' automatically
     *
     * @return string
     */
    public function render_footer_js($include_common = true)
    {
        $str = '';
        $requests = $this->_seal_combo_loader($include_common);

        foreach ($requests as $req) {
            $str .= '<script type="text/javascript" charset="utf-8" src="' . $this->base_url . AMP . 'C=javascript' . AMP . 'M=combo_load' . $req . '"></script>';
        }

        // if (ee()->extensions->active_hook('cp_js_end') === true) {
        //     $str .= '<script type="text/javascript" src="' . BASE . AMP . 'C=javascript' . AMP . 'M=load' . AMP . 'file=ext_scripts"></script>';
        // }

        return $str;
    }


    /**
     * Seal the current combo loader and reopen a new one.
     *
     * @param bool Whether to include 'common.js' automatically
     * @access private
     * @return array
     */
    public function _seal_combo_loader($include_common = true)
    {
        $str = '';
        $mtimes = array();

        if ($include_common) {
            $this->add_js_script([
                'file' => [
                    'common'
                ]
            ]);
        }

        $this->js_files = array_map('array_unique', $this->js_files);

        foreach ($this->js_files as $type => $files) {
            if (isset($this->loaded[$type])) {
                $files = array_diff($files, $this->loaded[$type]);
            }

            if (count($files)) {
                $mtimes[] = $this->_get_js_mtime($type, $files);
                $str .= AMP . $type . '=' . implode(',', $files);
            }
        }

        if ($str) {
            $this->loaded = array_merge_recursive($this->loaded, $this->js_files);

            $this->js_files = array(
                'ui' => array(),
                'plugin' => array(),
                'file' => array(),
                'package' => array(),
                'fp_module' => array(),
                'pro_file' => array()
            );

            $this->requests[] = $str . AMP . 'v=' . max($mtimes);
        }

        return $this->requests;
    }


    /**
     * Get last modification time of a js file.
     * Returns highest if passed an array.
     *
     * @param string
     * @param mixed
     * @return int
     */
    public function _get_js_mtime($type, $name)
    {
        if (is_array($name)) {
            $mtimes = array();

            foreach ($name as $file) {
                $mtimes[] = $this->_get_js_mtime($type, $file);
            }

            return max($mtimes);
        }

        switch ($type) {
            case 'ui':
                $file = PATH_THEMES_GLOBAL_ASSET . 'javascript/' . PATH_JS . '/jquery/ui/jquery.ui.' . $name . '.js';
                break;

            case 'plugin':
                $file = PATH_THEMES_GLOBAL_ASSET . 'javascript/' . PATH_JS . '/jquery/plugins/' . $name . '.js';
                break;

            case 'file':
                $file = PATH_THEMES_GLOBAL_ASSET . 'javascript/' . PATH_JS . '/' . $name . '.js';
                break;

            case 'pro_file':
                $file = PATH_PRO_THEMES . 'js/' . $name . '.js';
                break;

            case 'package':
                if (strpos($name, ':') !== false) {
                    list($package, $name) = explode(':', $name);
                } else {
                    $package = $name;
                }

                $file = PATH_THIRD . $package . '/javascript/' . $name . '.js';
                break;

            case 'fp_module':
                $file = PATH_ADDONS . $name . '/javascript/' . $name . '.js';
                break;

            default:
                return 0;
        }

        return file_exists($file) ? filemtime($file) : 0;
    }


}
