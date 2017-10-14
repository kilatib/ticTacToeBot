<?php

namespace AppBundle\Form;

use Symfony\Component\{
    Form\AbstractType,
    Form\FormBuilderInterface,
    Validator\Constraints\NotBlank,
    OptionsResolver\OptionsResolver,
    Form\Extension\Core\Type\CollectionType
};

/**
 * @author Maksym Ivanov
 */
class Board extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('board', CollectionType::class, [
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'entry_type' => BoardField::class
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'cascade_validation' => true,
        ]);
    }

    public function getName()
    {
        return 'board';
    }
}