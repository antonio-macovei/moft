<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, array(
                'label' => 'Nume', 
                'attr' => array('class' => 'input-md round form-control')
            ))
            ->add('firstName', TextType::class, array(
                'label' => 'Prenume', 
                'attr' => array('class' => 'input-md round form-control')
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Telefon', 
                'attr' => array('class' => 'input-md round form-control')
            ))
            ->add('email', TextType::class, array(
                'label' => 'Email', 
                'attr' => array('class' => 'input-md round form-control')
            ))
            ->add('university', TextType::class, array(
                'label' => 'Universitate', 
                'attr' => array('class' => 'input-md round form-control')
            ))
            ->add('faculty', TextType::class, array(
                'label' => 'Facultate', 
                'attr' => array('class' => 'input-md round form-control')
            ))
            ->add('studentId', TextType::class, array(
                'label' => 'Numarul legitimatiei de student', 
                'attr' => array('class' => 'input-md round form-control')
            ))
            ->add('facebook', TextType::class, array(
                'label' => 'Link Facebook', 
                'attr' => array('class' => 'input-md round form-control')
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array(
                    'attr' => array(
                        'autocomplete' => 'new-password',
                    ),
                ),
                'first_options' => array('label' => 'Parola', 'attr' => array('class' => 'input-md round form-control')),
                'second_options' => array('label' => 'Confirmare parola', 'attr' => array('class' => 'input-md round form-control')),
                'invalid_message' => 'fos_user.password.mismatch',
                
            ))
            ->add('GDPR', CheckboxType::class, array(
                'label'    => 'Ești de acord cu prelucrarea datelor personale? *',
                'required' => true,
                'mapped' => false,
                'attr' => array('class' => 'input-md round form-control checkbox')
            ))
            ->remove('username')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User',
            'csrf_token_id' => 'registration',
        ));
    }

    // BC for SF < 3.0

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fos_user_registration';
    }
}
