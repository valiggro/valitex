<?php

namespace App\Form\Setting;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NoteLastNumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note_last_number', IntegerType::class, [
                'row_attr' => [
                    'class' => 'd-inline-flex input-group',
                    'style' => 'max-width: 300px;',
                ],
                'label' => 'Ultimul număr:',
                'attr' => [
                    'min' => 0,
                    'class' => 'text-end',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('save', SubmitType::class, [
                'row_attr' => [
                    'class' => 'd-inline-flex ms-3',
                ],
                'label' => '<i class="bi bi-floppy-fill"></i> Salvează',
                'label_html' => true,
                'attr' => [
                    'class' => 'btn btn-success',
                ],
            ]);
    }
}
