<?php

namespace App\Form\Note;

use App\Model\Note\ItemModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameMatch', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->add('sellPrice', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'step' => 0.01,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\PositiveOrZero(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemModel::class,
        ]);
    }
}
