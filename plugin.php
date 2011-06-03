<?php
// Debugging.
function d($var, $dump = false, $exit = true)
{
    echo '<pre>';
    $dump ? var_dump($var) : print_r($var);
    echo '</pre>';
    if ($exit) exit;
}

add_plugin_hook('define_routes', 'ScriptoPlugin::defineRoutes');
add_plugin_hook('config_form', 'ScriptoPlugin::configForm');
add_plugin_hook('config', 'ScriptoPlugin::config');
add_plugin_hook('public_append_to_items_show', 'ScriptoPlugin::appendToItemsShow');
add_plugin_hook('admin_append_to_items_show_primary', 'ScriptoPlugin::appendToItemsShow');

add_filter('admin_navigation_main', 'ScriptoPlugin::adminNavigationMain');

/**
 * Contains methods specific to the Scripto plugin.
 */
class ScriptoPlugin
{
    /**
     * Define routes.
     * 
     * @param Zend_Controller_Router_Rewrite $router
     */
    public static function defineRoutes($router)
    {
        $router->addConfig(new Zend_Config_Ini(dirname(__FILE__) . '/routes.ini', 'routes'));
    }
    
    /**
     * Render the config form.
     */
    public static function configForm()
    {
        $db = get_db();
        
        // Get all the item types.
        $itemTypes = $db->getTable('ItemType')->findPairsForSelectForm();
        $itemTypes = array(0 => 'Select Below...') + $itemTypes;
        
        // Get all the item type's elements, if any.
        $itemTypeElements = array();
        if ($itemTypeId = get_option('scripto_document_item_type_id')) {
            $elements = $db->getTable('ItemType')->find($itemTypeId)->Elements;
            foreach ($elements as $element) {
                $itemTypeElements[$element->id] = $element->name;
            }
        }
        $itemTypeElements = array(0 => 'Select Below...') + $itemTypeElements;
       
        $url = uri(array('module'     => 'scripto', 
                         'controller' => 'index', 
                         'action'     => 'item-type-elements',  
                         'id'         => ''));
        
        include 'config_form.php';
    }
    
    /**
     * Handle a submitted config form.
     */
    public static function config()
    {
        // Validate the MediaWiki API URL.
        if (!Scripto::isValidApiUrl($_POST['scripto_mediawiki_api_url'])) {
            throw new Omeka_Validator_Exception('Invalid MediaWiki API URL');
        }
        
        set_option('scripto_mediawiki_api_url', $_POST['scripto_mediawiki_api_url']);
        set_option('scripto_mediawiki_db_name', $_POST['scripto_mediawiki_db_name']);
        set_option('scripto_document_item_type_id', $_POST['scripto_document_item_type_id']);
        set_option('scripto_transcription_element_id', $_POST['scripto_transcription_element_id']);
    }
    
    /**
     * Add Scripto to the admin navigation.
     * 
     * @param array $nav
     * @return array
     */
    public static function adminNavigationMain($nav)
    {
        $nav['Scripto'] = uri('scripto');
        return $nav;
    }
    
    /**
     * 
     */
    public static function appendToItemsShow()
    {
        // check if this item is a valid Scripto item type
        $item = get_current_item();
        if ($item->item_type_id !== (int) get_option('scripto_document_item_type_id')) {
            return;
        }
        $url = uri(array('action'  => 'transcribe',  
                         'item-id' => $item->id), 'scripto_action_item_file');
        

?>
<p><a href="<?php echo $url; ?>">Transcribe this item.</a></p>
<?php
    }
    
    public static function getScripto($apiUrl = null, $dbName = null)
    {
        if (null === $apiUrl) {
            $apiUrl = get_option('scripto_mediawiki_api_url');
        }
        if (null === $dbName) {
            get_option('scripto_mediawiki_db_name');
        }
        
        return new Scripto(new ScriptoAdapterOmeka, 
                           array('api_url' => $apiUrl, 'db_name' => $dbName));
    }
}
