<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Sales\Communication\Form;

use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Spryker\Zed\Kernel\Communication\Form\FormTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Spryker\Zed\Sales\Business\SalesFacadeInterface getFacade()
 * @method \Spryker\Zed\Sales\Communication\SalesCommunicationFactory getFactory()
 * @method \Spryker\Zed\Sales\Persistence\SalesQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Sales\SalesConfig getConfig()
 * @method \Spryker\Zed\Sales\Persistence\SalesRepositoryInterface getRepository()
 */
class EditShipmentForm extends AbstractType
{
    public const FIELD_ADDRESS_FORM_ID = 'address_form_id';
    public const FIELD_DELIVERY_DATE = 'delivery_date';
    public const FIELD_ORDER_ITEMS_FORM_ID = 'order_items_form_id';
    public const FIELD_SHIPMENT_METHOD = 'shipment_method';
    public const FIELD_DELIVERY_ADDRESS = 'delivery_address';


    protected $addressForm;

    protected $orderItemsForm;

    public function __construct(FormTypeInterface $addressForm, FormTypeInterface $orderItemsForm)
    {
        $this->addressForm = $addressForm;
        $this->orderItemsForm = $orderItemsForm;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'address';
    }

    /**
     * @deprecated Use `getBlockPrefix()` instead.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(self::OPTION_SALUTATION_CHOICES);
        $resolver->setRequired(self::OPTION_COUNTRY_CHOICES);
    }

    /**
     * @deprecated Use `configureOptions()` instead.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this
            ->addAddressForm($builder)
            ->addOrderItemsForm($builder)
            ->addShipmentMethodField($builder)
            ->addDeliveryDateField($builder)
            ;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    public function addDeliveryAddressField(FormBuilderInterface $builder)
    {
        $builder->add(
            self::FIELD_DELIVERY_ADDRESS,
            RadioType::class, [
                'constraints' => [
                ],
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return $this
     */
    protected function addAddressForm(FormBuilderInterface $builder)
    {
        $builder->add(
            self::FIELD_ADDRESS_FORM_ID,
            $this->addressForm
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $choices
     *
     * @return $this
     */
    protected function addOrderItemsForm(FormBuilderInterface $builder)
    {
        $builder->add(
            self::FIELD_ITEMS_FORM_ID,
            $this->orderItemsForm
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    public function addShipmentMethodField(FormBuilderInterface $builder)
    {
        $builder->add(
            self::FIELD_SHIPMENT_METHOD,
            TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    public function addDeliveryDateField(FormBuilderInterface $builder)
    {
        $builder->add(
            self::FIELD_DELIVERY_DATE,
            TextType::class, [
                'constraints' => [
                ],
            ]
        );

        return $this;
    }
}
