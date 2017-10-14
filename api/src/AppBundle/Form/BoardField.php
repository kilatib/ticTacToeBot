<?php

namespace AppBundle\Form;

use Symfony\Component\{
    Form\AbstractType,
    Form\FormBuilderInterface,
    Validator\Constraints\NotBlank,
    OptionsResolver\OptionsResolver,
    Form\Extension\Core\Type\NumberType,
    Form\Extension\Core\Type\TextType
};

use AppBundle\Model\Board\Field as ModelField;

/**
 * @author Maksym Ivanov
 */
class BoardField extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('x', NumberType::class, [
                'required' => true,
//                'scale' => 'int',
            ])
            ->add('y', NumberType::class, [
                'required' => true,
//                'scale' => 'int',
            ])
            ->add('value', TextType::class, [
                'empty_data' => ''
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => ModelField::class,
        ]);
    }

    public function getName()
    {
        return 'board';
    }
}