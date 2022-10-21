<?php declare(strict_types=1);

namespace CheckoutProcess\Core\Api\Controller\Payment;

use Psr\Log\NullLogger;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"store-api"}})
 * @deprecated tag:v6.5.0 - reason:becomes-internal - Will be internal
 */
class PaymentController extends AbstractController
{
    private EntityRepositoryInterface $paymentMethodRepository;
    private SystemConfigService $systemConfigService;

    public function __construct
    (
        EntityRepositoryInterface $paymentMethodRepository,
        SystemConfigService $systemConfigService
    )
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @Route ("store-api/checkout/getPaymentMethods", name="api.checkout.getPaymentMethods", methods={"GET"})
     * @param Context $context
     * @return JsonResponse
     * @throws \Exception
     */
    public function getPaymentMethods(Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addAssociation('translations');
        $allPaymentMethod = $this->paymentMethodRepository->search($criteria,$context)->getElements();
        $data = [];
        foreach ($allPaymentMethod as $methods)
        {
            if ($methods->getActive() == 'true')
            {
                $data[] = [
                    'id'=>$methods->getId(),
                    'name'=>$methods->getName()
                ];
            }

        }
        if ($data != null){
            return new JsonResponse([
                'status'=>200,
                'type' => 'success',
                'payment_method'=>$data
            ]);
        }else {
            return new JsonResponse([
                'status' => 404,
                'type' => 'fail'
            ]);
        }



    }

    /**
     * @Route ("store-api/checkout/payPalDetails", name="api.checkout.payPalDetails", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */

    public function payPalDetails(Request $request): JsonResponse
    {
        $paymentMethodId = $request->get('payment_method_id');
        $paymentMethod = $request->get('payment_method');
        $orderId = $request->get('order_id');
        if($paymentMethod == 'PayPal')
        {
            $clientId = $this->systemConfigService->get('SwagPayPal.settings.clientId');
            $clientSecretSandbox = $this->systemConfigService->get('SwagPayPal.settings.clientSecretSandbox');
            $clientIdSandbox = $this->systemConfigService->get('SwagPayPal.settings.clientIdSandbox');
            $merchantPayerIdSandbox = $this->systemConfigService->get('SwagPayPal.settings.merchantPayerIdSandbox');
            $merchantPayerId = $this->systemConfigService->get('SwagPayPal.settings.merchantPayerId');
            $clientSecret = $this->systemConfigService->get('SwagPayPal.settings.clientSecret');
            $data = [
                'payment_method_id' => $paymentMethodId ,
                'orderId' => $orderId ,
                'paymentMethod' => $paymentMethod ,
                'clientId' => $clientId ,
                'clientSecretSandbox' => $clientSecretSandbox ,
                'clientIdSandbox' => $clientIdSandbox ,
                'merchantPayerIdSandbox' => $merchantPayerIdSandbox ,
                'merchantPayerId' => $merchantPayerId ,
                'clientSecret' => $clientSecret
            ];
            return new JsonResponse([
                'status'=>200,
                'type' => 'success',
                'payment_details'=>$data
            ]);
        }else{
            return new JsonResponse([
                'status'=>400,
                'type' => 'fail',
            ]);
        }

    }



}

