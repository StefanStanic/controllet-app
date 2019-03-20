<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('account_name', TextType::class,
                array(
                    'label' => false
                ))
            ->add('account_type', ChoiceType::class,
                array(
                    'choices' => [
                        'cash' => true,
                        'bank' => true,
                        'paypal' => true,
                        'checks' => true
                    ],
                    'label' => false
                ))
            ->add('account_balance', IntegerType::class,
                array(
                    'label' => false
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
