<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        $universities = array(
            'Universitatea Politehnica din București' => 'Universitatea Politehnica din București',
            'Academia de Studii Economice (ASE)' => 'Academia de Studii Economice (ASE)',
            'Academia de Poliție Alexandru Ioan Cuza din București' => 'Academia de Poliție Alexandru Ioan Cuza din București',
            'Academia Națională de Informații' => 'Academia Națională de Informații',
            'Academia Tehnică Militară din București' => 'Academia Tehnică Militară din București',
            'Academia Națională de Educație Fizică și Sport (ANEFS)' => 'Academia Națională de Educație Fizică și Sport (ANEFS)',
            'Centrul de Cercetare și Consultanță în Domeniul Culturii' => 'Centrul de Pregătire Profesională în Cultură',
            'Centrul de Pregătire Profesională în Cultură' => 'Centrul de Pregătire Profesională în Cultură',
            'Școala Națională de Studii Politice și Administrative (SNSPA)' => 'Școala Națională de Studii Politice și Administrative (SNSPA)',
            'Școala Națională de Sănătate Publică, Management și Perfecționare în Domeniul Sanitar București' => 'Școala Națională de Sănătate Publică, Management și Perfecționare în Domeniul Sanitar București',
            'Școala Superioară de Aviație Civilă' => 'Școala Superioară de Aviație Civilă',
            'Universitatea din București' => 'Universitatea din București',
            'Universitatea de Arhitectură și Urbanism Ion Mincu' => 'Universitatea de Arhitectură și Urbanism Ion Mincu',
            'Universitatea Națională de Arte' => 'Universitatea Națională de Arte',
            'Universitatea de Medicină și Farmacie Carol Davila din București' => 'Universitatea de Medicină și Farmacie Carol Davila din București',
            'Universitatea de Științe Agronomice și Medicină Veterinară din București' => 'Universitatea de Științe Agronomice și Medicină Veterinară din București',
            'Universitatea Națională de Artă Teatrală și Cinematografică Ion Luca Caragiale (UNATC)' => 'Universitatea Națională de Artă Teatrală și Cinematografică Ion Luca Caragiale (UNATC)',
            'Universitatea Națională de Muzică Ciprian Porumbescu din București' => 'Universitatea Națională de Muzică Ciprian Porumbescu din București',
            'Academia de Științe Agricole și Silvice „Gheorghe Ionescu-Șișești” (ASAS)' => 'Academia de Științe Agricole și Silvice „Gheorghe Ionescu-Șișești” (ASAS)',
            'Universitatea Tehnică de Construcții din București (UTCB)' => 'Universitatea Tehnică de Construcții din București (UTCB)',
            'Universitatea Națională de Apărare „Carol I”' => 'Universitatea Națională de Apărare „Carol I”',
            'Universitatea „Spiru Haret”' => 'Universitatea „Spiru Haret”',
            'Universitatea „Bioterra”' => 'Universitatea „Bioterra”',
            'Universitatea „Spiru Haret”' => 'Universitatea „Spiru Haret”',
            'Universitatea Româno-Americană' => 'Universitatea Româno-Americană',
            'Universitatea Ecologică' => 'Universitatea Ecologică',
            'Altele' => 'Altele',
        );

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
            ->add('university', ChoiceType::class, array(
                'label' => 'Universitate', 
                'attr' => array('class' => 'input-md round form-control'),
                'choices'  => $universities
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
