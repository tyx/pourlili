<?php
namespace App\Catalog\Ui\Form;

use App\Catalog\App\Command\RegisterNewProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewProductForm extends AbstractType
{
    private $listId;

    private $productId;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->listId = $options['list_id'];
        $this->productId = $options['product_id'];
        $builder
            ->add('name', TextType::class)
            ->add('price', NumberType::class, [
                'required' => false,
            ])
            ->add('image', TextType::class)
            ->add('description', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['list_id', 'product_id']);
        $resolver->setDefaults([
            'data_class' => RegisterNewProduct::class,
            'empty_data' => function (FormInterface $form) {
                return new RegisterNewProduct(
                    $this->listId,
                    $this->productId,
                    $form->get('name')->getData(),
                    $form->get('price')->getData(),
                    $form->get('image')->getData(),
                    $form->get('description')->getData()
                );
            },
        ]);
    }
}
