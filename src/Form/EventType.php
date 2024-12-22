<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert; 

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom de l\'événement ne peut pas être vide.'
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 100,
                        'minMessage' => 'Le nom de l\'événement doit comporter au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom de l\'événement ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('date',null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de l\'événement est obligatoire.',
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => new \DateTime(),  // La date doit être égale ou supérieure à aujourd'hui
                        'message' => 'La date de l\'événement ne peut pas être dans le passé.',
                    ]),
                ],
            ])
            
            
            ->add('location', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le lieu de l\'événement ne peut pas être vide.'
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
