<?php
//
//class commentShared extends structureElementAction
//{
//    protected $loggable = true;
//
//    public function execute(&$structureManager, &$controller, &$structureElement)
//    {
//        if ($this->validated) {
//            $structureManager->setNewElementLinkType('commentTarget');
//            $newCommentElement = $structureManager->createElement('comment', 'show', $structureElement->id);
//            if ($newCommentElement) {
//                $newCommentElement->prepareActualData();
//                $fields = array(
//                    'author',
//                    'email',
//                    'content',
//                    'dateTime',
//                    'ipAddress',
//                    'languageId',
//                    'targetType',
//                );
//                $data = array(
//                    'author'     => $structureElement->comment_author,
//                    'email'      => $structureElement->comment_email,
//                    'content'    => $structureElement->comment_content,
//                    'dateTime'   => date('d.m.Y H:i', time()),
//                    'ipAddress'  => $this->getService('user')->IP,
//                    'languageId' => $this->getService('LanguagesManager')->getCurrentLanguageId(),
//                    'targetType' => $structureElement->structureType,
//                );
//                $newCommentElement->importExternalData($data, $fields, array());
//                $newCommentElement->persistElementData();
//            }
//            $controller->redirect($structureElement->URL . '?commented=1');
//        } else {
//            $structureElement->executeAction('show');
//        }
//    }
//
//    public function setExpectedFields(&$expectedFields)
//    {
//        $expectedFields = array(
//            'comment_author',
//            'comment_email',
//            'comment_content',
//            'comment_captcha',
//        );
//    }
//
//    public function setValidators(&$validators)
//    {
//        $validators['comment_author'][] = 'notEmpty';
//        $validators['comment_email'][] = 'email';
//        $validators['comment_content'][] = 'notEmpty';
//        $validators['comment_captcha'][] = 'captcha';
//    }
//}
//
//?>
