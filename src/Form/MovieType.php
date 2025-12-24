<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Movie Name',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a movie name']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Full Description',
                'attr' => ['class' => 'form-control', 'rows' => 5],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a description']),
                ],
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Short Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 3],
            ])
            ->add('totalSeats', IntegerType::class, [
                'label' => 'Total Number of Seats',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter the total number of seats']),
                    new Positive(['message' => 'The number of seats must be positive']),
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Movie Image',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, or GIF)',
                    ]),
                ],
            ])
            ->add('isTrending', CheckboxType::class, [
                'label' => 'Mark as Trending',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('isComingSoon', CheckboxType::class, [
                'label' => 'Mark as Coming Soon',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
            'is_edit' => false,
        ]);
    }
}

