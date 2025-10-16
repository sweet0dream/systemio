<?php

namespace App\Controller;

use App\Dto\Request\CalculatePriceDto;
use App\Dto\Request\PurchaseDto;
use App\Repository\ProductRepository;
use App\Service\PriceCalculate;
use App\Factory\PaymentProcessorFactory;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class PriceController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly PriceCalculate $priceCalculateService,
        private readonly PaymentProcessorFactory $paymentProcessorFactory,
    ) {
    }

    #[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculate(
        #[MapRequestPayload] CalculatePriceDto $request
    ): JsonResponse {
        $product = $this->productRepository->find($request->product);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $finalPrice = $this->priceCalculateService->calculate(
                $product,
                $request->taxNumber,
                $request->couponCode
            );

            return $this->json(['price' => $finalPrice]);
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function purchase(
        #[MapRequestPayload] PurchaseDto $request
    ): JsonResponse {
        $product = $this->productRepository->find($request->product);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $finalPrice = $this->priceCalculateService->calculate(
                $product,
                $request->taxNumber,
                $request->couponCode
            );

            $paymentProcessor = $this->paymentProcessorFactory->create($request->paymentProcessor);

            if ($request->paymentProcessor === 'paypal') {
                $paymentProcessor->pay($finalPrice);
            } else {
                if (!$paymentProcessor->processPayment($finalPrice)) {
                    return $this->json(['error' => 'Payment processing failed'], Response::HTTP_BAD_REQUEST);
                }
            }

            return $this->json(['success' => true, 'amount' => $finalPrice]);
        } catch (InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->json(['error' => 'Payment failed: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
