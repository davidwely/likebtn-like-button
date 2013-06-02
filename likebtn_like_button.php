<?php
/*
  Plugin Name: LikeBtn Like Button
  Plugin URI: http://www.likebtn.com
  Description: <strong><a href="http://www.likebtn.com" target="_blank" title="Like Button">LikeBtn.com</a></strong> - is the service providing a fully customizable like button widget for websites. The Like Button can be installed on any website for FREE. The service also offers a range of plans giving access to additional options and tools - see <a href="http://www.likebtn.com/en/#plans_pricing" target="_blank" title="Like Button Plans">Plans & Pricing</a>. This module allows to integrate the LikeBtn Like Button into your WordPress website to allow visitors to like and dislike pages, posts and comments anonymously.
  Version: 1.1
  Author: likebtn
  Author URI: http://www.likebtn.com
 */

// i18n domain
define('LIKEBTN_LIKE_BUTTON_I18N_DOMAIN', 'likebtn_like_button');

// LikeBtn plans
define('LIKEBTN_LIKE_BUTTON_PLAN_FREE', 0);
define('LIKEBTN_LIKE_BUTTON_PLAN_PLUS', 1);
define('LIKEBTN_LIKE_BUTTON_PLAN_PRO', 2);
define('LIKEBTN_LIKE_BUTTON_PLAN_VIP', 3);

define('LIKEBTN_LIKE_BUTTON_ENTITY_POST', 'post');
define('LIKEBTN_LIKE_BUTTON_ENTITY_PAGE', 'page');
define('LIKEBTN_LIKE_BUTTON_ENTITY_COMMENT', 'comment');

// position
define('LIKEBTN_LIKE_BUTTON_POSITION_TOP', 'top');
define('LIKEBTN_LIKE_BUTTON_POSITION_BOTTOM', 'bottom');
define('LIKEBTN_LIKE_BUTTON_POSITION_BOTH', 'both');

// alignment
define('LIKEBTN_LIKE_BUTTON_ALIGNMENT_LEFT', 'left');
define('LIKEBTN_LIKE_BUTTON_ALIGNMENT_RIGHT', 'right');

// post full/excerpt
define('LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_FULL', 'full');
define('LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_EXCERPT', 'excerpt');
define('LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_BOTH', 'both');

// entities for which plugin can be enabled
global $likebtn_like_button_entities;
$likebtn_like_button_entities = _likebtn_like_button_get_entities();
//$likebtn_like_button_entities = array(
//    LIKEBTN_LIKE_BUTTON_ENTITY_POST => 'Post',
//    LIKEBTN_LIKE_BUTTON_ENTITY_PAGE => 'Page',
//    LIKEBTN_LIKE_BUTTON_ENTITY_COMMENT => 'Comments',
//);
// likebtn settings
global $likebtn_like_button_settings;
$likebtn_like_button_settings = array(
    "lang" => array("default" => "en"),
    "share_enabled" => array("default" => '1'),
    "show_like_label" => array("default" => '1'),
    "show_dislike_label" => array("default" => '0'),
    "dislike_share" => array("default" => '0'),
    "dislike_enabled" => array("default" => '1'),
    "counter_clickable" => array("default" => '0'),
    "counter_type" => array("default" => "number"),
    "display_only" => array("default" => '0'),
    "substract_dislikes" => array("default" => '0'),
    "unlike_allowed" => array("default" => '1'),
    "style" => array("default" => 'white'),
    "addthis_pubid" => array("default" => ''),
    "addthis_service_codes" => array("default" => ''),
    "popup_enabled" => array("default" => '1'),
    "i18n_like" => array("default" => ''),
    "i18n_dislike" => array("default" => ''),
    "i18n_like_tooltip" => array("default" => ''),
    "i18n_dislike_tooltip" => array("default" => ''),
    "i18n_unlike_tooltip" => array("default" => ''),
    "i18n_undislike_tooltip" => array("default" => ''),
    "i18n_share_text" => array("default" => ''),
    "i18n_popup_close" => array("default" => ''),
    "i18n_popup_text" => array("default" => '')
);

// plans
global $likebtn_like_button_plans;
$likebtn_like_button_plans = array(
    LIKEBTN_LIKE_BUTTON_PLAN_FREE => 'FREE',
    LIKEBTN_LIKE_BUTTON_PLAN_PLUS => 'PLUS',
    LIKEBTN_LIKE_BUTTON_PLAN_PRO => 'PRO',
    LIKEBTN_LIKE_BUTTON_PLAN_VIP => 'VIP',
);

// styles
global $likebtn_like_button_styles;
$likebtn_like_button_styles = array(
    "white",
    "lightgray",
    "gray",
    "black",
    "padded",
    "drop",
    "line",
    "transparent",
    "youtube",
    "habr",
    "heartcross",
    "plusminus",
    "google",
    "greenred",
    "large"
);

// languages
global $likebtn_like_button_languages;
$likebtn_like_button_languages = array(
    'auto' => 'auto - ' . __("Detect from client browser", LIKEBTN_LIKE_BUTTON_I18N_DOMAIN),
    'en' => 'en - English',
    'ru' => 'ru - Русский (Russian)',
    'de' => 'de - Deutsch (German)',
    'ja' => 'ja - 日本語 (Japanese)',
    'uk' => 'uk - Українська мова (Ukrainian)',
);

###############
### Backend ###
###############
// i18n function
/* function likebtn_like_button_trans($text, $params = null) {
  if (!is_array($params)) {
  $params = func_get_args();
  $params = array_slice($params, 1);
  }
  return vsprintf(__($text, LIKEBTN_LIKE_BUTTON_I18N_DOMAIN), $params);
  } */

