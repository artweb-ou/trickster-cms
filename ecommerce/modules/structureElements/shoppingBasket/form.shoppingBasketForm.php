<?php

class ShoppingBasketFormStructure extends ElementForm
{
    protected $structure = [
        'title' => [
            'type' => 'input.text',
        ],
        'destination' => [
            'type' => 'input.email',
        ],
        'columns' => [
            'type' => 'select.index',
            'options' => [
                'left' => 'columns_left',
                'right' => 'columns_right',
                'both' => 'columns_both',
                'none' => 'columns_none',
            ],
            'translationGroup' => 'selector',
        ],
        'paymentSuccessfulText' => [
            'type' => 'input.html',
        ],
        'paymentInvoiceText' => [
            'type' => 'input.html',
        ],
        'paymentQueryText' => [
            'type' => 'input.html',
        ],
        'paymentFailedText' => [
            'type' => 'input.html',
        ],
        'hidden' => [
            'type' => 'input.checkbox',
        ],
        'conditionsLink' => [
            'type' => 'input.text',
        ],

// ALTER TABLE `engine_module_shoppingbasket` ADD `addToBasketButtonAction` INT(2) NOT NULL DEFAULT '1' AFTER `conditionsLink`;
/*
 * one of parts DB data for translations ...
 *
set @id= ( SELECT id FROM `engine_module_translation` ORDER by id DESC limit 1);
set @lang_EST_P = 1123;
set @lang_RUS_P = 1124;
set @lang_ENG_P = 1125;

set @lang_ENG_A = 14;
set @lang_EST_A = 15;
set @lang_ENG_A = 16;

INSERT INTO `engine_module_translation` (`id`, `valueText`, `languageId`, `valueType`, `valueTextarea`, `valueHtml`) VALUES

(@id+1, 'Jätka ostlemist', @lang_EST_P, 'text', '', ''),
(@id+1, 'Продолжить покупки', @lang_RUS_P, 'text', '', ''),
(@id+1, 'Continue Shopping', @lang_ENG_P, 'text', '', ''),

(@id+2, 'Vaata ostukorvi', @lang_EST_P, 'text', '', ''),
(@id+2, 'Посмотреть корзину', @lang_RUS_P, 'text', '', ''),
(@id+2, 'View Cart', @lang_ENG_P, 'text', '', ''),


(@id+3, 'Действие кнопки «Добавить в корзину» при нажатии', @lang_RUS_A, 'text', '', ''),
(@id+3, '&quot;Lisa ostukorvi&quot; nupu toiming klikkimisel', @lang_EST_A, 'text', '', ''),
(@id+3, '&quot;Add To Basket&quot; button action on click', @lang_ENG_A, 'text', '', ''),

(@id+4, 'Нет дополнительных действий (по умолчанию)', @lang_RUS_A, 'text', '', ''),
(@id+4, 'Täiendavaid toiminguid ei ole (vaikimisi)', @lang_EST_A, 'text', '', ''),
(@id+4, 'No additional actions (default)', @lang_ENG_A, 'text', '', ''),

(@id+5, 'Показать Всплывающую Подсказку', @lang_RUS_A, 'text', '', ''),
(@id+5, 'Näidata Kohtspikrit', @lang_EST_A, 'text', '', ''),
(@id+5, 'Show the ToolTip', @lang_ENG_A, 'text', '', ''),

(@id+6, 'Показать Модальное Всплывающее Окно (требуется реакция пользователя)', @lang_RUS_A, 'text', '', ''),
(@id+6, 'Näidata Modaal-Hüpikakent (vajab kasutajapoolset reageeringut)', @lang_EST_A, 'text', '', ''),
(@id+6, 'Show the Modal PopUp (user response required)', @lang_ENG_A, 'text', '', '')
;


INSERT INTO `engine_structure_elements` (`id`, `structureType`, `structureName`, `structureRole`, `dateCreated`, `dateModified`, `marker`)
VALUES
(@id+3, 'adminTranslation', 'addtobasketbuttonaction', 'content', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL),
(@id+4, 'adminTranslation', 'none', 'content', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL),
(@id+5, 'adminTranslation', 'tooltip', 'content', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL),
(@id+6, 'adminTranslation', 'modal', 'content', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), NULL)
;

*/
        'addToBasketButtonAction' => [ // ALTER TABLE `engine_module_shoppingbasket` ADD `addToBasketButtonAction` INT(2) NOT NULL DEFAULT '0' AFTER `conditionsLink`;
            'type' => 'select.index',
            'options' => [
                '0' => 'action_none',
                '1' => 'action_tooltip',
                '2' => 'action_modal',
            ],
        ],
    ];

    protected $additionalContent = 'shared.contentlist_singlepage';
}