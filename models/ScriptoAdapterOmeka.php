<?php
/**
 * Omeka adapter for Scripto.
 */
class ScriptoAdapterOmeka implements Scripto_Adapter_Interface
{
    /**
     * @var Omeka_Db
     */
    private $_db;

    /**
     * @var Item
     */
    private $_item;

    /**
     * Set the database object on construction.
     */
    public function __construct()
    {
        $this->_db = get_db();
    }

    /**
     * Indicate whether the document exists in Omeka.
     *
     * @param int|string $documentId The unique document ID
     * @return bool True: it exists; false: it does not exist
     */
    public function documentExists($documentId)
    {
        $this->_item = $this->_getItem($documentId);
        return $this->_validDocument();
    }

    /**
     * Indicate whether the document page exists in Omeka.
     *
     * @param int|string $documentId The unique document ID
     * @param int|string $pageId The unique page ID
     * @return bool True: it exists; false: it does not exist
     */
    public function documentPageExists($documentId, $pageId)
    {
        if (!$this->documentExists($documentId)) {
            return false;
        }
        $item = $this->_item;

        // The Omeka file ID must match the Scripto page ID.
        $files = $item->Files;
        foreach ($files as $file) {
            if ($pageId == $file->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all the pages belonging to the document.
     *
     * @param int|string $documentId The unique document ID
     * @return array An array containing page identifiers as keys and page names
     * as values, in sequential page order.
     */
    public function getDocumentPages($documentId)
    {
        if (!$this->documentExists($documentId)) {
            return false;
        }
        $item = $this->_item;

        $documentPages = array();
        $files = get_db()->getTable('File')->findByItem($item->id, array(), get_option('scripto_files_order'));
        foreach ($files as $file) {
            // The page name is either the Dublin Core title of the file or the
            // file's original filename.
            $titles = $file->getElementTexts('Dublin Core', 'Title');
            if (empty($titles)) {
                $pageName = $file->original_filename;
            } else {
                $pageName = $titles[0]->text;
            }
            $documentPages[$file->id] = $pageName;
        }
        return $documentPages;
    }

    /**
     * Get the URL of the specified document page file.
     *
     * @param int|string $documentId The unique document ID
     * @param int|string $pageId The unique page ID
     * @return string The page file URL
     */
    public function getDocumentPageFileUrl($documentId, $pageId)
    {
        if (!$this->documentExists($documentId)) {
            return false;
        }

        $file = $this->_getFile($pageId);
        return $file->getWebPath(get_option('scripto_file_source'));
    }

    /**
     * Get the first page of the document.
     *
     * @param int|string $documentId The document ID
     * @return int|string
     */
    public function getDocumentFirstPageId($documentId)
    {
        if (!$this->documentExists($documentId)) {
            return false;
        }
        $item = $this->_item;

        return $item->Files[0]->id;
    }

    /**
     * Get the title of the document.
     *
     * @param int|string $documentId The document ID
     * @return string
     */
    public function getDocumentTitle($documentId)
    {
        if (!$this->documentExists($documentId)) {
            return false;
        }
        $item = $this->_item;

        $titles = $item->getElementTexts('Dublin Core', 'Title');
        if (empty($titles)) {
            return '';
        }
        return $titles[0]->text;
    }

    /**
     * Get the name of the document page.
     *
     * @param int|string $documentId The document ID
     * @param int|string $pageId The unique page ID
     * @return string
     */
    public function getDocumentPageName($documentId, $pageId)
    {
        if (!$this->documentExists($documentId)) {
            return false;
        }

        $file = $this->_getFile($pageId);

        // The page name is either the Dublin Core title of the file or the
        // file's original filename.
        $titles = $file->getElementTexts('Dublin Core', 'Title');
        if (empty($titles)) {
            $pageName = $file->original_filename;
        } else {
            $pageName = $titles[0]->text;
        }
        return $pageName;
    }

    /**
     * Get the existing document page transcription if it already exists.
     *
     * @param int|string $pageId The unique page ID
     * @return string
     */
    public function getDocumentPageTranscription($pageId)
    {
        $file = $this->_getFile($pageId);
        if (!$this->documentExists($file->item_id)) {
            return false;
        }

        // The transcription text comes from the the chosen source element of
        // the file (Scripto:Transcription by default).
        // If no existing transcription, then return null.
        list($elementSetName, $elementName) = explode(':', get_option('scripto_source_element'));
        $transcription = $file->getElementTexts($elementSetName, $elementName);
        if (empty($transcription)) {
            $pageText = null;
        } else {
            $pageText = $transcription[0]->text;
        }
        return $pageText;
    }

    /**
     * Indicate whether the document transcription has been imported.
     *
     * @param int|string $documentId The document ID
     * @return bool True: has been imported; false: has not been imported
     */
    public function documentTranscriptionIsImported($documentId)
    {}

    /**
     * Indicate whether the document page transcription has been imported.
     *
     * @param int|string $documentId The document ID
     * @param int|string $pageId The page ID
     */
    public function documentPageTranscriptionIsImported($documentId, $pageId)
    {}

    /**
     * Import a document page's transcription into Omeka.
     *
     * @param int|string $documentId The document ID
     * @param int|string $pageId The page ID
     * @param string $text The text to import
     * @return bool True: success; false: fail
     */
    public function importDocumentPageTranscription($documentId, $pageId, $text)
    {
        if (!$this->documentExists($documentId)) {
            return false;
        }

        $file = $this->_getFile($pageId);
        $element = $file->getElement('Scripto', 'Transcription');
        $file->deleteElementTextsByElementId(array($element->id));
        $isHtml = false;
        if ('html' == get_option('scripto_import_type')) {
            $isHtml = true;
        }
        $text = Scripto::removeNewPPLimitReports($text);
        $file->addTextForElement($element, $text, $isHtml);
        $file->save();
    }

    /**
     * Import an entire document's transcription into Omeka.
     *
     * @param int|string $documentId The document ID
     * @param string $text The text to import
     * @return bool True: success; false: fail
     */
    public function importDocumentTranscription($documentId, $text)
    {
        if (!$this->documentExists($documentId)) {
            return false;
        }
        $item = $this->_item;

        $element = $item->getElement('Scripto', 'Transcription');
        $item->deleteElementTextsByElementId(array($element->id));
        $isHtml = false;
        if ('html' == get_option('scripto_import_type')) {
            $isHtml = true;
        }
        $text = Scripto::removeNewPPLimitReports($text);
        $item->addTextForElement($element, $text, $isHtml);
        $item->save();
    }

    /**
     * Check the transcription status of a document page in the Omeka database.
     *
     * @param int|string $pageId The page ID
     * @return string
     */
    public function documentPageTranscriptionStatus($pageId)
    {
        $file = $this->_getFile($pageId);
        if (!$this->documentExists($file->item_id)) {
            return false;
        }

        $elementTexts = $file->getElementTexts('Scripto', 'Status');
        if (!empty($elementTexts)) {
            $status = array_pop($elementTexts);
            $status = $status->text;
        }
        if (empty($status)) {
            $status = 'Not Started';
        }
        return $status;
    }

    /**
     * Check the transcription status of all document pages in the Omeka base.
     *
     * @internal All files of the item are returned, even if they are not to
     * transcribe.
     *
     * @param int|string $documentId The document ID
     * @return array
     */
    public function allDocumentPagesTranscriptionStatus($documentId)
    {
        if (!$this->documentExists($documentId)) {
            return false;
        }
        $item = $this->_item;

        $db = get_db();
        $element = $db->getTable('Element')->findByElementSetNameAndElementName('Scripto', 'Status');
        $bind = array($item->id);
        $sql = "
            SELECT files.id, IFNULL(element_texts.text, 'Not Started')
            FROM {$db->File} files
                LEFT JOIN {$db->ElementText} element_texts
                    ON element_texts.record_type = 'File'
                        AND element_texts.record_id = files.id
                        AND element_texts.element_id = $element->id
            WHERE files.item_id = ?
            ORDER BY
                files.order ASC,
                files.id ASC
        ";
        $result = $db->fetchPairs($sql, $bind);
        return $result;
    }

    /**
     * Set a page transcription status in Omeka.
     *
     * @param int|string $documentId The document ID
     * @param int|string $pageId The page ID
     * @param int|string $status The page transcription status
     */
    public function importPageTranscriptionStatus($documentId, $pageId, $status)
    {
        // Delete current transcription status.
        $file = $this->_getFile($pageId);
        if (!$this->documentExists($file->item_id)) {
            return false;
        }

        $element = $file->getElement('Scripto', 'Status');
        $file->deleteElementTextsByElementId(array($element->id));
        // Save status to Omeka.
        $file->addTextForElement($element, $status);
        $file->save();
    }

    /**
     * Set the document progress (percent transcribed) in Omeka.
     *
     * @param int|string $documentId The document ID
     * @param int|string $progress The document progress
     */
    public function importDocumentTranscriptionProgress($documentId, $completedProgress, $needsReviewProgress)
    {
        // Delete current values for Percent Completed and Percent Needs Review.
        if (!$this->documentExists($documentId)) {
            return false;
        }
        $item = $this->_item;

        $completed = $item->getElement('Scripto', 'Percent Completed');
        $needsReview = $item->getElement('Scripto', 'Percent Needs Review');
        $item->deleteElementTextsByElementId(array($completed->id));
        $item->deleteElementTextsByElementId(array($needsReview->id));
        // Save progress to Omeka.
        if ($completedProgress != '0') {
            $item->addTextForElement($completed, $completedProgress);
        }
        if ($needsReviewProgress != '0') {
            $item->addTextForElement($needsReview, $needsReviewProgress);
        }
        $item->save();
    }

    /**
     * Set the item sort weight in item-level Omeka record ('Scripto', 'Weight').
     *
     * @param int|string $documentId The document ID
     * @param int|string $weight The 6 digit sort weight
     */
    public function importItemSortWeight($documentId, $weight)
    {
        // Delete current value of item sort weight.
        if (!$this->documentExists($documentId)) {
            return false;
        }
        $item = $this->_item;

        $sortWeight = $item->getElement('Scripto', 'Weight');
        $item->deleteElementTextsByElementId(array($sortWeight->id));
        // Save sort weight to Omeka.
        $item->addTextForElement($sortWeight, $weight);
        $item->save();
    }

    /**
     * Return an Omeka item object.
     *
     * @param int $itemId
     * @return Item|null
     */
    private function _getItem($itemId)
    {
        return $this->_db->getTable('Item')->find($itemId);
    }

    /**
     * Return an Omeka file object.
     *
     * @param int $fileId
     * @return File|int
     */
    private function _getFile($fileId)
    {
        return $this->_db->getTable('File')->find($fileId);
    }

    /**
     * Check if the provided item exists in Omeka and is a valid Scripto
     * document.
     *
     * @param Item $item
     * @return bool
     */
    private function _validDocument()
    {
        $item = $this->_item;

        // Check item.
        if (empty($item)) {
            return false;
        }
        // The item must exist.
        if (!($item instanceof Item)) {
            return false;
        }
        // The item must have at least one file assigned to it.
        if (!isset($item->Files[0])) {
            return false;
        }
        // The item must not have status 'Not to transcribe'.
        $status = $item->getElementTexts('Scripto', 'Status');
        if (!empty($status) && $status[0]->text == 'Not to transcribe') {
            return false;
        }
        return true;
    }
}
