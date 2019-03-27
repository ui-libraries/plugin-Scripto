<?php
/**
 * Scripto plugin
 *
 * @copyright Copyright 2007-2013 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The Scripto plugin.
 *
 * @package Omeka\Plugins\Scripto
 */
class ScriptoPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * The name of the Scripto element set.
     */
    const ELEMENT_SET_NAME = 'Scripto';

    /**
     * @var array This plugin's hooks.
     */
    protected $_hooks = array(
        'initialize',
        'install',
        'upgrade',
        'uninstall',
        'uninstall_message',
        'define_routes',
        'config_form',
        'config',
        'admin_head',
        'admin_items_browse_simple_each',
        'admin_items_browse_detailed_each',
        'admin_items_browse',
        'admin_items_show',
        'public_items_show',
    );

    /**
     * @var array This plugin's filter.
     */
    protected $_filters = array(
        'admin_navigation_main',
        'public_navigation_main',
    );

    /**
     * @var array This plugin's options.
     */
    protected $_options = array(
        'scripto_mediawiki_api_url' => '',
        'scripto_mediawiki_cookie_prefix' => '',
        'scripto_source_element' => 'Scripto:Transcription',
        'scripto_import_type' => null,
        'scripto_allow_register' => false,
        'scripto_image_viewer' => null,
        'scripto_viewer_class' => '',
        'scripto_use_google_docs_viewer' => '',
        'scripto_iframe_class' => '',
        'scripto_file_source' => 'original',
        // This path is not really an option, but it allows to save it one time,
        // because paths aren't available after a file is stored.
        'scripto_file_source_path' => 'original',
        'scripto_files_order' => '',
        'scripto_home_page_text' => '<p>Scripto</p>',
    );

    /**
     * @var MIME types compatible with OpenLayers.
     */
    public static $fileIdentifiersOpenLayers = array(
        'mimeTypes' => array(
            // gif
            'image/gif', 'image/x-xbitmap', 'image/gi_',
            // jpg
            'image/jpeg', 'image/jpg', 'image/jpe_', 'image/pjpeg',
            'image/vnd.swiftview-jpeg',
            // png
            'image/png', 'application/png', 'application/x-png',
            // bmp
            'image/bmp', 'image/x-bmp', 'image/x-bitmap',
            'image/x-xbitmap', 'image/x-win-bitmap',
            'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp',
            'application/bmp', 'application/x-bmp',
            'application/x-win-bitmap',
        ),
        'fileExtensions' => array(
            'gif', 'jpeg', 'jpg', 'jpe', 'png', 'bmp',
        ),
    );

    /**
     * @var MIME types compatible with Google Docs viewer.
     */
    public static $fileIdentifiersGoogleDocs = array(
        'mimeTypes' => array(
            // pdf
            'application/pdf', 'application/x-pdf',
            'application/acrobat', 'applications/vnd.pdf', 'text/pdf',
            'text/x-pdf',
            // docx
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            // doc
            'application/msword', 'application/doc', 'appl/text',
            'application/vnd.msword', 'application/vnd.ms-word',
            'application/winword', 'application/word', 'application/vnd.ms-office',
            'application/x-msw6', 'application/x-msword',
            // ppt
            'application/vnd.ms-powerpoint', 'application/mspowerpoint',
            'application/ms-powerpoint', 'application/mspowerpnt',
            'application/vnd-mspowerpoint', 'application/powerpoint',
            'application/x-powerpoint', 'application/x-m',
            // pptx
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            // xls
            'application/vnd.ms-excel', 'application/msexcel',
            'application/x-msexcel', 'application/x-ms-excel',
            'application/vnd.ms-excel', 'application/x-excel',
            'application/x-dos_ms_excel', 'application/xls',
            // xlsx
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            // tiff
            'image/tiff',
            // ps, ai
            'application/postscript', 'application/ps',
            'application/x-postscript', 'application/x-ps',
            'text/postscript', 'application/x-postscript-not-eps',
            // eps
            'application/eps', 'application/x-eps', 'image/eps',
            'image/x-eps',
            // psd
            'image/vnd.adobe.photoshop', 'image/photoshop',
            'image/x-photoshop', 'image/psd', 'application/photoshop',
            'application/psd', 'zz-application/zz-winassoc-psd',
            // dxf
            'application/dxf', 'application/x-autocad',
            'application/x-dxf', 'drawing/x-dxf', 'image/vnd.dxf',
            'image/x-autocad', 'image/x-dxf',
            'zz-application/zz-winassoc-dxf',
            // xvg
            'image/svg+xml',
            // xps
            'application/vnd.ms-xpsdocument',
        ),
        'fileExtensions' => array(
            'pdf',
            'docx',
            'doc', 'dot',
            'ppt', 'pps', 'pot',
            'pptx',
            'xls', 'xlm', 'xla', 'xlc', 'xlt', 'xlw',
            'xlsx',
            'tiff', 'tif',
            'ai', 'eps', 'ps',
            'psd',
            'dxf',
            'xvg',
            'xps',
        ),
    );

    /**
     * Initialize Scripto.
     */
    public function hookInitialize()
    {
        // Add translation.
        add_translation_source(dirname(__FILE__) . '/languages');
    }

    /**
     * Install Scripto.
     */
    public function hookInstall()
    {
        // Don't install if an element set by the name "Scripto" already exists.
        if ($this->_db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)) {
            throw new Omeka_Plugin_Installer_Exception(
                __('An element set by the name "%s" already exists. You must delete '
                 . 'that element set to install this plugin.', self::ELEMENT_SET_NAME)
            );
        }

        $this->_setScriptoSet();

        $this->_installOptions();
    }

    /**
     * Upgrades the plugin.
     */
    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];

        if (version_compare($oldVersion, '2.2', '<')) {
            delete_option('scripto_viewer_css');
            delete_option('scripto_iframe_properties');

            $this->_setScriptoSet();
        }

        $option = get_option('scripto_file_source');
        if (empty($option)) {
            set_option('scripto_file_source', $this->_options['scripto_file_source']);
            set_option('scripto_file_source_path', $this->_options['scripto_file_source_path']);
        }
    }

    private function _setScriptoSet()
    {
        // Create the set if needed.
        $elementSet = get_record('ElementSet', array('name' => self::ELEMENT_SET_NAME));
        if (!$elementSet) {
            $elementSetMetadata = array(
                'name' => self::ELEMENT_SET_NAME,
                'description' => 'Manages transcriptions of items and files',
                'record_type' => NULL,
            );
            insert_element_set($elementSetMetadata, array());
            $elementSet = get_record('ElementSet', array('name' => self::ELEMENT_SET_NAME));
        }

        // Fill the set if needed.
        $elements = array(
            array('name' => 'Transcription',
                  'description' => 'A written representation of a document or a page.'),
            array('name' => 'Status',
                  'description' => 'The current transcription status of a document or a page.'),
            array('name' => 'Percent Needs Review',
                  'description' => 'The percentage of pages with Needs Review status.'),
            array('name' => 'Percent Completed',
                  'description' => 'The percentage of pages with Completed status.'),
            array('name' => 'Weight',
                  'description' => 'A 6-digit number used to sort items quickly.'),
        );

        // Remove existing elements.
        $existingElements = $elementSet->getElements();
        foreach ($existingElements as $existingElement) {
            foreach ($elements as $key => $newElement) {
                if ($newElement['name'] == $existingElement->name) {
                    unset($elements[$key]);
                }
            }
        }

        // Save new elements if any.
        $elementSet->addElements($elements);
        $elementSet->save();
    }

    /**
     * Uninstall Scripto.
     */
    public function hookUninstall()
    {
        // Delete the Scripto element set.
        $this->_db->getTable('ElementSet')->findByName(self::ELEMENT_SET_NAME)->delete();

        // Delete options that are specific to Scripto.
        $this->_uninstallOptions();
    }

    /**
     * Appends a warning message to the uninstall confirmation page.
     */
    public function hookUninstallMessage()
    {
        echo '<p>' . __(
            '%1$sWarning%2$s: This will permanently delete the "%3$s" element set and '
          . 'all transcriptions imported from MediaWiki. You may deactivate this '
          . 'plugin if you do not want to lose data. Uninstalling this plugin will '
          . 'not affect your MediaWiki database in any way.',
            '<strong>', '</strong>', self::ELEMENT_SET_NAME) . '</p>';
    }

    /**
     * Define routes.
     *
     * @param Zend_Controller_Router_Rewrite $router
     */
    public function hookDefineRoutes($args)
    {
        $args['router']->addConfig(new Zend_Config_Ini(dirname(__FILE__) . '/routes.ini', 'routes'));
    }

    /**
     * Shows plugin configuration page.
     */
    public function hookConfigForm($args)
    {
        $view = get_view();

        $this->_validateMediaWikiApiUrl();

        // Set form defaults.
        list($elementSetName, $elementName) = explode(':', get_option('scripto_source_element'));
        $element = get_db()->getTable('Element')->findByElementSetNameAndElementName($elementSetName, $elementName);
        if (empty($element)) {
            $elementSetName = self::ELEMENT_SET_NAME;
            $elementName = 'Transcription';
            $element = get_db()->getTable('Element')->findByElementSetNameAndElementName($elementSetName, $elementName);
        }
        $imageViewer = get_option('scripto_image_viewer');
        if (!in_array($imageViewer, array('openlayers'))) {
            $imageViewer = 'default';
        }
        $useGoogleDocsViewer = get_option('scripto_use_google_docs_viewer');
        if (is_null($useGoogleDocsViewer)) {
            $useGoogleDocsViewer = 0;
        }
        $importType = get_option('scripto_import_type');
        if (is_null($importType)) {
            $importType = 'html';
        }

        // To be removed when there will be an upgrade process.
        $option = get_option('scripto_file_source');
        if (empty($option)) {
            set_option('scripto_file_source', $this->_options['scripto_file_source']);
            set_option('scripto_file_source_path', $this->_options['scripto_file_source_path']);
        }

        echo $view->partial(
            'plugins/scripto-config-form.php',
            array(
                'element_id' => $element->id,
                'image_viewer' => $imageViewer,
                'use_google_docs_viewer' => $useGoogleDocsViewer,
                'import_type' => $importType,
        ));
    }

    /**
     * Handle a submitted config form.
     *
     * @param array Options set in the config form.
     */
    public function hookConfig($args)
    {
        $post = $args['post'];

        $this->_validateMediaWikiApiUrl($post['scripto_mediawiki_api_url']);

        // Validate the source element.
        $element = get_record_by_id('Element', (integer) $post['scripto_source_element']);
        $post['scripto_source_element'] = $element->set_name . ':' . $element->name;

        // Get source path.
        $post['scripto_file_source_path'] = $this->_getFilePath(get_option('scripto_file_source_path'));

        foreach ($this->_options as $optionKey => $optionValue) {
            if (isset($post[$optionKey])) {
                set_option($optionKey, $post[$optionKey]);
            }
        }
    }

    /**
     * Helper to validate the MediaWiki API URL.
     *
     * @param string $url If not set, check the current option.
     */
    protected function _validateMediaWikiApiUrl($url = null)
    {
        $url = $url ?: get_option('scripto_mediawiki_api_url');
        if (!Scripto::isValidApiUrl($url)) {
            $flash = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
            $flash->addMessage(__('Invalid MediaWiki API URL'), 'error');
        }
    }

    public function hookAdminHead($args)
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ($controller == 'items' && $action == 'browse') {
            queue_css_file('scripto');
            queue_js_file('scripto');
        }
    }

    public function hookAdminItemsBrowseSimpleEach($args)
    {
        $view = $args['view'];
        $item = $args['item'];

        $status = $item->getElementTexts('Scripto', 'Status');
        if (empty($status)) {
            $statusClass = 'undefined';
            $statusText = __('Undefined');
        }
        else {
            switch ($status[0]->text) {
                case 'Not to transcribe':
                    $statusClass = 'not-to-transcribe';
                    $statusText = __('Not to transcribe');
                    break;
                case 'To transcribe':
                    $statusClass = 'to-transcribe';
                    $statusText = __('To transcribe');
                    break;
                case 'Undefined':
                default:
                    $statusClass = 'undefined';
                    $statusText = __('Undefined');
            }
        }
        $html = '<a href="' . ADMIN_BASE_URL . '" id="scripto-%d" class="scripto toggle-status status %s">%s</a>';
        $args = array();
        $args[] = $item->id;
        $args[] = $statusClass;
        $args[] = $statusText;

        echo '<p>' . __('Scripto: %s', vsprintf($html, $args)) . '</p>';
    }

    public function hookAdminItemsBrowseDetailedEach($args)
    {
        $view = $args['view'];
        $item = $args['item'];

        $status = $item->getElementTexts('Scripto', 'Status');
        if (empty($status) || $status[0]->text != 'To transcribe') {
            return;
        }
        $html = '<a href="' . ADMIN_BASE_URL . '" id="scripto-reset-%d" class="scripto fill-pages">%s</a>' ;
        $args = array();
        $args[] = $item->id;
        $args[] = __('Fill pages');

        echo '<p>' . __('%sScripto:%s %s', '<strong>', '</strong>', vsprintf($html, $args)) . '</p>';
    }

    public function hookAdminItemsBrowse($args)
    { ?>
<script type="text/javascript">
    Omeka.messages = jQuery.extend(Omeka.messages,
        {'scripto':{
            'notToTranscribe':<?php echo json_encode(__('Not to transcribe')); ?>,
            'toTranscribe':<?php echo json_encode(__('To transcribe')); ?>,
            'confirmation':<?php echo json_encode(__('Are your sure to fill all pages of this item from the field "%s" into the field Scripto:Transcription?', get_option('scripto_source_element'))); ?>,
            'error':<?php echo json_encode(__('Failure during process.')); ?>
        }}
    );
</script>
    <?php
    }

    /**
     * Append the transcribe link to the admin items show page.
     */
    public function hookAdminItemsShow($args)
    {
        $this->_appendToItemsShow($args);
    }

    /**
     * Append the transcribe link to the public items show page.
     */
    public function hookPublicItemsShow($args)
    {
        $this->_appendToItemsShow($args);
    }

    /**
     * Add Scripto to the admin navigation.
     *
     * @param array $nav
     * @return array
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array('label' => __('Scripto'), 'uri' => url('scripto'));
        return $nav;
    }

    /**
     * Add Scripto to the public navigation.
     *
     * @param array $nav
     * @return array
     */
    public function filterPublicNavigationMain($nav)
    {
        $nav[] = array('label' => __('Scripto'), 'uri' => url('scripto'));
        return $nav;
    }

    /**
     * Append the transcribe link to the items show page.
     */
    protected function _appendToItemsShow($args)
    {
        $view = isset($args['view']) ? $args['view'] : get_view();
        $item = isset($args['item']) ? $args['item'] : get_current_record('item');

        $scripto = self::getScripto();
        // Do not show page links if document is not valid.
        if (!$scripto->documentExists($item->id)) {
            return;
        }
        $doc = $scripto->getDocument($item->id);

        echo $view->partial('common/scripto-append-to-item-show.php', array(
            'item' => $item,
            'doc' => $doc,
        ));
    }

    /**
     * add_file_display_callback() callback for OpenLayers.
     *
     * @see Scripto_IndexController::init()
     * @param File $file
     */
    public static function openLayers($file)
    {
        // Check size via local path to avoid to use the server.
        $imagePath = realpath(FILES_DIR . DIRECTORY_SEPARATOR . get_option('scripto_file_source_path') . DIRECTORY_SEPARATOR . $file->filename);
        $imageSize = ScriptoPlugin::getImageSize($imagePath);
        // Image to send.
        $imageUrl = $file->getWebPath(get_option('scripto_file_source'));
?>
<div id="map" class="map"></div>
<script type="text/javascript">
    var target = 'map';
    var imgWidth = <?php echo $imageSize['width']; ?>;
    var imgHeight = <?php echo $imageSize['height']; ?>;
    var url = <?php echo json_encode($imageUrl); ?>;

    // The zoom is set to extent after map initialization.
    var zoom = 2;
    var extent = [0, 0, imgWidth, imgHeight];

    var source = new ol.source.ImageStatic({
        url: url,
        projection: projection,
        imageExtent: extent
    });

    // Map views always need a projection.  Here we just want to map image
    // coordinates directly to map coordinates, so we create a projection that uses
    // the image extent in pixels.
    var projection = new ol.proj.Projection({
        code: 'pixel',
        units: 'pixels',
        extent: extent
    });

    var map = new ol.Map({
        layers: [
            new ol.layer.Image({
                source: source
            })
        ],
        logo: false,
        controls: ol.control.defaults({attribution: false}).extend([
            new ol.control.FullScreen()
        ]),
        interactions: ol.interaction.defaults().extend([
            new ol.interaction.DragRotateAndZoom()
        ]),
        target: target,
        view: new ol.View({
                projection: projection,
                center: ol.extent.getCenter(extent),
                zoom: zoom
            })
    });

    // Initialize zoom to extent.
    map.getView().fit(extent, map.getSize());
 </script>
<?php
    }

    /**
     * add_file_display_callback() callback for Google Docs.
     *
     * @see Scripto_IndexController::init()
     * @param File $file
     */
    public static function googleDocs($file)
    {
        $uri = Zend_Uri::factory('https://docs.google.com/viewer');
        $uri->setQuery(array(
            'url' => $file->getWebPath(get_option('scripto_file_source')),
            'embedded' => 'true',
        ));
        echo vsprintf('<iframe src="%s" id="scripto-iframe" class="%s"></iframe>',
            array($uri->getUri(), get_option('scripto_iframe_class')));
    }

    /**
     * Convenience method to get the Scripto object.
     *
     * @param string $apiUrl
     */
    public static function getScripto($apiUrl = null)
    {
        if (null === $apiUrl) {
            $apiUrl = get_option('scripto_mediawiki_api_url');
        }

        $cookiePrefix = get_option('scripto_mediawiki_cookie_prefix') ?: null;

        return new Scripto(new ScriptoAdapterOmeka, array(
            'api_url' => $apiUrl,
            'cookie_prefix' => $cookiePrefix,
        ));
    }

    /**
     * Helper to determine if an item may be transcribed or not.
     *
     * An item may be transcribed if its status is not 'Not to transcribe' and
     * if it get one file or more. This is determined via the scripto check.
     */
    public static function isToTranscribe($item = null)
    {
        if ($item == null) {
            $item = get_current_record('item');
        }
        $scripto = self::getScripto();
        try {
            $result = $scripto->documentExists($item->id);
        } catch (Exception $e) {
            return false;
        }
        return $result;
    }

    /**
     * Return a truncated string with left and right padding.
     *
     * Primarily used for truncating long document page names that would
     * otherwise break tables.
     *
     * @param string $str The string to truncate.
     * @param int $length The trancate length.
     * @param string $default The string to return if the string is empty.
     * @return string
     */
    public static function truncate($str, $length, $default = '')
    {
        $str = trim($str);
        if (empty($str)) {
            return $default;
        }
        if (strlen($str) <= $length) {
            return $str;
        }
        $padding = floor($length / 2);
        return preg_replace('/^(.{' . $padding . '}).*(.{' . $padding . '})$/', '$1... $2', $str);
    }

    /**
     * Get dimensions of the provided image.
     *
     * @param string $filename URI to file.
     * @param int $width Width constraint.
     * @return array
     */
    public static function getImageSize($filename, $width = null)
    {
        $size = getimagesize($filename);
        if (!$size) {
            return false;
        }
        if (is_int($width)) {
            $height = round(($width * $size[1]) / $size[0]);
        } else {
            $width = $size[0];
            $height = $size[1];
        }
        return array('width' => $width, 'height' => $height);
    }

    /**
     * Get path of a file type.
     *
     * Paths aren't available after a file is stored, but it can be guessed.
     *
     * @param string $type
     * @return string Partial path
     */
    private function _getFilePath($type)
    {
        $file = new File;
        $filename = 'filename.jpg';
        $file->filename = $filename;
        $path = $file->getStoragePath($type);
        return trim(substr($path, 0, strlen($path) - strlen($filename)), '/');
    }
}