// initicalization
function likebtn_like_button_init() {
    load_plugin_textdomain(LIKEBTN_LIKE_BUTTON_I18N_DOMAIN, false, dirname(plugin_basename(__FILE__)) . '/languages/');
    wp_enqueue_script('jquery');
}

add_action('init', 'likebtn_like_button_init');

// add Settings link to the plugin list page
function likebtn_like_button_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="options-general.php?page=likebtn_like_button_settings">' . __('Settings', 'likebtn_like_button') . '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

add_filter('plugin_action_links', 'likebtn_like_button_links', 10, 2);

// admin options
function likebtn_like_button_admin_menu() {
    $logo_url = _likebtn_like_button_get_public_url() . 'img/menu_icon.png';

    add_menu_page(__('Settings', 'likebtn_like_button'), 'LikeBtn', 'manage_options', 'likebtn_like_button_settings', '', $logo_url);
    //add_options_page('LikeBtn Like Button', __('LikeBtn Like Button', 'likebtn_like_button'), 'activate_plugins', 'likebtn_like_button', 'likebtn_like_button_admin_content');
    add_submenu_page(
        'likebtn_like_button_settings', __('Settings', 'likebtn_like_button') . ' ‹ ' . __('LikeBtn Like Button', 'likebtn_like_button'), __('Settings', 'likebtn_like_button'), 'manage_options', 'likebtn_like_button_settings', 'likebtn_like_button_admin_settings'
    );
    add_submenu_page(
        'likebtn_like_button_settings', __('Results', 'likebtn_like_button') . ' ‹ LikeBtn Like Button', __('Results', 'likebtn_like_button'), 'manage_options', 'likebtn_like_button_results', 'likebtn_like_button_admin_results'
    );
}

add_action('admin_menu', 'likebtn_like_button_admin_menu');

// plugin header
function likebtn_like_button_admin_head() {
    $url_css = _likebtn_like_button_get_public_url() . 'css/admin.css?v=' . _likebtn_like_button_get_plugin_version();
    $url_js = _likebtn_like_button_get_public_url() . 'js/admin.js?v=' . _likebtn_like_button_get_plugin_version();
    echo '<link rel="stylesheet" type="text/css" href="' . $url_css . '" />';
    echo '<script src="' . $url_js . '" type="text/javascript"></script>';
}

add_action('admin_head', 'likebtn_like_button_admin_head');

// admin header
function likebtn_like_button_admin_header() {
    $logo_url = _likebtn_like_button_get_public_url() . 'img/logotype.png';
    $header = <<<HEADER
    <div class="wrap" id="likebtn_like_button">
        <h2 class="likebtn_logo">
            <a href="http://www.likebtn.com" target="_blank" title="LikeBtn Like Button">
                <img alt="" src="{$logo_url}">LikeBtn
            </a>
        </h2>
HEADER;

    $header .= '
        <h2 class="nav-tab-wrapper">
            <a class="nav-tab ' . ($_GET['page'] == 'likebtn_like_button_settings' ? 'nav-tab-active' : '') . '" href="/wp-admin/admin.php?page=likebtn_like_button_settings">' . __('Settings', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) . '</a>
            <a class="nav-tab ' . ($_GET['page'] == 'likebtn_like_button_results' ? 'nav-tab-active' : '') . '" href="/wp-admin/admin.php?page=likebtn_like_button_results">' . __('Results', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) . '</a>
        </h2>';
    echo $header;
}

// uninstall hook
function likebtn_like_button_unistall() {
    global $likebtn_like_button_entities;
    global $likebtn_like_button_settings;

    // set default values for options
    delete_option('likebtn_like_button_plan');
    delete_option('likebtn_like_button_account_email');
    delete_option('likebtn_like_button_account_api_key');
    foreach ($likebtn_like_button_entities as $entity_name => $entity_title) {
        delete_option('likebtn_like_button_show_' . $entity_name);
        delete_option('likebtn_like_button_use_settings_from_' . $entity_name);
        delete_option('likebtn_like_button_post_view_mode_' . $entity_name);
        delete_option('likebtn_like_button_post_format_' . $entity_name);
        delete_option('likebtn_like_button_exclude_sections_' . $entity_name);
        delete_option('likebtn_like_button_exclude_categories_' . $entity_name);
        delete_option('likebtn_like_button_allow_ids_' . $entity_name);
        delete_option('likebtn_like_button_exclude_ids_' . $entity_name);
        delete_option('likebtn_like_button_position_' . $entity_name);
        delete_option('likebtn_like_button_alignment_' . $entity_name);
        // settings
        foreach ($likebtn_like_button_settings as $option => $option_info) {
            delete_option('likebtn_like_button_settings_' . $option . '_' . $entity_name);
        }
    }
}

register_uninstall_hook(__FILE__, 'likebtn_like_button_unistall');

