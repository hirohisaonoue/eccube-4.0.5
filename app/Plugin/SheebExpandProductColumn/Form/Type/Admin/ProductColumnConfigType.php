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

use Eccube\Common\EccubeConfig;
use Plugin\SheebExpandProductColumn\Entity\ProductColumn;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProductColumnConfigType

 */
class ProductColumnConfigType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('code', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => "/^[a-zA-Z0-9\_]+$/",
                        'match' => true,
                        'message' => 'sheeb_expand_product_column.error.alphabet_or_number',
                    ]),
                ],
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => $this->eccubeConfig['sheeb_expand_product_column.choices.column_types']
            ])
            ->add('is_multiple', ChoiceType::class, [
                'choices' => $this->eccubeConfig['sheeb_expand_product_column.choices.switch'],
                'mapped' => true,
            ])
            ->add('is_searchable', ChoiceType::class, [
                'choices' => $this->eccubeConfig['sheeb_expand_product_column.choices.switch'],
                'mapped' => true,
            ])
            ->add('setting', TextareaType::class, [])
            ->add('csv', HiddenType::class, [
                'property_path' => 'Csv.id',
                'mapped' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductColumn::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    function getBlockPrefix()
    {
        return 'sheeb_expand_product_column';
    }
}
