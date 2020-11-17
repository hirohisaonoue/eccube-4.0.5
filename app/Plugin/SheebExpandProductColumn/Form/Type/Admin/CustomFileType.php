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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class CustomFileType
 */
class CustomFileType extends FileType
{
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new CallbackTransformer(
                // String to File
                function ($fileName) {
                    return new File($fileName, false);
                },
                // File to String
                function ($File) {
                    $result = $File;
                    if ($File instanceof File) {
                        $result = $File->getFilename();
                    }
                    return $result;
                }
            ))
        ;
    }

}
