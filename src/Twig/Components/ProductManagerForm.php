<?php

namespace App\Twig\Components;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use App\Services\FileUploaderService;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsLiveComponent]
final class ProductManagerForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;


    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ProductType::class );
    }

    #[LiveAction]
    public function save(Request $request,EntityManagerInterface $entityManager,FileUploaderService $fileUploader)
    {
        $this->submitForm();
        /** @var Product $product */
        $product = $this->getForm()->getData();
        
        $uploadedFile = $request->files->get('product')['image'] ?? null;
        
       
        if ($uploadedFile instanceof UploadedFile) {
            $newFilename = $fileUploader->uploadFile($uploadedFile);
            $product->setImageFileName($newFilename);
        }
        
      
     
        $entityManager->persist($product);
        $entityManager->flush();

        $this->dispatchBrowserEvent('modal:close');
        $this->resetForm();

    }
}