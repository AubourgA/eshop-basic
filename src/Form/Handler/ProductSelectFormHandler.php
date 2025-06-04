<?php

namespace App\Form\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use App\Form\ProductSelectType;
use Symfony\Component\Form\FormFactoryInterface;

class ProductSelectFormHandler
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private RouterInterface $router
    ) {}

    public function handle(Request $request): ?RedirectResponse
    {
        $form = $this->formFactory->create(ProductSelectType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData()['product'] ?? null;

            
            if ($product) {
                $url = $this->router->generate('app_admin_stock_detail', ['id' => $product->getId()]);
                return new RedirectResponse($url);
            }
        }

        return null;
    }

    public function createForm()
    {
        return $this->formFactory->create(ProductSelectType::class);
    }
}
