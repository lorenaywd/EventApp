<?php

namespace App\Form;

use App\Entity\event;
use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom ne peut pas être vide.',
                ]),
                new Assert\Length([
                    'min' => 3,
                    'max' => 50,
                    'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
                    'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                ]),
            ],
        ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\Email([
                        'mode' => 'strict',
                        'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas valide.',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
