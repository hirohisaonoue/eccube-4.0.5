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

namespace Plugin\SheebExpandProductColumn;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeTwigBlock;
use Eccube\Twig\Environment;
use Plugin\SheebExpandProductColumn\Twig\Extension\Methods;

class TwigBlock implements EccubeTwigBlock
{
    /**
     * @var Environment 
     */
    private $twig;

    public function __construct(Environment $twig, EntityManagerInterface $em)
    {
        $this->twig = $twig;
        $this->twig->addExtension(new Methods($em));
    }
    
    /**
     * @return array
     */
    public static function getTwigBlock()
    {
        return [];
    }
}
