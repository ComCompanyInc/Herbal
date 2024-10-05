<?php

namespace App\Form;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

//php -S localhost:8000 -t public
class RegistrationForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Имя: ',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('surname', TextType::class, [
                'label' => 'Фамилия: ',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('patronymic', TextType::class, [
                'label' => 'Отчество: ',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('dateOfBirth', DateType::class, [
                'label' => 'Дата рождения: ',
                'years'  =>  range(1950,2099),
                'attr' => ['class' => 'form-control'],
            ])
            ->add('country', EntityType::class, [
                'label' => 'Город: ',
                'attr' => ['class' => 'form-control'],
                'class' => Country::class,
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail: ',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Пароль: ',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Регистрироваться",
                'attr' => ['class' => 'btn btn-primary btn-lg mt-3'],
            ]);
    }

    /*public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Access::class,
        ]);
    }*/
}