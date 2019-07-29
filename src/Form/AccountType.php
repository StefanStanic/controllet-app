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
                        'CASH' => 'CASH',
                        'BANK' => 'BANK',
                        'PAYPAL' => 'PAYPAL',
                        'CHECKS' => 'CHECKS'
                    ],
                    'label' => false
                ))
            ->add('account_balance', IntegerType::class,
                array(
                    'label' => false
                ))
            ->add('currency', ChoiceType::class,
                array(
                    'choices' => [
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                        'RSD' => 'RSD'
                    ],
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
