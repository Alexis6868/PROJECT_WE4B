<?php

namespace App\Form;

use App\Entity\Entrepot;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Char léger' => 'Char léger',
                    'Char moyen' => 'Char moyen',
                    'Char lourd' => 'Char lourd',
                    'Transport' => 'Transport',
                    'Spécial' => 'Spécial',
                ],
                'placeholder' => 'Choisir un type',
            ])
            ->add('image')
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Opérationnel' => 'Opérationnel',
                    'En maintenance' => 'En maintenance',
                    'En panne' => 'En panne',
                    'Réservé' => 'Réservé',
                ],
                'placeholder' => 'Choisir un état',
            ])
            ->add('description')
            ->add('masse')
            ->add('indice_maintenance')
            ->add('entrepot', EntityType::class, [
                'class' => Entrepot::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
