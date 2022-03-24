<?php

namespace Allsoftware\SymfonyKernelTabler\Form\Type;

use Allsoftware\SymfonyKernelTabler\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines the custom form field type used to change user's password.
 */
class ChangePasswordType extends AbstractType
{
    public function __construct(
        private AuthorizationCheckerInterface $auth,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->auth->isGranted(User::CST_Role_Admin) === false) {
            $builder
                ->add('currentPassword', PasswordType::class, [
                    'constraints' => [
                        new UserPassword(),
                    ],
                    'label' => 'label.current_password',
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ])
            ;
        }

        $builder
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        min: 6,
                        max: 128,
                    ),
                ],
                'first_options' => [
                    'label' => 'label.new_password',
                ],
                'second_options' => [
                    'label' => 'label.new_password_confirm',
                ],
            ])
        ;
    }
}
