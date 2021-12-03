<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ExpressionEngine Config Items
// Find more configs and overrides at
// https://docs.expressionengine.com/latest/general/system-configuration-overrides.html

$config['app_version'] = '6.1.6';
$config['encryption_key'] = '5b2fe40a6bb45fcc0efdf3eb132b4735d6faf175';
$config['session_crypt_key'] = 'ba172f405a1c1ba27353bfa7dcc7b16ca7cfedf2';
$config['database'] = array(
    'expressionengine' => array(
        'hostname' => 'localhost',
        'database' => 'db_name',
        'username' => 'db_user',
        'password' => 'db_pass',
        'dbprefix' => 'exp_',
        'char_set' => 'utf8mb4',
        'dbcollat' => 'utf8mb4_unicode_ci',
        'port'     => ''
    ),
);

/*
 * --------------------------------------------------------------------
 * Helper Values
 * --------------------------------------------------------------------
 */
$yes                            = 'y';
$no                             = 'n';
$mk_str                         = function($i) { return strval($i); };

/*
 * --------------------------------------------------------------------
 * Path and URL Settings
 * --------------------------------------------------------------------
 */
$http_host                      = $_SERVER['HTTP_HOST'];
$protocol                       = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
$base_url                       = $protocol . $http_host;
$base_path                      = $_SERVER['DOCUMENT_ROOT'];

switch ($http_host) {
    case 'csci331.cs.montana.edu':
        $base_path = '/home/b62v473/public_html/ArtHub';
        $base_url = $protocol . 'csci331.cs.montana.edu/~b62v473/ArtHub';
        break;
}

$images_folder                  = 'images';
$base_path_var                  = '{base_path}';
$base_url_var                   = '{base_url}';
$images_path                    = $base_path . '/' . $images_folder . '/';
$images_url                     = $base_url . '/' . $images_folder . '/';

// --------------------------------------------------------------------

$config['index_page']           = 'index.php';
$config['site_index']           = $config['index_page'];
$config['base_url']             = $base_url . '/';
$config['base_path']            = $base_path . '/';
$config['site_url']             = $base_url_var;
$config['cp_url']               = $base_url_var . 'admin.php';

$config['theme_folder_path']    = $base_path_var . 'themes/';
$config['theme_folder_url']     = $base_url_var . 'themes/';
$config['emoticon_path']        = $base_path_var . 'images/smileys/';
$config['emoticon_url']         = $base_url_var . 'images/smileys/';
$config['captcha_path']         = $base_path_var . 'images/captchas/';
$config['captcha_url']          = $base_url_var . 'images/captchas/';
$config['avatar_path']          = $base_path_var . 'images/avatars/';
$config['avatar_url']           = $base_url_var . 'images/avatars/';
$config['photo_path']           = $base_path_var . 'images/member_photos/';
$config['photo_url']            = $base_url_var . 'images/member_photos/';
$config['sig_img_path']         = $base_path_var . 'images/signature_attachments/';
$config['sig_img_url']          = $base_url_var . 'images/signature_attachments/';
$config['prv_msg_upload_path']  = $base_path_var . 'images/pm_attachments/';
$config['prv_msg_upload_url']   = $base_url_var . 'images/pm_attachments/';

// --------------------------------------------------------------------

$config['site_license_key']             = '';
$config['is_system_on']                 = $yes;
$config['is_site_on']                   = $yes;
$config['show_ee_news']                 = $no;
$config['share_analytics']              = $no;
$config['multiple_sites_enabled']       = $no;
$config['new_version_check']            = $no;
$config['updater_allow_advanced']       = 'n';
$config['spellcheck_language_code']     = 'en';
$config['xml_lang']                     = 'en';

$config['filename_increment']           = 'y'; # (default: 'n' - Allow duplicate filename) Forces filenames of uploaded files to be unique. Secondary uploads of existing files or uploads that share a filename with an existing file will have an incrementing number appended to the filename.


