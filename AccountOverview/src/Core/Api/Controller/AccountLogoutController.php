<?php declare(strict_types=1);

namespace AccountOverview\Core\Api\Controller;


use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Event\CustomerLogoutEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route(defaults={"_routeScope"={"store-api"}})
 * @deprecated tag:v6.5.0 - reason:becomes-internal - Will be internal
 */

class AccountLogoutController extends AbstractController
    {
    /**
     * @var SalesChannelContextPersister
     */
    private $contextPersister;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var SystemConfigService
     */
    private $systemConfig;

    /**
     * @var CartService
     */
    private $cartService;

    private EntityRepository $customerRepository;

    public function __construct(
        SalesChannelContextPersister $contextPersister,
        EventDispatcherInterface $eventDispatcher,
        SystemConfigService $systemConfig,
        CartService $cartService,
        EntityRepository $customerRepository
    ) {
        $this->contextPersister = $contextPersister;
        $this->eventDispatcher = $eventDispatcher;
        $this->systemConfig = $systemConfig;
        $this->cartService = $cartService;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Route ("store-api/account-overview/logout", name="api.account.overview.logout", methods={"POST"}, defaults={"_loginRequired"=true})
     * @param SalesChannelContext $context
     * @return JsonResponse
     */

    public function logout(SalesChannelContext $context, RequestDataBag $data): JsonResponse
    {
        /** @var CustomerEntity $customer */
        $customer = $context->getCustomer();
        if ($this->shouldDelete($context, $customer)) {
            $this->cartService->deleteCart($context);
            $this->contextPersister->delete($context->getToken(), $context->getSalesChannelId());
            $event = new CustomerLogoutEvent($context, $customer);
            $this->eventDispatcher->dispatch($event);
            return new JsonResponse([
                'type' => 'Forbidden',
                'status' => 403,
                'token' => $context->getToken(),
            ]);
        }
        $newToken = Random::getAlphanumericString(32);
        if ((bool) $data->get('replace-token')) {
            $newToken = $this->contextPersister->replace($context->getToken(), $context);
        }
        $context->assign([
            'token' => $newToken,
        ]);
        $event = new CustomerLogoutEvent($context, $customer);
        $this->eventDispatcher->dispatch($event);
        $responseStatus = ([
            'token' => $newToken,
        ]);
        return new JsonResponse([
            'type' => 'success',
            'status' => 200,
            'token' => $context->getToken(),
        ]);
    }

    private function shouldDelete(SalesChannelContext $context, $customer)
    {
        $config = $this->systemConfig->get('core.loginRegistration.invalidateSessionOnLogOut', $context->getSalesChannelId());
        if ($config) {
            return true;
        }
        if ($customer === null) {
            return true;
        }
        return $customer->getGuest();
    }
}
?>
