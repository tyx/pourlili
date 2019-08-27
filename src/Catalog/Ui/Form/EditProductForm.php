<?php
namespace App\Catalog\Ui\Form;

use App\Catalog\App\Command\RegisterNewProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('name', TextType::class)
            ->add('price', NumberType::class)
            ->add('image', TextType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class)
        ;
    }
}
