<?php

/*
 * Project Name: 商品情報項目追加プラグイン for 4.0
 * Copyright(c) 2018 Kenji Nakanishi. All Rights Reserved.
 *
 * https://www.facebook.com/web.kenji.nakanishi
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SheebExpandProductColumn\Form\Type\Admin;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CheckBoxType
 */
class CheckBoxType extends ChoiceType
{
    const ARRAY_SEPARATOR = ',';
    const CSV_GROUPING_SEPARATOR = '-';

    public function __construct(ChoiceListFactoryInterface $choiceListFactory = null)
    {
        parent::__construct($choiceListFactory);
    }
    
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->addModelTransformer(new CallbackTransformer(
                // String to Array
                function ($string) {
                    $array = $string;
                    if (is_string($string)) {
                        $array = explode(self::ARRAY_SEPARATOR, $string);
                    }
                    return $array;
                },
                // Array to String
                function ($array) {
                    $string = $array;
                    if (is_array($array)) {
                        $string = implode(self::ARRAY_SEPARATOR, $array);
                    }
                    return $string;
                }
            ))
        ;
    }
}
