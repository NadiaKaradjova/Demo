<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Isbn;
use Symfony\Component\Validator\Constraints\IsbnValidator;
use Symfony\Component\Validator\Tests\Constraints\IsbnValidatorTest;
use Vich\UploaderBundle\Form\Type\VichFileType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('ISBN', TextType::class)
            ->add('year', TextType::class)
            ->add('description', TextType::class)
//            ->add('coverImage', VichFileType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Book::class,
            'allow_extra_fields' => true,
            'error_bubbling' => true
        ));
    }
}