$config['sc_paypal_account']            = '';
$config['sc_encrypt_buttons']           = 'n';
$config['sc_certificate_id']            = '';
$config['sc_public_certificate']        = '';
$config['sc_private_key']               = '';
$config['sc_paypal_certificate']        = '';
$config['sc_temp_path']                 = '/tmp';
$config['legacy_member_templates']      = 'n';
$config['redirect_method']              = 'redirect';

// tracking
$config['disable_all_tracking']         = 'n';
$config['relaxed_track_views']          = 'y'; # Allow Entry Views Tracking to work for ANY combination that results in only one entry being returned by the tag, including Channel query caching.
$config['log_referrers']                = 'y'; # Enable referrer tracking. When enabled, one additional database access query will be performed for each page load so that the statistics can be generated.
$config['enable_online_user_tracking']  = 'y'; # If enabled, online user statistics are tracked and the user-based variables in the Statistics module are available for use.
$config['dynamic_tracking_disabling']   = '1000'; # Set a value for the maximum number of online visitors to track.
$config['enable_entry_view_tracking']   = 'y';
$config['enable_hit_tracking']          = 'y';
$config['ignore_entry_stats']           = 'n'; # Default: n - Disable entry stats and analytics being saved during creating/updating of entries when using models. Disabling entry stats can lead to improved performance when using models
$config['log_threshold']                = '4';

// dev/debug
$config['show_profiler']                = $no; # y/n
$config['debug']                        = '1'; # '0': Hide PHP/SQL error messages, '1':Show PHP/SQL error messages to only Super Admin users, '2': Show PHP/SQL error messages all users NOT SECURE
$config['enable_devlog_alerts']         = 'n';
$config['db_backup_row_limit']          = 4000; # (default: 4000) When using the Database Backup Utility, some databases and PHP configurations may cause the backup utility to run out of memory while creating the backup. This config sets the maximum number of rows that will be queried and written to the backup file at a time. If you run into an out-of-memory error, try setting this to a lower number than the default to have the utility work in smaller batches.

// cookies
$config['expire_session_on_browser_close'] = 'n'; # Set the system to end a user’s session when the browser is closed.
$config['cp_session_type']              = 'c'; # Set the method for session handling in the Control Panel. ('c': Use cookies only, 's': Use session ID only, 'cs': Use both cookies and session ID (default))
$config['cookie_httponly']              = $yes;
$config['cookie_domain']                = $mk_str('');
$config['cookie_prefix']                = '';
$config['cookie_secure']                = 'n';
$config['require_cookie_consent']       = 'n';

// caching
$config['cache_driver']                 = $mk_str('file');
$config['cache_driver_backup']          = $mk_str('file');
$config['enable_sql_caching']           = $no; # Improves the speed at which the Channel Entries tag is rendered by caching queries that are normally executed dynamically.
$config['disable_tag_caching']          = $no; # Warning: Use only under extreme circumstances. Disables tag caching, which if used unwisely on a high traffic site can lead to disastrous disk i/o. This setting allows quick thinking admins to temporarily disable it without hacking or modifying folder permissions.
$config['new_posts_clear_caches']       = $yes;

$config['default_site_timezone']        = $mk_str('America/Denver');
$config['date_format']                  = '%n/%j/%Y'; # Set the default format for displaying dates. See: https://docs.expressionengine.com/latest/templates/date-variable-formatting.html#date-formatting-codes
$config['time_format']                  = '12';
$config['include_seconds']              = 'n';
$config['enable_throttling']            = $no; # If enabled, the system will throttle excessive web requests from potentially malicious users.
$config['lockout_time']                 = '10';
$config['max_page_loads']               = '15';
$config['time_interval']                = '8';


