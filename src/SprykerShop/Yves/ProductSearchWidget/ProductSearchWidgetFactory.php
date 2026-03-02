<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\ProductSearchWidget;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;
use SprykerShop\Yves\ProductSearchWidget\Dependency\Client\ProductSearchWidgetToCatalogClientInterface;
use SprykerShop\Yves\ProductSearchWidget\Dependency\Client\ProductSearchWidgetToLocaleClientInterface;
use SprykerShop\Yves\ProductSearchWidget\Dependency\Service\ProductSearchWidgetToUtilEncodingServiceInterface;
use SprykerShop\Yves\ProductSearchWidget\Form\DataProvider\ProductQuickAddFormDataProvider;
use SprykerShop\Yves\ProductSearchWidget\Form\ProductQuickAddForm;
use SprykerShop\Yves\ProductSearchWidget\Mapper\ProductConcreteMapper;
use SprykerShop\Yves\ProductSearchWidget\Mapper\ProductConcreteMapperInterface;
use SprykerShop\Yves\ProductSearchWidget\Reader\ProductConcreteReader;
use SprykerShop\Yves\ProductSearchWidget\Reader\ProductConcreteReaderInterface;
use SprykerShop\Yves\ProductSearchWidget\Resolver\ShopContextResolver;
use SprykerShop\Yves\ProductSearchWidget\Resolver\ShopContextResolverInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ProductSearchWidgetFactory extends AbstractFactory
{
    public function createProductConcreteReader(): ProductConcreteReaderInterface
    {
        return new ProductConcreteReader(
            $this->getCatalogClient(),
            $this->createProductConcreteMapper(),
        );
    }

    public function createProductConcreteMapper(): ProductConcreteMapperInterface
    {
        return new ProductConcreteMapper();
    }

    public function getCatalogClient(): ProductSearchWidgetToCatalogClientInterface
    {
        return $this->getProvidedDependency(ProductSearchWidgetDependencyProvider::CLIENT_CATALOG);
    }

    public function getProductQuickAddForm(): FormInterface
    {
        return $this->getFormFactory()->create(
            ProductQuickAddForm::class,
            [],
            $this->createProductQuickAddFormDataProvider()->getOptions(),
        );
    }

    public function getFormFactory(): FormFactoryInterface
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    public function getUtilEncodingService(): ProductSearchWidgetToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(ProductSearchWidgetDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    public function createProductQuickAddFormDataProvider(): ProductQuickAddFormDataProvider
    {
        return new ProductQuickAddFormDataProvider($this->getLocaleClient());
    }

    public function getLocaleClient(): ProductSearchWidgetToLocaleClientInterface
    {
        return $this->getProvidedDependency(ProductSearchWidgetDependencyProvider::CLIENT_LOCALE);
    }

    /**
     * @return array<\SprykerShop\Yves\ProductSearchWidgetExtension\Dependency\Plugin\ProductQuickAddFormExpanderPluginInterface>
     */
    public function getProductQuickAddFormExpanderPlugins(): array
    {
        return $this->getProvidedDependency(ProductSearchWidgetDependencyProvider::PLUGINS_PRODUCT_QUICK_ADD_FORM_EXPANDER);
    }

    public function createShopContextResolver(): ShopContextResolverInterface
    {
        return new ShopContextResolver($this->getContainer());
    }
}
