<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class MediaType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'by_reference' => false,
                    'constraints'  => [
                        new NotNull(),
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => Media::class,
            'csrf_protection' => false,
        ]);
    }
}
