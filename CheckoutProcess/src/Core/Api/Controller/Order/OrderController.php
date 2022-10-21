<?php declare(strict_types=1);

namespace CheckoutProcess\Core\Api\Controller\Order;

use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\NumberRange\ValueGenerator\NumberRangeValueGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_routeScope"={"store-api"}})
 * @deprecated tag:v6.5.0 - reason:becomes-internal - Will be internal
 */
class OrderController extends AbstractController
{
    private NumberRangeValueGeneratorInterface $numberRangeValueGenerator;
    private EntityRepository $orderRepository;
    private EntityRepository $customerRepository;

    public function __construct
    (
        NumberRangeValueGeneratorInterface $numberRangeValueGenerator,
        EntityRepository $orderRepository,
        EntityRepository $customerRepository
    )
    {
        $this->numberRangeValueGenerator = $numberRangeValueGenerator;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Route ("store-api/checkout/createOrder", name="api.checkout.createOrder", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */

    public function createOrder(Context $context, Request $request): JsonResponse
    {
        $customerId =$request->get('customerId');
        $customerEmail = $request->get('customerEmail');
        $customerSaluationId = $request->get('customerSaluationId');
        $customerFirstName = $request->get('customerFirstName');
        $customerLastName = $request->get('customerLastName');
        $customerNumber = $request->get('customerNumber');
        $productDetails = $request->get("productDetails");
        $amountTotal = floatval($request->get('amountTotal'));
        $amountNet = floatval($request->get('amountNet'));
        $positionPrice = floatval($request->get('positionPrice'));
        $unitPrice = floatval($request->get('unitPrice'));
        $paymentMethodId = $request->get('paymentMethodId');
        $lineItems = [];
        $identifier = Uuid::randomHex();
        foreach ($productDetails as $details)
        {
            $lineItemAmountTotal = floatval($details['lineItemAmountTotal']);
            $lineItemUnitPrice = floatval($details['lineItemUnitPrice']);
            $lineItemQuantity = intval($details['lineItemQuantity']);
            $lineItems[] =
                [
                    'productId' => $details['productId'],
                    'identifier' => $details['productId'],
                    'referencedId' => $details['productId'],
                    'coverId'=>$details['coverId'],
                    'quantity' => $lineItemQuantity,
                    'label' => $details['productName'],
                    'type' => LineItem::PRODUCT_LINE_ITEM_TYPE,
                    'payload'=>[
                        'productNumber'=>$details['productNumber'],
                        'productId'=> $details['productId']
                    ],
                    'price'=>new CalculatedPrice($lineItemUnitPrice, $lineItemAmountTotal, new CalculatedTaxCollection(), new TaxRuleCollection()),
                    'priceDefinition' => new QuantityPriceDefinition($lineItemAmountTotal, new TaxRuleCollection(), $lineItemQuantity),
                ];

        }
        if
        (
            !empty($customerId) &&
            !empty($productDetails)&&
            !empty($amountTotal)&&
            !empty($amountNet)&&
            !empty($positionPrice)&&
            !empty($paymentMethodId)
        ) {

            $orderStateId = '412a1c2dc0ea4a6c853ef2acf0cf5d5e';
            $transactionStateId = '39c3eb4ba0534feab0954d488833a55b';
            $cusomerCriteria = new Criteria();
            $cusomerCriteria->addFilter(new EqualsFilter('id', $customerId));
            $customer = $this->customerRepository->search($cusomerCriteria, $context)->first();
            $data[] = [
                'orderNumber' => $this->numberRangeValueGenerator->getValue(
                    $this->orderRepository->getDefinition()->getEntityName(),
                    $context,
                    $context->getSource()->getSalesChannelId()
                ),
                'billingAddressId' => $customer->getDefaultBillingAddressId(),
                'currencyId' => $context->getCurrencyId(),
                'languageId' => $context->getLanguageId(),
                'salesChannelId' => $context->getSource()->getSalesChannelId(),
                'orderDateTime' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'currencyFactor' => 1.0,
                'stateId' => $orderStateId,
                'price' => new CartPrice($amountNet, $amountTotal, $positionPrice, new CalculatedTaxCollection(), new TaxRuleCollection(), CartPrice::TAX_STATE_GROSS),
                'shippingCosts' => new CalculatedPrice($unitPrice, $amountTotal, new CalculatedTaxCollection(), new TaxRuleCollection()),
                'ruleIds' => [Uuid::randomHex()],
                'orderCustomer'=>[
                    'customerId' => $customerId,
                    'customerNumber'=>$customerNumber,
                    'email' => $customerEmail,
                    'salutationId' => $customerSaluationId,
                    'firstName' => $customerFirstName,
                    'lastName' => $customerLastName,
                ],
                'lineItems' => $lineItems,
                'transactions' => [
                    [
                        'paymentMethodId' => $paymentMethodId,
                        'stateId' => $transactionStateId,
                        'amount' => new CalculatedPrice($unitPrice, $amountTotal, new CalculatedTaxCollection(), new TaxRuleCollection()),
                    ],
                ],
            ];
            $this->orderRepository->create($data, $context);
            return new JsonResponse([
                'status' => 200,
                'type' => 'success'
            ]);
        }else{
            return new JsonResponse([
                'status' => 404,
                'type' => 'fail'
            ]);
        }
    }
    /**
     * @Route ("store-api/checkout/getOrderCustomerVise", name="api.checkout.getOrderCustomerVise", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function getOrderCustomerVise(Context $context, Request $request):JsonResponse
    {
        $customerId = $request->get("customerId");
        if(!empty($customerId)) {
            $orderCriteria = new Criteria();
            $orderCriteria->addAssociation('orderCustomer');
            $orderCriteria->addAssociation('transactions');
            $orderCriteria->addAssociation('stateMachineState');
            $orderCriteria->addFilter(new EqualsFilter('orderCustomer.customerId', $customerId));
            $orders = $this->orderRepository->search($orderCriteria, $context)->getElements();

            if ($orders != Null) {
                foreach ($orders as $order) {
                    $data[] = [
                        'id' => $order->getId(),
                        'orderNumber' => $order->getOrderNumber(),
                        'price' => $order->getPrice(),
                        'orderDate' => $order->getOrderDate(),
                        'totalAmount' => $order->getAmountTotal(),
                        'customerEmail' => $order->getOrderCustomer()->getEmail(),
                        'customerFirstName' => $order->getOrderCustomer()->getFirstName(),
                        'customerLastName' => $order->getOrderCustomer()->getLastName(),
                        'payment' => $order->getStateMachineState()->getName()
                    ];
                }

                return new JsonResponse([
                    'status' => 200,
                    'type' => 'success',
                    'orders' => $data
                ]);
            } else {
                return new JsonResponse([
                    'status' => 400,
                    'type' => 'fail'
                ]);
            }
        }else{
            return new JsonResponse([
                'status' => 400,
                'type' => 'fail'
            ]);
        }


    }
}

?>
