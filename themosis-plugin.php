<?php
/*
 * Themosis - A framework for WordPress developers.
 * Based on php 5.4 features and above.
 *
 * @author  Julien Lambé <julien@themosis.com>
 * @link    http://www.themosis.com/
 */

/*----------------------------------------------------*/
// The directory separator.
/*----------------------------------------------------*/
defined('DS') ? DS : define('DS', DIRECTORY_SEPARATOR);

/*----------------------------------------------------*/
// Asset directory URL.
/*----------------------------------------------------*/
defined('THEMOSIS_ASSETS') ? THEMOSIS_ASSETS : define('THEMOSIS_ASSETS', get_template_directory_uri().'/app/assets');

/*----------------------------------------------------*/
// Pugin Textdomain.
/*----------------------------------------------------*/
defined('THEMOSIS_PLUGIN_TEXTDOMAIN') ? THEMOSIS_PLUGIN_TEXTDOMAIN : define('THEMOSIS_PLUGIN_TEXTDOMAIN', 'themosis-plugin');

/*----------------------------------------------------*/
// Themosis Plugin class.
// Check if the framework is loaded. If not, warn the user
// to activate it before continuing using the theme.
/*----------------------------------------------------*/
if (!class_exists('THFWK_ThemosisPlugin'))
{
    class THFWK_ThemosisPlugin
    {
        /**
         * Theme class instance.
         *
         * @var \THFWK_ThemosisPlugin
         */
        protected static $instance = null;
        
        /**
         * Switch that tell if core and datas plugins are loaded.
         *
         * @var bool
         */
        protected $pluginsAreLoaded = false;
        protected function __construct()
        {
            // Default path to Composer autoload file.
            $autoload = __DIR__.DS.'vendor'.DS.'autoload.php';
            // Check for autoload file in dev mode (vendor loaded into the theme)
            if (file_exists($autoload))
            {
                require($autoload);
            }
            // Check if framework is loaded.
            add_action('after_setup_theme', [$this, 'check']);
        }
        
        /**
         * Init the class.
         *
         * @return \THFWK_ThemosisPlugin
         */
        public static function getInstance()
        {
            if (is_null(static::$instance))
            {
                static::$instance = new static();  
            }
            return static::$instance;
        }
        
        /**
         * Trigger by the action hook 'after_switch_theme'.
         * Check if the framework and dependencies are loaded.
         *
         * @return void
         */
        public function check()
        {
            // Check if core application class is loaded...
            if (!class_exists('Themosis\Core\Application'))
            {
                // Message for the back-end
                add_action('admin_notices', [$this, 'displayMessage']);
                // Message for the front-end
                if (!is_admin())
                {
                    wp_die(__("The <strong>Themosis theme</strong> can't work properly. Please make sure the Themosis framework plugin is installed. Check also your <strong>composer.json</strong> autoloading configuration.", THEMOSIS_THEME_TEXTDOMAIN));
                }
                return;
            }
        }
        
        /**
         * Display a notice to the user if framework is not loaded.
         *
         * @return void
         */
        public function displayMessage()
        {
            ?>
                <div id="message" class="error">
                    <p><?php _e("You first need to activate the <b>Themosis framework</b> in order to use this theme.", THEMOSIS_THEME_TEXTDOMAIN); ?></p>
                </div>
            <?php
        }
    }
}

/*----------------------------------------------------*/
// Instantiate the plugin class.
/*----------------------------------------------------*/
THFWK_ThemosisPlugin::getInstance();

/*----------------------------------------------------*/
// Set theme's paths.
/*----------------------------------------------------*/
add_filter('themosis_framework_paths', 'themosisPlugin_setApplicationPaths');
add_filter('themosis_application_paths', 'themosisPlugin_setApplicationPaths');

if (!function_exists('themosisPlugin_setApplicationPaths'))
{
    function themosisPlugin_setApplicationPaths($paths)
    {
        // Plugin base path.
        $paths['base'] = __DIR__.DS;

        // Application path.
        $paths['plugin'] = __DIR__.DS.'resources'.DS;
        
        // Application admin directory.
        $paths['admin'] = __DIR__.DS.'resources'.DS.'admin'.DS;

        // Application storage directory.
        $paths['storage'] = __DIR__.DS.'storage'.DS;
        return $paths;
    }
}

/*----------------------------------------------------*/
// Start the plugin.
/*----------------------------------------------------*/
require_once('bootstrap'.DS.'start.php');
