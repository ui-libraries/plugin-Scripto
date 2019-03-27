<fieldset id="fieldset-scripto-wiki"><legend><?php echo __('Relation between Omeka and MediaWiki'); ?></legend>
    <p class="explanation">
        <?php echo __(
    'This plugin requires you to download and install %1$sMediaWiki%2$s, a popular free '
  . 'web-based wiki software application that Scripto uses to manage user and transcription '
  . 'data. Once you have successfully installed MediaWiki, you can complete the following '
  . 'form and install the plugin.',
    '<a href="http://www.mediawiki.org/wiki/MediaWiki">', '</a>'
        ); ?>
    </p>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('scripto_mediawiki_api_url',
                __('MediaWiki API URL')); ?>
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
            <?php echo $this->formLabel('scripto_mediawiki_cookie_prefix',
                __('MediaWiki cookie prefix')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __(
                'Enter your %sMediaWiki cookie prefix%s. This is most likely your MediaWiki database name. Only required for MediaWiki installations since 1.27.0.',
                '<a href="https://www.mediawiki.org/wiki/Manual:$wgCookiePrefix">',
                '</a>'); ?>
            </p>
            <?php echo $this->formText(
                'scripto_mediawiki_cookie_prefix',
                get_option('scripto_mediawiki_cookie_prefix')
            ); ?>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('scripto_source_element',
                __('Element source used to initialize transcription')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php
                echo __('A document page is automatically initialized with this element, if available.');
                echo ' ' . __('This is helpful when Scripto is used to correct OCR for typescript pages.');
                echo ' ' . __('This can be useful to keep track of original source too.');
                echo ' ' . __('When the transcription is updated, it is always save into the Scripto:Transcription field.');
            ?></p>
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
            <?php echo $this->formLabel('scripto_import_type',
                __('Import type')); ?>
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
</fieldset>
<fieldset id="fieldset-scripto-account"><legend><?php echo __('Accounts'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('scripto_allow_register',
                __('Allow Direct Register')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation">
                <?php echo __('Allow people to create a Mediawiki account for Scripto through Omeka.'); ?>
                <?php echo __('Note that this can be a source of spam.'); ?>
            </p>
            <?php echo $this->formCheckbox('scripto_allow_register', true,
                    array('checked' => (bool) get_option('scripto_allow_register'))); ?>
        </div>
    </div>
</fieldset>
<fieldset id="fieldset-scripto-viewer"><legend><?php echo __('Viewer'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('scripto_image_viewer',
                __('Image viewer')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __(
                'Select an image viewer to use when transcribing image files. %1$sOpenLayers%2$s '
              . 'can display JPEG, PNG, GIF, and BMP formats.',
                '<a href="https://openlayers.org/">', '</a>'
            ); ?></p>
            <?php echo $this->formRadio(
                'scripto_image_viewer',
                $this->image_viewer,
                null,
                array('default' => __('Omeka default'),
                      'openlayers' => __('OpenLayers')),
                null
            ); ?>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('scripto_viewer_class',
                __('Class to add to the image viewer')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __(
                'If any, this class will be added to the image viewer.'
            ); ?></p>
            <?php echo $this->formText(
                'scripto_viewer_class',
                get_option('scripto_viewer_class')
            ); ?>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('scripto_use_google_docs_viewer',
                __('Use Google Docs Viewer?')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __(
                'Use Google Docs Viewer when transcribing document files? Document files '
              . 'include PDF, DOC, PPT, XLS, TIFF, PS, and PSD formats. By using this service '
              . 'you acknowledge that you have read and agreed to the %1$sGoogle Terms of Service%2$s.',
              '<a href="https://www.google.com/intl/en/policies/terms/">', '</a>'
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
            <?php echo $this->formLabel('scripto_iframe_class',
                __('Class to add to the document viewer')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __(
                'If any, this class will be added to the document viewer.'
            ); ?></p>
            <?php echo $this->formText(
                'scripto_iframe_class',
                get_option('scripto_iframe_class')
            ); ?>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('scripto_file_source',
                __('File to display in the document viewer')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __(
                'If the original image is too heavy, it is possibile to choose a derivative, for example "fullsize".'
            ); ?></p>
            <?php echo $this->formText(
                'scripto_file_source',
                get_option('scripto_file_source')
            ); ?>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('scripto_files_order',
                __('Order of files')); ?>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"><?php echo __(
                'Pages of a document can be ordered by predefined order (default), by filename or by id.'
            ); ?></p>
            <?php
                echo $this->formSelect('scripto_files_order',
                    get_option('scripto_files_order'),
                    array(),
                    array(
                        'order' => __('Predefined order'),
                        'filename' => __('Original filename'),
                        'id' => __('File id'),
                ));
            ?>
        </div>
    </div>
</fieldset>

<fieldset id="fieldset-scripto-home"><legend><?php echo __('Scripto Home Page'); ?></legend>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('scripto_home_page_text',
                __('Home page text')); ?>
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
</fieldset>
