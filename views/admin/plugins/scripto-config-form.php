<p><?php echo __(
    'This plugin requires you to download and install %1$sMediaWiki%2$s, a popular free '
  . 'web-based wiki software application that Scripto uses to manage user and transcription '
  . 'data. Once you have successfully installed MediaWiki, you can complete the following '
  . 'form and install the plugin.',
    '<a href="http://www.mediawiki.org/wiki/MediaWiki">', '</a>'
); ?></p>

<p><?php echo __(
    'Scripto will assume files belonging to an item are in logical order, first to last page.'
); ?></p>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_mediawiki_api_url"><?php echo __('MediaWiki API URL'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'URL to your %1$sMediaWiki installation API%2$s.',
            '<a href="http://www.mediawiki.org/wiki/API:Quick_start_guide#What_you_need_to_access_the_API">', '</a>'
        ); ?></p>
        <?php echo $this->formText(
            'scripto_mediawiki_api_url',
            get_option('scripto_mediawiki_api_url')
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_source_element"><?php echo __('Element source used to initialize transcription'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'A document page is automatically initialized with this element, if available. '
            . 'This is helpful when Scripto is used to correct OCR for typescript pages. '
            . 'This can be useful to keep track of original source too.'
        ); ?></p>
        <?php
            echo $this->formSelect('scripto_source_element',
                $element_id,
                array(),
                get_table_options('Element', null, array(
                    'record_types' => array('File', 'All'),
                    'sort' => 'alphaBySet')
            ));
        ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_image_viewer"><?php echo __('Image viewer'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'Select an image viewer to use when transcribing image files. %1$sOpenLayers%2$s '
          . 'and %3$sZoom.it%4$s can display JPEG, PNG, GIF, and BMP formats. Zoom.it can '
          . 'also display TIFF and ICO formats. By using Zoom.it you awknowledge that '
          . 'you have read and agreed to the %5$sMicrosoft Zoom.it Terms of Service%6$s',
            '<a href="http://openlayers.org/">', '</a>',
            '<a href="http://zoom.it/">', '</a>',
            '<a href="http://zoom.it/pages/terms/">', '</a>'
        ); ?></p>
        <?php echo $this->formRadio(
            'scripto_image_viewer',
            $this->image_viewer,
            null,
            array('default' => __('Omeka default'),
                  'openlayers' => __('OpenLayers'),
                  'zoomit' => __('Zoom.it')),
            null
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_viewer_css"><?php echo __('CSS style to add to the image viewer'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'This style will be added as value of attribute to the image viewer '
            . 'in order to get a default size and other properties.'
        ); ?></p>
        <?php echo $this->formText(
            'scripto_viewer_css',
            $this->viewer_css
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_use_google_docs_viewer"><?php echo __('Use Google Docs Viewer?'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'Use Google Docs Viewer when transcribing document files? Document files '
          . 'include PDF, DOC, PPT, XLS, TIFF, PS, and PSD formats. By using this service '
          . 'you acknowledge that you have read and agreed to the %1$sGoogle Docs Viewer Terms of Service%2$s.',
          '<a href="http://docs.google.com/viewer/TOS?hl=en">', '</a>'
        ); ?></p>
        <?php echo $this->formCheckbox(
            'scripto_use_google_docs_viewer',
            null,
            array('checked' => (bool) $this->use_google_docs_viewer)
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_iframe_properties"><?php echo __('Properties to add to the document viewer'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'These properties will be added as attributes to the document viewer '
            . 'in order to get a default size and other properties.'
        ); ?></p>
        <?php echo $this->formText(
            'scripto_iframe_properties',
            $this->iframe_properties
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_import_type"><?php echo __('Import type'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'Import transcriptions as HTML or plain text? Importing will copy document '
          . 'and page transcriptions from MediaWiki to their corresponding items and '
          . 'files in Omeka. Choose HTML if you want to preserve formatting. Choose '
          . 'plain text if formatting is not important.'
        ); ?></p>
        <?php echo $this->formRadio(
            'scripto_import_type',
            $this->import_type,
            null,
            array('html' => __('HTML'),
                  'plain_text' => __('plain text')),
            null
        ); ?>
    </div>
</div>

<div class="field">
    <div class="two columns alpha">
        <label for="scripto_home_page_text"><?php echo __('Home page text'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation"><?php echo __(
            'Enter text that will appear on the Scripto home page. Use this to display '
          . 'custom messages to your users, such as instructions on how to use Scripto '
          . 'and how to register for a MediaWiki account. Default text will appear if '
          . 'nothing is entered. You may use HTML. (Wrapping %s tags recommended.)',
          '&lt;p&gt;&lt;/p&gt;'
        ); ?></p>
        <?php echo $this->formTextarea(
            'scripto_home_page_text',
            get_option('scripto_home_page_text')
        ); ?>
    </div>
</div>
