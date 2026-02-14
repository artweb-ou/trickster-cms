<?php

use App\Users\CurrentUserService;

class submitVotePoll extends structureElementAction
{
    public function execute(&$structureManager, &$controller, &$structureElement)
    {
        // verify that all questions were answered

        $questions = $structureElement->getQuestionsList();
        $idIndex = [];
        foreach ($questions as &$question) {
            $bQuestionsAnswered = false;
            foreach ($structureElement->answers as $questionId => $answersList) {
                if ($questionId == $question->id) {
                    $bQuestionsAnswered = true;
                    break;
                }
            }
            if (!$bQuestionsAnswered) {
                $idIndex[$questionId] = true;
            }
        }

        if (count($idIndex)) {
            $structureElement->setFormError("answers", $idIndex);
        }

        // send results to DB, redirect the user
        if ($this->validated) {
            $collection = persistableCollection::getInstance("polls_votes");
            $currentUserService = $this->getService(CurrentUserService::class);
            $IP = $currentUserService->getCurrentUser()->IP;

            foreach ($structureElement->answers as $questionId => $answersList) {
                foreach ($answersList as &$answerId) {
                    $row = $collection->getEmptyObject();

                    $row->pollId = $structureElement->id;
                    $row->questionId = $questionId;
                    $row->answerId = $answerId;
                    $row->IP = $IP;
                    $row->timestamp = time();
                    $row->persist();
                }
            }
            $currentElement = $structureManager->getCurrentElement();
            $controller->redirect($currentElement->URL);
        }
        $structureElement->executeAction("show");
    }

    public function setExpectedFields(&$expectedFields)
    {
        $expectedFields = [
            'answers',
        ];
    }

    public function setValidators(&$validators)
    {
        //		$validators['title'][] = 'notEmpty';
        //		$validators['description'][] = 'notEmpty';
    }
}




