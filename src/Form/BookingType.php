<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberOfSeats', IntegerType::class, [
                'label' => 'Number of Seats',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter the number of seats']),
                    new Positive(['message' => 'The number of seats must be positive']),
                    new GreaterThan(['value' => 0, 'message' => 'The number of seats must be greater than 0']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}

