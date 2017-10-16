<?php

namespace AppBundle\Form;

use Symfony\Component\{
    Form\AbstractType,
    Form\FormBuilderInterface,
    Validator\Constraints\NotBlank,
    OptionsResolver\OptionsResolver,
    Form\Extension\Core\Type\IntegerType,
    Form\Extension\Core\Type\ChoiceType
};

use AppBundle\Model\Board\Field          as ModelField;
use AppBundle\Model\Board\FieldInterface;

/**
 * @author Maksym Ivanov
 */
class BoardField extends AbstractType
{
    /**
     * Validators for fields
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('x', IntegerType::class, [
                'required' => true,
                'scale' => 30,
                'empty_data' => 0
            ])
            ->add('y', IntegerType::class, [
                'required' => true,
                'scale' =>  30,
                'empty_data' => 0
            ])
            ->add('value', ChoiceType::class, [
                'choices' => ['', FieldInterface::PRIMARY_PLAYER_SYMBOL, FieldInterface::SECONDARY_PLAYER_SYMBOL]
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
        return 'boardField';
    }
}