<?php
/**
 * The Scripto Ajax controller class.
 *
 * @package Scripto
 */
class Scripto_AjaxController extends Omeka_Controller_AbstractActionController
{
    /**
     * Handle AJAX requests to update status of an item.
     */
    public function updateAction()
    {
        if (!$this->_checkAjax('update')) {
            return;
        }

        // Handle action.
        try {
            $status = $this->_getParam('status');
            if (!in_array($status, array('Not to transcribe', 'To transcribe'))) {
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }

            $id = (integer) $this->_getParam('id');
            $item = get_record_by_id('Item', $id);
            if (!$item) {
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }
            $currentStatus = $item->getElementTexts('Scripto', 'Status');
            if (!empty($currentStatus)) {
                if ($status === $currentStatus[0]) {
                    return;
                }
                $item->setReplaceElementTexts();
            }
            $element = $item->getElement('Scripto', 'Status');
            $item->addTextForElement($element, $status);
            $item->save();
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
        }
    }

    /**
     * Handle AJAX requests to fill transcription of an item from source
     * element.
     */
    public function fillPagesAction()
    {
        if (!$this->_checkAjax('fill-pages')) {
            return;
        }

        // Handle action.
        try {
            $id = (integer) $this->_getParam('id');
            $scripto = ScriptoPlugin::getScripto();
            if (!$scripto->documentExists($id)) {
                $this->getResponse()->setHttpResponseCode(400);
                return;
            }
            // Get some variables.
            list($elementSetName, $elementName) = explode(':', get_option('scripto_source_element'));
            $type = get_option('scripto_import_type');

            $doc = $scripto->getDocument($id);
            // Check all pages, created or not.
            foreach ($doc->getPages() as $pageId => $pageName) {
                // If the page doesn't exist, it is created automatically with
                // text from source element.
                $doc->setPage($pageId);
                // Else, edit the transcription if the page is already created.
                if ($doc->isCreatedPage()) {
                    $file = get_record_by_id('File', $pageId);
                    $transcription = $file->getElementTexts($elementSetName, $elementName);
                    $transcription = empty($transcription) ? '' : $transcription[0]->text;
                    $flagProtect = $doc->isProtectedTranscriptionPage();
                    if ($flagProtect) {
                        $doc->unprotectTranscriptionPage();
                    }
                    $doc->editTranscriptionPage($transcription);
                    // Automatic update of metadata.
                    $doc->setPageTranscriptionStatus();
                    $doc->setDocumentTranscriptionProgress();
                    $doc->setItemSortWeight();
                    $doc->exportPage($type);
                    if ($flagProtect) {
                        $doc->protectTranscriptionPage();
                    }
                }
            }
            $this->getResponse()->setBody('success');
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
        }
    }

    /**
     * Check AJAX requests.
     *
     *
     * 403 Forbidden
     * 400 Bad Request
     * 500 Internal Server Error
     *
     * @param string $action
     */
    protected function _checkAjax($action)
    {
        // Don't render the view script.
        $this->_helper->viewRenderer->setNoRender(true);

        // Only allow AJAX requests.
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest()) {
            $this->getResponse()->setHttpResponseCode(403);
            return false;
        }

        // Allow only valid calls.
        if ($request->getControllerName() != 'ajax'
                || $request->getActionName() != $action
            ) {
            $this->getResponse()->setHttpResponseCode(400);
            return false;
        }

        // All admin users are allowed.

        return true;
    }
}
