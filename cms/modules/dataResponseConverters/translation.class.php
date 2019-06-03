<?php

class translationDataResponseConverter extends StructuredDataResponseConverter
{
    use SimpleDataResponseConverter;
    protected $defaultPreset = 'api';
}