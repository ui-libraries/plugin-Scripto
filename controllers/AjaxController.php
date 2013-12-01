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