// activation hook
function likebtn_like_button_activation_hook() {

    global $likebtn_like_button_entities;
    global $likebtn_like_button_settings;

    // set default values for options
    add_option('likebtn_like_button_plan', LIKEBTN_LIKE_BUTTON_PLAN_FREE);
    add_option('likebtn_like_button_account_email', '');
    add_option('likebtn_like_button_account_api_key', '');

    foreach ($likebtn_like_button_entities as $entity_name => $entity_title) {
        add_option('likebtn_like_button_show_' . $entity_name, '0');
        add_option('likebtn_like_button_use_settings_from_' . $entity_name, '');
        add_option('likebtn_like_button_post_view_mode_' . $entity_name, LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_BOTH);
        add_option('likebtn_like_button_post_format_' . $entity_name, 'all');
        add_option('likebtn_like_button_exclude_sections_' . $entity_name, array());
        add_option('likebtn_like_button_exclude_categories_' . $entity_name, array());
        add_option('likebtn_like_button_allow_ids_' . $entity_name, '');
        add_option('likebtn_like_button_exclude_ids_' . $entity_name, '');
        add_option('likebtn_like_button_position_' . $entity_name, LIKEBTN_LIKE_BUTTON_POSITION_BOTTOM);
        add_option('likebtn_like_button_alignment_' . $entity_name, LIKEBTN_LIKE_BUTTON_ALIGNMENT_LEFT);
        // settings
        foreach ($likebtn_like_button_settings as $option => $option_info) {
            add_option('likebtn_like_button_settings_' . $option . '_' . $entity_name, $option_info['default']);
        }
    }
}

register_activation_hook(__FILE__, 'likebtn_like_button_activation_hook');

// registering settings
function likebtn_like_button_register_settings() {
    global $likebtn_like_button_entities;
    global $likebtn_like_button_settings;

    register_setting('likebtn_like_button_settings', 'likebtn_like_button_plan');
    register_setting('likebtn_like_button_settings', 'likebtn_like_button_account_email');
    register_setting('likebtn_like_button_settings', 'likebtn_like_button_account_api_key');

    // entities settings
    foreach ($likebtn_like_button_entities as $entity_name => $entity_title) {
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_show_' . $entity_name);
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_use_settings_from_' . $entity_name);
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_post_view_mode_' . $entity_name);
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_post_format_' . $entity_name);
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_exclude_sections_' . $entity_name);
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_exclude_categories_' . $entity_name);
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_allow_ids_' . $entity_name);
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_exclude_ids_' . $entity_name);
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_position_' . $entity_name);
        register_setting('likebtn_like_button_settings', 'likebtn_like_button_alignment_' . $entity_name);

        // settings
        foreach ($likebtn_like_button_settings as $option => $option_info) {
            register_setting('likebtn_like_button_settings', 'likebtn_like_button_settings_' . $option . '_' . $entity_name);
        }
    }
}

add_action('admin_init', 'likebtn_like_button_register_settings');