// members
$config['allow_avatar_uploads']         = $yes; # Set whether members can upload their own avatar.
$config['enable_avatars']               = $yes;
$config['avatar_max_height']            = '2500'; # Set the maximum height (in pixels) allowed for user-uploaded avatars.
$config['avatar_max_width']             = '2500'; # Set the maximum width (in pixels) allowed for user-uploaded avatars.
$config['avatar_max_kb']                = '5000'; # Set the maximum file size (in kilobytes) allowed for user-uploaded avatars.
$config['allow_member_localization']    = $yes; # Set whether dates and times are localized to each members’ own localization preferences.
$config['allow_signatures']             = 'n'; # Set whether member signatures are enabled.
$config['allow_username_change']        = 'y'; # Set whether members can change their own usernames after registration.

$config['password_lockout']             = $no;
$config['password_lockout_interval']    = '1';


$config['allow_member_registration']    = $yes; # Set whether site visitors are allowed to register for accounts.
$config['allow_multi_logins']           = $yes; # Set whether an account can have multiple active sessions at one time.
$config['allow_pending_login']          = $yes; # Set whether members of the Pending member role can log in or not. By default, Pending members cannot log in.

$config['req_mbr_activation']           = 'none'; # 'email': Require email verification for new member accounts, 'manual': Require administrator’s approval
$config['require_terms_of_service']     = 'y'; # Require new members to agree to your terms of service upon registration.
$config['new_member_notification']      = 'n';
$config['mbr_notification_emails']      = 'rkelly.msu@gmail.com';
$config['default_primary_role']         = '5';
$config['prv_msg_throttling_period']    = $mk_str(0); # Set the length of time users must wait between sending private messages.
$config['profile_trigger']              = substr(md5(microtime()),rand(0,26),8); // $mk_str('member');
$config['legacy_member_templates']      = 'y';
$config['member_theme']                 = 'default';
$config['anonymize_consent_logs']       = '';



$config['require_captcha']              = 'n'; # When enabled, site visitors will be required to pass a CAPTCHA to submit any front-end form, including Channel Form, comment forms, and member registrations.
$config['captcha_rand']                 = 'y'; # Specify whether to add a random three-digit number to the end of each generated CAPTCHA word. This makes it more difficult for scripts to guess or brute-force the form submission.
$config['captcha_require_members']      = 'n'; # Specify whether to require logged-in members to pass CAPTCHA validation to submit front-end forms, such as Channel Form, comment forms and email forms.
$config['require_ip_for_login']         = $yes;
$config['require_ip_for_posting']       = $yes;
$config['deny_duplicate_data']          = $yes;
$config['require_secure_passwords']     = 'n'; # Require users’ passwords to contain at least one uppercase character, one lowercase character, and one numeric character. Passwords that follow this basic formula are much more difficult to guess.
$config['pw_min_len']                   = '5'; # Set the minimum number of characters allowed for member passwords.
$config['un_min_len']                   = '5';
$config['allow_dictionary_pw']          = 'n'; # Set whether words commonly found in the dictionary can be used as passwords. Must be used in combination with name_of_dictionary_file.
$config['name_of_dictionary_file']      = __DIR__ . '/dictionary.txt'; # Filename for the dictionary file.
$config['memberlist_order_by']          = 'member_id'; # Set the default sorting criteria for the member list.
$config['memberlist_row_limit']         = '50';
$config['memberlist_sort_order']        = 'desc';

$config['enable_censoring']             = 'n'; # If enabled, the system will censor any specified words in channel entries, comments, forum posts, etc. Censored words will be replaced with the censoring replacement word.
$config['censor_replacement']           = '';
$config['censored_words']               = ''; # Specify a list of words to censor. Wildcards are allowed. For example, test* would censor the words “test”, “testing”, and “tester”, while *gress would censor the words “progress” and “congress”.


