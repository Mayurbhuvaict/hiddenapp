<?php declare(strict_types=1);


namespace CheckoutProcess\Core\Api\Controller;

use OpenApi\Annotations as OA;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartCalculator;
use Shopware\Core\Checkout\Cart\CartPersister;
use Shopware\Core\Checkout\Cart\CartPersisterInterface;
use Shopware\Core\Checkout\Cart\Event\CartCreatedEvent;
use Shopware\Core\Checkout\Cart\Exception\CartTokenNotFoundException;
use Shopware\Core\Checkout\Cart\SalesChannel\AbstractCartLoadRoute;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Shopware\Core\Checkout\Cart\SalesChannel\CartResponse;


/**
 * @Route(defaults={"_routeScope"={"store-api"}})
 */
class CartController extends AbstractCartLoadRoute
{
    private CartPersister $persister;

    private EventDispatcherInterface $eventDispatcher;

    private CartCalculator $cartCalculator;

    /**
     * @internal
     */
    public function __construct(
        CartPersister $persister,
        EventDispatcherInterface $eventDispatcher,
        CartCalculator $cartCalculator
    ) {
        $this->persister = $persister;
        $this->eventDispatcher = $eventDispatcher;
        $this->cartCalculator = $cartCalculator;
    }

    public function getDecorated(): AbstractCartLoadRoute
    {
        throw new DecorationPatternException(self::class);
    }

    /**
     * @Since("6.3.0.0")
     * @OA\Get(
     *      path="/checkout/cart",
     *      summary="Fetch or create a cart",
     *      description="Used to fetch the current cart or for creating a new one.",
     *      operationId="readCart",
     *      tags={"Store API", "Cart"},
     *      @OA\Parameter(
     *          name="name",
     *          description="The name of the new cart. This parameter will only be used when creating a new cart.",
     *          @OA\Schema(type="string"),
     *          in="query",
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Cart",
     *          @OA\JsonContent(ref="#/components/schemas/Cart")
     *     )
     * )
     * @Route("/store-api/checkout-process/cart", name="store-api.checkout.process.cart.read", methods={"GET", "POST"})
     *  @param Request $request
     * @param SalesChannelContext $context
     * @return CartResponse
     */
    public function load(Request $request, SalesChannelContext $context): CartResponse
    {
        $name = $request->get('name', CartService::SALES_CHANNEL);
        $token = $request->get('token', $context->getToken());
        try {
            $cart = $this->persister->load($token, $context);
        } catch (CartTokenNotFoundException $e) {
            $cart = $this->createNew($token, $name);
        }
        $cartItems = $this->cartCalculator->calculate($cart, $context);
        if ($this->cartCalculator->calculate($cart, $context)->getLineItems()->getElements() != null){
            $cartItems->status=200;
            $cartItems->type="success";
            return new CartResponse($cartItems);
        }else{
            $cartItems->status=404;
            $cartItems->type="fail";
            return new CartResponse($cartItems);
        }


    }

    private function createNew(string $token, string $name): Cart
    {
        $cart = new Cart($name, $token);

        $this->eventDispatcher->dispatch(new CartCreatedEvent($cart));

        return $cart;
    }
}