// admin content
function likebtn_like_button_admin_settings() {

    global $likebtn_like_button_plans;
    global $likebtn_like_button_entities;
    global $likebtn_like_button_styles;
    global $likebtn_like_button_languages;

    // retrieve post formats
    $post_formats = _likebtn_like_button_get_post_formats();

    //likebtn_like_button_unistall();

    likebtn_like_button_admin_header();
    ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <form method="post" action="options.php">
            <?php settings_fields('likebtn_like_button_settings'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label><?php echo __('Website Tariff Plan', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                    <td>
                        <select name="likebtn_like_button_plan">
                            <?php foreach ($likebtn_like_button_plans as $plan_id => $plan_name): ?>
                                <option value="<?php echo $plan_id; ?>" <?php if (get_option('likebtn_like_button_plan') == $plan_id): ?>selected="selected"<?php endif ?> ><?php echo $plan_name; ?></option>
                            <?php endforeach ?>
                        </select>

                        <span class="description"><?php echo __('Specify your website <a href="http://www.likebtn.com/en/#plans_pricing" target="_blank">plan</a>, the plan specified determines available settings', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) ?></span>
                        <br/>
                        <div class="description">
                            <?php echo __('Options marked with tariff plan name (PLUS, PRO, VIP) are available only if your website is upgraded to the corresponding plan. Keep in mind that only websites upgraded to <a href="http://www.likebtn.com/en/#plans_pricing" target="_blank">PLUS</a> plan or higher are allowed to display more then 3 Like Buttons per page.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) ?><br/><br/>
                            <a href="javascript:toggleToUpgrade();void(0);"><?php echo __('To upgrade your website...', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) ?></a>
                            <ol id="likebtn_like_button_to_upgrade" class="hidden">
                                <li><?php echo __('Register on <a href="http://www.likebtn.com/en/customer.php/register/" target="_blank">LikeBtn.com</a>', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) ?></li>
                                <li><?php echo __('Add your website to your account and activate it on <a href="http://www.likebtn.com/en/customer.php/websites" target="_blank">Websites page</a>', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) ?></li>
                                <li><?php echo __('Upgrade your website to the desired plan.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) ?></li>
                            </ol>
                        </div>
                    </td>
                </tr>
            </table>

            <br/>

            <div class="postbox">
                <h3><?php echo __('Your account data on LikeBtn.com (PRO, VIP)', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></h3>
                <div class="inside">
                    <?php echo __('Enter this information to enable Results and other features which need it to fetch likes from LikeBtn.com into your database.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label><?php echo __('E-mail', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                            <td>
                                <input type="text" name="likebtn_like_button_account_email" value="<?php echo get_option('likebtn_like_button_account_email') ?>" size="60"/>
                                <span class="description"><?php echo __('Your LikeBtn.com account email. Can be found on <a href="http://www.likebtn.com/en/customer.php/profile/edit" target="_blank">Profile page</a>', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label><?php echo __('API key', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                            <td>
                                <input type="text" name="likebtn_like_button_account_api_key" value="<?php echo get_option('likebtn_like_button_account_api_key') ?>" size="60"/>
                                <span class="description"><?php echo __('Your website API key on LikeBtn.com. Can be obtained on <a href="http://www.likebtn.com/en/customer.php/websites" target="_blank">Websites page</a>', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN) ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <?php
            foreach ($likebtn_like_button_entities as $entity_name => $entity_title):

                $excluded_sections = get_option('likebtn_like_button_exclude_sections_' . $entity_name);
                $excluded_categories = get_option('likebtn_like_button_exclude_categories_' . $entity_name);
                ?>

                <div class="postbox">
                    <h3><?php echo __($entity_title, LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></h3>
                    <div class="inside">

                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><label><?php echo __('Show Like Button', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                <td>
                                    <input type="checkbox" name="likebtn_like_button_show_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_show_' . $entity_name)); ?> onClick="entityShowChange(this, '<?php echo $entity_name; ?>')" />
                                </td>
                            </tr>
                        </table>

                        <div id="entity_container_<?php echo $entity_name; ?>" <?php if (!get_option('likebtn_like_button_show_' . $entity_name)): ?>style="display:none"<?php endif ?>>
                            <table class="form-table" >
                                <tr valign="top">
                                    <th scope="row"><label><?php echo __('Use settings from', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                    <td>
                                        <select name="likebtn_like_button_use_settings_from_<?php echo $entity_name; ?>" onChange="userSettingsFromChange(this, '<?php echo $entity_name; ?>')">
                                            <option value="" <?php selected('', get_option('likebtn_like_button_use_settings_from_' . $entity_name)); ?> >&nbsp;</option>
                                            <?php foreach ($likebtn_like_button_entities as $use_entity_name => $use_entity_title): ?>
                                                <?php
                                                if ($use_entity_name == $entity_name) {
                                                    continue;
                                                }
                                                ?>
                                                <option value="<?php echo $use_entity_name; ?>" <?php selected($use_entity_name, get_option('likebtn_like_button_use_settings_from_' . $entity_name)); ?> ><?php echo $use_entity_title; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <span class="description"><?php echo __('Choose the entity you want to copy the Like Button settings from or leave it blank if you want to configure the Like Button.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></span>
                                    </td>
                                </tr>
                            </table>
                            <div class="postbox" id="use_settings_from_container_<?php echo $entity_name; ?>" <?php if (get_option('likebtn_like_button_use_settings_from_' . $entity_name)): ?>style="display:none"<?php endif ?>>
                                <h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_like_button_collapse_trigger"><small>►</small> <?php echo __('Settings', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></h3>

                                <div class="inside hidden" >
                                    <table class="form-table" >

                                        <tr valign="top">
                                            <th scope="row"><label><?php echo __('Post view mode', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                            <td>
                                                <input type="radio" name="likebtn_like_button_post_view_mode_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_FULL; ?>" <?php checked(LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_FULL, get_option('likebtn_like_button_post_view_mode_' . $entity_name)) ?> /> <?php echo __('Full', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?>&nbsp;&nbsp;&nbsp;
                                                <input type="radio" name="likebtn_like_button_post_view_mode_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_EXCERPT; ?>" <?php checked(LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_EXCERPT, get_option('likebtn_like_button_post_view_mode_' . $entity_name)) ?> /> <?php echo __('Excerpt', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?>&nbsp;&nbsp;&nbsp;
                                                <input type="radio" name="likebtn_like_button_post_view_mode_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_BOTH; ?>" <?php checked(LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_BOTH, get_option('likebtn_like_button_post_view_mode_' . $entity_name)) ?> /> <?php echo __('Both', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?>


                                                <br/>
                                                <span class="description"><?php echo __('Choose Post display mode for which you want to show the Like Button.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></span>
                                            </td>
                                        </tr>

                                        <tr valign="top">
                                            <th scope="row"><label><?php echo __('Post format', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                            <td>
                                                <input type="checkbox" name="likebtn_like_button_post_format_<?php echo $entity_name; ?>[]" value="all" <?php if (in_array('all', get_option('likebtn_like_button_post_format_' . $entity_name))): ?>checked="checked"<?php endif ?> onClick="postFormatAllChange(this, '<?php echo $entity_name; ?>')" /> <?php echo __('All', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?>&nbsp;&nbsp;&nbsp;
                                                <span id="post_format_container_<?php echo $entity_name; ?>" <?php if (in_array('all', get_option('likebtn_like_button_post_format_' . $entity_name))): ?>style="display:none"<?php endif ?>>
                                                    <?php foreach ($post_formats as $post_format): ?>
                                                        <input type="checkbox" name="likebtn_like_button_post_format_<?php echo $entity_name; ?>[]" value="<?php echo $post_format; ?>" <?php if (in_array($post_format, get_option('likebtn_like_button_post_format_' . $entity_name))): ?>checked="checked"<?php endif ?> /> <?php echo ucfirst($post_format); ?>&nbsp;&nbsp;&nbsp;
                                                    <?php endforeach ?>
                                                </span>
                                                <br/>
                                                <span class="description"><?php echo __('Select Post formats for which you want to show the Like Button.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></span>
                                            </td>
                                        </tr>

                                        <tr valign="top">
                                            <th scope="row"><label><?php echo __('Exclude on selected sections', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                            <td>
                                                <input type="checkbox" name="likebtn_like_button_exclude_sections_<?php echo $entity_name; ?>[]" value="home" <?php if (in_array('home', $excluded_sections)): ?>checked="checked"<?php endif ?> /> <?php echo __('Home'); ?>&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="likebtn_like_button_exclude_sections_<?php echo $entity_name; ?>[]" value="archive" <?php if (in_array('archive', $excluded_sections)): ?>checked="checked"<?php endif ?> /> <?php echo __('Archive'); ?>
                                                <br/>
                                                <span class="description"><?php echo __('Choose sections where you do not want to show the Like Button.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></span>
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row"><label><?php echo __('Exclude in selected categories', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                            <td>
                                                <select name='likebtn_like_button_exclude_categories_<?php echo $entity_name; ?>[]' multiple="multiple" size="4" style="height:auto !important;">
                                                    <?php
                                                    $categories = get_categories();

                                                    foreach ($categories as $category) {
                                                        $selected = (in_array($category->cat_ID, $excluded_categories)) ? 'selected="selected"' : '';
                                                        $option = '<option value="' . $category->cat_ID . '" ' . $selected . '>';
                                                        $option .= $category->cat_name;
                                                        $option .= ' (' . $category->category_count . ')';
                                                        $option .= '</option>';
                                                        echo $option;
                                                    }
                                                    ?>
                                                </select>
                                                <span class="description"><?php echo __('Select categories where you do not want to show the Like Button. Use CTRL key to select/unselect categories.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></span>
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row"><label><?php echo __('Allow post/page IDs', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                            <td>
                                                <input type="text" size="40" name="likebtn_like_button_allow_ids_<?php echo $entity_name; ?>" value="<?php echo __(get_option('likebtn_like_button_allow_ids_' . $entity_name)); ?>" /><br/>
                                                <span class="description"><?php echo __('Enter comma separated post ids where you want to show the Like Button irrespective of that post category being excluded. Suppose you have a post which belongs to more than one categories and you have excluded one of those categories. So the Like Button will not be available for that post.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></span>
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row"><label><?php echo __('Exclude post/page IDs', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                            <td>
                                                <input type="text" size="40" name="likebtn_like_button_exclude_ids_<?php echo $entity_name; ?>" value="<?php echo __(get_option('likebtn_like_button_exclude_ids_' . $entity_name)); ?>" />
                                                <span class="description"><?php echo __('Comma separated post/page IDs where you do not want to show the Like Button.', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></span>
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row"><label><?php echo __('Position', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                            <td>
                                                <input type="radio" name="likebtn_like_button_position_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BUTTON_POSITION_TOP ?>" <?php if (LIKEBTN_LIKE_BUTTON_POSITION_TOP == get_option('likebtn_like_button_position_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php echo __('Top of Content'); ?>&nbsp;&nbsp;&nbsp;
                                                <input type="radio" name="likebtn_like_button_position_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BUTTON_POSITION_BOTTOM ?>" <?php if (LIKEBTN_LIKE_BUTTON_POSITION_BOTTOM == get_option('likebtn_like_button_position_' . $entity_name) || !get_option('likebtn_like_button_position_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php echo __('Bottom of Content'); ?>&nbsp;&nbsp;&nbsp;
                                                <input type="radio" name="likebtn_like_button_position_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BUTTON_POSITION_BOTH ?>" <?php if (LIKEBTN_LIKE_BUTTON_POSITION_BOTH == get_option('likebtn_like_button_position_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php echo __('Top and Bottom', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?>

                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row"><label><?php echo __('Alignment', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                            <td>
                                                <input type="radio" name="likebtn_like_button_alignment_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BUTTON_ALIGNMENT_LEFT; ?>" <?php if (LIKEBTN_LIKE_BUTTON_ALIGNMENT_LEFT == get_option('likebtn_like_button_alignment_' . $entity_name) || !get_option('likebtn_like_button_alignment_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php echo __('Left'); ?>
                                                &nbsp;&nbsp;&nbsp;
                                                <input type="radio" name="likebtn_like_button_alignment_<?php echo $entity_name; ?>" value="<?php echo LIKEBTN_LIKE_BUTTON_ALIGNMENT_RIGHT; ?>" <?php if (LIKEBTN_LIKE_BUTTON_ALIGNMENT_RIGHT == get_option('likebtn_like_button_alignment_' . $entity_name)): ?>checked="checked"<?php endif ?> /> <?php echo __('Right'); ?>

                                            </td>
                                        </tr>
                                        <?php /*
                                          <tr valign="top">
                                          <th scope="row"></th>
                                          <td>

                                          <input class="button-secondary" type="submit" name="Reset" value="<?php _e('Reset Options', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?>" onclick="return confirmReset()" />
                                          </td>
                                          </tr>
                                         */ ?>
                                    </table>
                                    <br/>
                                    <p class="description">&nbsp;&nbsp;<?php echo __('You can find detailed description of the Like Button settings available below on <a href="http://www.likebtn.com/en/#settings" target="_blank">LikeBtn.com</a>', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></p><br/>
                                    <div class="postbox">
                                        <h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_like_button_collapse_trigger"><small>►</small> <?php echo __('Style and language', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></h3>
                                        <div class="inside hidden">
                                            <table class="form-table">
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Style', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <select name="likebtn_like_button_settings_style_<?php echo $entity_name; ?>">
                                                            <?php foreach ($likebtn_like_button_styles as $style): ?>
                                                                <option value="<?php echo $style; ?>" <?php selected($style, get_option('likebtn_like_button_settings_style_' . $entity_name)); ?> ><?php echo $style; ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                        <span class="description">style</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Language', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <select name="likebtn_like_button_settings_lang_<?php echo $entity_name; ?>">
                                                            <?php foreach ($likebtn_like_button_languages as $lang_code => $lang_title): ?>
                                                                <option value="<?php echo $lang_code; ?>" <?php selected($lang_code, get_option('likebtn_like_button_settings_lang_' . $entity_name)); ?> ><?php echo $lang_title; ?></option>
                                                            <?php endforeach ?>
                                                        </select>
                                                        <span class="description">lang</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="postbox">
                                        <h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_like_button_collapse_trigger"><small>►</small> <?php echo __('Appearance and behaviour', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></h3>
                                        <div class="inside hidden">
                                            <table class="form-table">
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Offer to share a link in social networks after "liking" (PLUS, PRO, VIP)', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_share_enabled_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_share_enabled_' . $entity_name)); ?> />
                                                        <span class="description">share_enabled</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Show "like"-label', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_show_like_label_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_show_like_label_' . $entity_name)); ?> />
                                                        <span class="description">show_like_label</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Show "dislike"-label', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_show_dislike_label_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_show_dislike_label_' . $entity_name)); ?> />
                                                        <span class="description">show_dislike_label</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Offer to share a link in social networks after "disliking"', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_dislike_share_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_dislike_share_' . $entity_name)); ?> />
                                                        <span class="description">dislike_share</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Show Dislike Button', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_dislike_enabled_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_dislike_enabled_' . $entity_name)); ?> />
                                                        <span class="description">dislike_enabled</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Votes counter is clickable', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_counter_clickable_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_counter_clickable_' . $entity_name)); ?> />
                                                        <span class="description">counter_clickable</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Language', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <select name="likebtn_like_button_settings_counter_type_<?php echo $entity_name; ?>">
                                                            <option value="number" <?php selected('number', get_option('likebtn_like_button_settings_counter_type_' . $entity_name)); ?> >number</option>
                                                            <option value="percent" <?php selected('percent', get_option('likebtn_like_button_settings_counter_type_' . $entity_name)); ?> >percent</option>
                                                        </select>
                                                        <span class="description">counter_type</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Voting is disabled, display results only', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_display_only_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_display_only_' . $entity_name)); ?> />
                                                        <span class="description">display_only</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Dislikes are substracted from likes', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_substract_dislikes_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_substract_dislikes_' . $entity_name)); ?> />
                                                        <span class="description">substract_dislikes</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Allow to unlike and undislike', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_unlike_allowed_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_unlike_allowed_' . $entity_name)); ?> />
                                                        <span class="description">unlike_allowed</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Show popop after "liking" (VIP)', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="checkbox" name="likebtn_like_button_settings_popup_enabled_<?php echo $entity_name; ?>" value="1" <?php checked('1', get_option('likebtn_like_button_settings_popup_enabled_' . $entity_name)); ?> />
                                                        <span class="description">popup_enabled</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="postbox">
                                        <h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_like_button_collapse_trigger"><small>►</small> <?php echo __('Statistics', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></h3>
                                        <div class="inside hidden">
                                            <table class="form-table">
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('AddThis <a href="https://www.addthis.com/settings/publisher" target="_blank">Profile ID</a> (PRO, VIP)', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_addthis_pubid_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_addthis_pubid_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">addthis_pubid</span><br/>
                                                        <span class="description"><?php echo __('Allows to collect sharing statistics and view it on AddThis <a href="http://www.addthis.com/analytics" target="_blank">analytics page</a>', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('AddThis <a href="http://www.addthis.com/services/list" target="_blank">service codes</a> (PRO, VIP)', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_addthis_service_codes_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_addthis_service_codes_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">addthis_service_codes</span><br/>
                                                        <span class="description"><?php echo __('Service codes separated by comma (max 8). Used to specify which buttons are displayed in share popup.<br/>Example: google_plusone_share, facebook, twitter', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="postbox">
                                        <h3 style="cursor:pointer" onclick="toggleCollapsable(this)" class="likebtn_like_button_collapse_trigger"><small>►</small> <?php echo __('Labels', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></h3>
                                        <div class="inside hidden">
                                            <table class="form-table">
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Like Button label', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_i18n_like_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_i18n_like_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">i18n_like</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Like Button tooltip', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_i18n_like_tooltip_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_i18n_like_tooltip_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">i18n_like_tooltip</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Dislike Button tooltip', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_i18n_dislike_tooltip_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_i18n_dislike_tooltip_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">i18n_dislike_tooltip</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Like Button tooltip after "liking"', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_i18n_unlike_tooltip_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_i18n_unlike_tooltip_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">i18n_unlike_tooltip</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Dislike Button tooltip after "liking"', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_i18n_undislike_tooltip_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_i18n_undislike_tooltip_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">i18n_undislike_tooltip</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Text displayed in share popup after "liking"', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_i18n_share_text_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_i18n_share_text_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">i18n_share_text</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Popup close button', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_i18n_popup_close_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_i18n_popup_close_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">i18n_popup_close</span>
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row"><label><?php echo __('Popup text when sharing is disabled', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                                    <td>
                                                        <input type="text" name="likebtn_like_button_settings_i18n_popup_text_<?php echo $entity_name; ?>" value="<?php echo get_option('likebtn_like_button_settings_i18n_popup_text_' . $entity_name); ?>" size="60"/>
                                                        <span class="description">i18n_popup_text</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php if (get_option('likebtn_like_button_show_' . $entity_name) == '1'): ?>
                                <table class="form-table">
                                    <tr valign="top">
                                        <th scope="row"><label><?php echo __('Demo', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?></label></th>
                                        <td>
                                            <?php echo _likebtn_like_button_get_markup($entity_name, 'demo') ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>

            <input class="button-primary" type="submit" name="Save" value="<?php echo __('Save', LIKEBTN_LIKE_BUTTON_I18N_DOMAIN); ?>" />
        </form>

    </div>
    </div>
    <?php
}

// admin vote results
function likebtn_like_button_admin_results() {
    likebtn_like_button_admin_header();
    echo '<br/>' . __('The feature will be available in future releases.');
    ?>
    </div>
    <?php
}

// get URL of the public folder
function _likebtn_like_button_get_public_url() {
    $siteurl = get_option('siteurl');
    return $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/public/';
}

// get plugin version
function _likebtn_like_button_get_plugin_version() {
    $plugin_data = get_plugin_data(__FILE__);
    return $plugin_data['Version'];
}

// Get supported by current theme Post Formats
function _likebtn_like_button_get_post_formats() {
    $post_formats = get_theme_support('post-formats');
    if (is_array($post_formats[0])) {
        $post_formats = $post_formats[0];
    } else {
        $post_formats = array();
    }
    // append Standard format
    array_unshift($post_formats, 'standard');

    return $post_formats;
}

// Get entity types
function _likebtn_like_button_get_entities() {
    $entities = array();
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        $entities[$post_type] = str_replace('_', ' ', ucfirst($post_type));
    }

    // append Comments
    $entities[LIKEBTN_LIKE_BUTTON_ENTITY_COMMENT] = ucfirst(LIKEBTN_LIKE_BUTTON_ENTITY_COMMENT);

    // translate entity names
    foreach ($entities as $entity_name => $entity_title) {
        $entities[$entity_name] = __($entity_title);
    }

    return $entities;
}

################
### Frontend ###
################

function _likebtn_like_button_get_markup($entity_name, $entity_id, $values = null) {

    global $likebtn_like_button_settings;

    // @todo Run sunchronization
    //$likebtn = new LikeBtn();
    //$likebtn->runSyncVotes();

    $data = 'data-identifier="' . $entity_name . '_' . $entity_id . '"';

    foreach ($likebtn_like_button_settings as $option_name => $option_info) {

        if ($values) {
            // if values passed
            if (isset($values[$option_name])) {
                $option_value = $values[$option_name];
            } else {
                $option_value = '';
            }
        } else {
            $option_value = get_option('likebtn_like_button_settings_' . $option_name . '_' . $entity_name);
        }

        // do not add option if it has default value
        if ($option_value == $likebtn_like_button_settings[$option_name]['default'] ||
            ($option_value === '' && $likebtn_like_button_settings[$option_name]['default'] == '0')
        ) {
            // option has default value
        } else {
            $option_value_prepared = $option_value;

            // do not format i18n options
            if (!strstr($option_name, 'i18n')) {
                if (is_int($option_value)) {
                    if ($option_value) {
                        $option_value_prepared = 'true';
                    } else {
                        $option_value_prepared = 'false';
                    }
                }
                if ($option_value === '1') {
                    $option_value_prepared = 'true';
                }
                if ($option_value === '0' || $option_value === '') {
                    $option_value_prepared = 'false';
                }
            }
            // Replace quotes with &quot; to avoid XSS.
            $option_value_prepared = str_replace('"', '&quot;', $option_value_prepared);

            $data .= ' data-' . $option_name . '="' . $option_value_prepared . '" ';
        }
    }

    $markup = <<<MARKUP
<!-- LikeBtn.com BEGIN -->
<span class="likebtn-wrapper" {$data}></span>
<script type="text/javascript" src="http://www.likebtn.com/js/widget.js" async="async"></script>
<!-- LikeBtn.com END -->
MARKUP;

    // alignment
    if (get_option('likebtn_like_button_alignment_' . $entity_name) == LIKEBTN_LIKE_BUTTON_ALIGNMENT_RIGHT) {
        $markup = '<div style="text-align:right">' . $markup . '</div>';
    }

    return $markup;
}

// add Like Button to the entity (except Comment)
function likebtn_like_button_the_content($content) {

    if (is_feed()) {
        return $content;
    }

    // detemine entity type
    $real_entity_name = get_post_type();

    // get entity name whose settings should be copied
    $use_entity_name = get_option('likebtn_like_button_use_settings_from_' . $entity_name);
    if ($use_entity_name) {
        $entity_name = $use_entity_name;
    } else {
        $entity_name = $real_entity_name;
    }

    if (get_option('likebtn_like_button_show_' . $entity_name) != '1') {
        return $content;
    }

    $entity_id = get_the_ID();

    // get the Posts/Pages IDs where we do not need to show like functionality
    $allow_ids = explode(",", get_option('likebtn_like_button_allow_ids_' . $entity_name));
    $exclude_ids = explode(",", get_option('likebtn_like_button_exclude_ids_' . $entity_name));
    $exclude_categories = get_option('likebtn_like_button_exclude_categories_' . $entity_name);
    $exclude_sections = get_option('likebtn_like_button_exclude_sections_' . $entity_name);

    if (empty($exclude_categories)) {
        $exclude_categories = array();
    }

    if (empty($exclude_sections)) {
        $exclude_sections = array();
    }

    // checking if section is excluded
    if ((in_array('home', $exclude_sections) && is_home()) || (in_array('archive', $exclude_sections) && is_archive())) {
        return $content;
    }

    // checking if category is excluded
    $categories = get_the_category();
    foreach ($categories as $category) {
        if (in_array($category->cat_ID, $exclude_categories) && !in_array($entity_id, $allow_ids)) {
            return $content;
        }
    }

    // check if post is excluded
    if (in_array($entity_id, $exclude_ids)) {
        return $content;
    }

    // check Post view mode

    switch (get_option('likebtn_like_button_post_view_mode_' . $entity_name)) {
        case LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_FULL:
            if (!is_single()) {
                return $content;
            }
            break;
        case LIKEBTN_LIKE_BUTTON_POST_VIEW_MODE_EXCERPT:
            if (is_single()) {
                return $content;
            }
            break;
    }



    // check Post format
    $post_format = get_post_format($entity_id);
    if (!$post_format) {
        $post_format = 'standard';
    }

    if (get_option('likebtn_like_button_post_format_' . $entity_name) != 'all' &&
        !in_array($post_format, get_option('likebtn_like_button_post_format_' . $entity_name))
    ) {
        return $content;
    }

    $html = _likebtn_like_button_get_markup($real_entity_name, $entity_id);

    $position = get_option('likebtn_like_button_position_' . $entity_name);

    if ($position == LIKEBTN_LIKE_BUTTON_POSITION_TOP) {
        $content = $html . $content;
    } elseif ($position == LIKEBTN_LIKE_BUTTON_POSITION_BOTTOM) {
        $content = $content . $html;
    } else {
        $content = $html . $content . $html;
    }

    return $content;
}

add_filter('the_content', 'likebtn_like_button_the_content');

// add Like Button to the Comment
function likebtn_like_button_comment_text($content) {

    global $comment;

    if (is_feed()) {
        return $content;
    }

    // detemine entity type
    $real_entity_name = LIKEBTN_LIKE_BUTTON_ENTITY_COMMENT;

    // get entity name whose settings should be copied
    $use_entity_name = get_option('likebtn_like_button_use_settings_from_' . $entity_name);
    if ($use_entity_name) {
        $entity_name = $use_entity_name;
    } else {
        $entity_name = $real_entity_name;
    }

    if (get_option('likebtn_like_button_show_' . $entity_name) != '1') {
        return $content;
    }

    $comment_id = $comment->comment_ID;
    //$comment = get_comment($comment_id, ARRAY_A);
    $post_id = $comment->comment_post_ID;

    // get the Posts/Pages IDs where we do not need to show like functionality
    $allow_ids = explode(",", get_option('likebtn_like_button_allow_ids_' . $entity_name));
    $exclude_ids = explode(",", get_option('likebtn_like_button_exclude_ids_' . $entity_name));
    $exclude_categories = get_option('likebtn_like_button_exclude_categories_' . $entity_name);
    $exclude_sections = get_option('likebtn_like_button_exclude_sections_' . $entity_name);

    if (empty($exclude_categories)) {
        $exclude_categories = array();
    }

    if (empty($exclude_sections)) {
        $exclude_sections = array();
    }

    // checking if section is excluded
    if ((in_array('home', $exclude_sections) && is_home()) || (in_array('archive', $exclude_sections) && is_archive())) {
        return $content;
    }

    // checking if category is excluded
    $categories = get_the_category();
    foreach ($categories as $category) {
        if (in_array($category->cat_ID, $exclude_categories) && !in_array($post_id, $allow_ids)) {
            return $content;
        }
    }

    // check if post is excluded
    if (in_array($post_id, $exclude_ids)) {
        return $content;
    }

    // check Post view mode - no need

    // check Post format
    $post_format = get_post_format($post_id);
    if (!$post_format) {
        $post_format = 'standard';
    }

    if (get_option('likebtn_like_button_post_format_' . $entity_name) != 'all' &&
        !in_array($post_format, get_option('likebtn_like_button_post_format_' . $entity_name))
    ) {
        return $content;
    }

    $html = _likebtn_like_button_get_markup($real_entity_name, $comment_id);

    $position = get_option('likebtn_like_button_position_' . $entity_name);

    if ($position == LIKEBTN_LIKE_BUTTON_POSITION_TOP) {
        $content = $html . $content;
    } elseif ($position == LIKEBTN_LIKE_BUTTON_POSITION_BOTTOM) {
        $content = $content . $html;
    } else {
        $content = $html . $content . $html;
    }

    return $content;
}

add_filter('comment_text', 'likebtn_like_button_comment_text');

// show the Like Button in Post/Page
// if Like Button is enabled in admin for Post/Page do not show button twice
function likebtn_post($post_id = NULL) {
    global $post;
    if (empty($post_id)) {
        $post_id = $post->ID;
    }

    // detemine entity type
    if (is_page()) {
        $entity_name = LIKEBTN_LIKE_BUTTON_ENTITY_PAGE;
    } else {
        $entity_name = LIKEBTN_LIKE_BUTTON_ENTITY_POST;
    }

    // check if the Like Button should be displayed
    // if Like Button enabled for Post or Page in Admin do not show Like Button twice
    if ($entity_name == LIKEBTN_LIKE_BUTTON_ENTITY_POST && get_option('likebtn_like_button_show_' . LIKEBTN_LIKE_BUTTON_ENTITY_POST) == '1') {
        return;
    }
    if ($entity_name == LIKEBTN_LIKE_BUTTON_ENTITY_PAGE && get_option('likebtn_like_button_show_' . LIKEBTN_LIKE_BUTTON_ENTITY_PAGE) == '1') {
        return;
    }

    // 'post' here is for the sake of backward compatibility
    $html = _likebtn_like_button_get_markup('post', $post_id);

    echo $html;
}

// get or echo the Like Button in comment
function likebtn_comment($comment_id = NULL) {
    //global $comment;
    if (empty($comment_id)) {
        $comment_id = get_comment_ID();
    }

    // if Like Button enabled for Comment in Admin do not show Like Button twice
    if (get_option('likebtn_like_button_show_' . LIKEBTN_LIKE_BUTTON_ENTITY_COMMENT) == '1') {
        return;
    }

    $html = _likebtn_like_button_get_markup(LIKEBTN_LIKE_BUTTON_ENTITY_COMMENT, $comment_id);

    echo $html;
}