// forum
$config['forum_is_installed']           = 'y';
$config['use_forum_url']                = 'n'; # Set the system to use the forum URL specified in the forum board preferences (https://docs.expressionengine.com/latest/add-ons/forum/boards.html#forum-url) rather than the the main site URL to form the forum’s URL.
// $config['forum_trigger']                = 'eerox'; # Sets the forum triggering word if the Discussion Forum module is installed. (https://docs.expressionengine.com/latest/add-ons/forum/index.html)

$config['codemirror_height']            = '80vh';
// $config['code_block_pre'] = '<div class="codeblock">';
// $config['code_block_post'] = '</div>';

// publish entries
$config['allow_textarea_tabs']          = 'y'; # (default: y) Set whether a tab keystroke produces a tab in Publish Page and Template Editor textareas. This is the default behavior.
$config['allowed_preview_domains']      = ''; # 'example1.com,example2.com' List extra domains that can be used to show live preview. You will need this if your site or CP is using domain that is not configured as Site URL or CP URL. Can be array or comma-separated string.
$config['auto_assign_cat_parents']      = 'y'; # Set whether to assign an entry to both the selected category and its parent category.
$config['autosave_interval_seconds']    = '5000'; # Set the interval between autosaves on the Publish Page.
$config['autosave_prune_hours']         = '1'; # Set the age at which Channel Entry autosaves are automatically deleted.
$config['channel_form_overwrite']       = 'n'; # Allows Channel Form authors to overwrite their own files only when uploading files named the same as files previously uploaded by that author.

// templates
$config['strict_urls']                  = 'y';
$config['save_tmpl_files']              = $yes;
$config['save_tmpl_globals']            = $yes;
$config['save_tmpl_revisions']          = $yes;
$config['max_tmpl_revisions']           = $mk_str('5000');
$config['enable_template_routes']       = $yes;
$config['remove_unparsed_vars']         = $no;
$config['max_url_segments']             = '15';
$config['hidden_template_indicator']    = $mk_str('_');
$config['word_separator']               = $mk_str('dash');
$config['allow_php']                    = 'y'; # (default: n) Set whether the toggle to enable/disable PHP in templates is displayed.
$config['site_404']                     = $mk_str('home/404');
$config['force_query_string']           = 'n';
$config['use_category_name']            = 'n';

// email
$config['webmaster_name']       = $mk_str('ArtHub: CSCI-331 Final Project');
$config['webmaster_email']      = $mk_str('support@csci331-final-project.rk311y.com');
$config['smtp_username']        = $mk_str('support@csci331-final-project.rk311y.com');
$config['smtp_password']        = $mk_str('EH3O;Pj6u0_d');
$config['mail_protocol']        = $mk_str('smtp');
$config['smtp_port']            = $mk_str(465);
$config['smtp_server']          = $mk_str('mail.high-altitude-tech.com');
$config['email_smtp_crypto']    = $mk_str('ssl');
$config['mail_format']          = 'html';
$config['email_charset']        = $mk_str('utf-8');

/*
 * --------------------------------------------------------------------
 * Upload Preferences
 * --------------------------------------------------------------------
 */
$config['upload_preferences'] = array(
    // Art Posts
    5 => array(
        'name'        => 'Art Posts',
        'server_path' => $base_path_var . 'images/uploads/art_posts/',
        'url'         => $base_url_var . 'images/uploads/art_posts/'
    )
);

/*
 * --------------------------------------------------------------------
 * Creating Dynamic Database Connections
 * --------------------------------------------------------------------
 */
$dbConnection = [];

switch ($_SERVER['HTTP_HOST']) {
    // Course Server
    case 'csci331.cs.montana.edu':
        $dbConnection = array(
            'database' => 'db36',
            'username' => 'user36',
            'password' => '36oxon'
        );
        break;
    // local instance
    default:
        $dbConnection = array(
            'database' => 'art_hub',
            'username' => 'csci331',
            'password' => 'pass123!!',
        );
        break;
}

$config['database']['expressionengine'] = array_merge($config['database']['expressionengine'], $dbConnection);

// EOF