<?php

class commentDataResponseConverter extends StructuredDataResponseConverter
{
    protected $defaultPreset = 'api';

    protected function getRelationStructure()
    {
        return [
            'id' => 'id',
            'searchTitle' => function ($element) {
                return $element->content;
            },
            'url' => 'getUrl',
            'structureType' => 'structureType',
            'votes' => 'votes',
            'userVote' => 'getUserVote',
        ];
    }

    protected function getPresetsStructure()
    {
        return [
            'api' => [
                'id',
                'dateCreated',
                'dateModified',
                'url',
                'image',
                'content',
                'introduction',
            ],
            'search' => [
                'id',
                'searchTitle',
                'url',
                'structureType',
            ],
        ];
    }
}