<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer votre nom',
                    ),
                    new Length(
                        min: 2,
                        minMessage: 'Votre nom doit contenir au moins {{ limit }} caractères',
                        max: 50,
                        maxMessage: 'Votre nom ne peut pas contenir plus de {{ limit }} caractères',
                    ),
                ],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer votre prénom',
                    ),
                    new Length(
                        min: 2,
                        minMessage: 'Votre prénom doit contenir au moins {{ limit }} caractères',
                        max: 50,
                        maxMessage: 'Votre prénom ne peut pas contenir plus de {{ limit }} caractères',
                    ),
                ],
            ])
            ->add('email',EmailType::class,[
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer votre adresse email',
                    ),
                   new Email([
                        'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.'
                    ]),
                ],
            ] )
            ->add('tel', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Length(
                        min: 10,
                        max: 15,
                        minMessage: 'Votre numéro de téléphone doit contenir au moins {{ limit }} chiffres',
                        maxMessage: 'Votre numéro de téléphone ne peut pas contenir plus de {{ limit }} chiffres',
                    ),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(
                        message: 'Vous devez accepter nos conditions générales pour vous inscrire.',
                    ),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer un mot de passe',
                    ),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
