<?php

class pollElement extends structureElement
{
    public $dataResourceName = 'module_poll';
    protected $allowedTypes = ['pollQuestion'];
    public $defaultActionName = 'show';
    public $role = 'content';
    protected $questionsList;
    protected $resultsIndex; //$resultsIndex[answerId]
    protected $voteCount;

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['description'] = 'text';
        $moduleStructure['answers'] = 'array';
    }

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
        $multiLanguageFields[] = 'description';
    }

    protected function getTabsList()
    {
        return [
            'showForm',
            'showResults',
            'showPositions',
            'showPrivileges',
        ];
    }

    public function getQuestionsList()
    {
        if (is_null($this->questionsList)) {
            $structureManager = $this->getService('structureManager');
            $this->questionsList = $structureManager->getElementsChildren($this->id);
        }
        return $this->questionsList;
    }

    public function currentIpHasVoted()
    {
        $IP = $this->getService('user')->IP;
        return $this->ipHasVoted($IP);
    }

    public function ipHasVoted(&$IP)
    {
        $collection = persistableCollection::getInstance("polls_votes");
        $search = [
            "pollId" => $this->id,
            "IP" => $IP,
        ];
        $objectsList = $collection->load($search);

        if (count($objectsList) > 0) {
            return true;
        }
        return false;
    }

    public function getResults()
    {
        $voters = $this->getVoteCount();
        $this->resultsIndex = [];
        $collection = persistableCollection::getInstance("polls_votes");
        $conditions = [
            [
                "pollId",
                "=",
                $this->id,
            ],
        ];
        $group = ["answerId"];
        $answersData = $collection->conditionalLoad([
            "answerId",
            "count(answerId)",
        ], $conditions, [], null, $group, true);
        foreach ($answersData as &$answer) {
            $this->resultsIndex[$answer['answerId']] = number_format($answer['count(answerId)'] / $voters * 100, 0);
        }
    }

    public function getAnswerResults($answerId)
    {
        if (is_null($this->resultsIndex)) {
            $this->getResults();
        }
        if (isset($this->resultsIndex) && isset($this->resultsIndex[$answerId])) {
            return $this->resultsIndex[$answerId];
        }
        return 0;
    }

    public function getVoteCount()
    {
        if (is_null($this->voteCount)) {
            $this->voteCount = 0;
            // return total number of voters for polls
            $collection = persistableCollection::getInstance("polls_votes");

            $conditions = [
                [
                    "pollId",
                    "=",
                    $this->id,
                ],
            ];
            $group = ["IP"];

            if ($voteCollection = $collection->conditionalLoad(null, $conditions, [], [], $group)) {
                $this->voteCount = count($voteCollection);

                // check for irrelevant votes
                $questions = $this->getQuestionsList();
                $answers = [];
                foreach ($questions as &$question) {
                    $answers = array_merge($question->getAnswersList(), $answers);
                }

                foreach ($voteCollection as &$vote) {
                    $bFound = false;
                    foreach ($answers as &$answer) {
                        if ($vote["answerId"] == $answer->id) {
                            $bFound = true;
                        }
                    }
                    if (!$bFound) {
                        $this->voteCount--;
                    }
                }
            }
        }
        return $this->voteCount;
    }
}

