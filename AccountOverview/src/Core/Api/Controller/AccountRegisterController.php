<?php declare(strict_types=1);

namespace AccountOverview\Core\Api\Controller;


use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Checkout\Customer\SalesChannel\CustomerResponse;
use Shopware\Core\Content\Mail\Service\AbstractMailFactory;
use Shopware\Core\Content\Mail\Service\AbstractMailSender;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Monolog\Logger;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextServiceParameters;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Test\TestDefaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\NumberRange\ValueGenerator\NumberRangeValueGeneratorInterface;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Shopware\Core\System\SalesChannel\ContextTokenResponse;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextServiceInterface;



/**
 * @Route(defaults={"_routeScope"={"store-api"}})
 * @deprecated tag:v6.5.0 - reason:becomes-internal - Will be internal
 */

class AccountRegisterController extends AbstractController
{

    private AbstractMailFactory $mailFactory;
    private AbstractMailSender $mailSender;
    private EntityRepository $accountRegisterVerificationRepository;
    private EntityRepository $mailTemplateTypeRepository;
    private SystemConfigService $systemConfigService;
    private EntityRepository $logEntryRepository;
    private EntityRepository $categoryRepository;
    private EntityRepository $promotionRepository;
    private EntityRepository $customerRepository;
    private EntityRepository $customerGroupRepository;
    private EntityRepository $productRepository;
    private NumberRangeValueGeneratorInterface $numberRangeValueGenerator;
    private EntityRepository $customerExtensionRepository;
    private SalesChannelContextPersister $contextPersister;
    private SalesChannelContextServiceInterface $contextService;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        AbstractMailFactory $mailFactory,
        AbstractMailSender  $emailSender,
        EntityRepository    $accountRegisterVerificationRepository,
        EntityRepository    $mailTemplateTypeRepository,
        SystemConfigService $systemConfigService,
        EntityRepository    $logEntryRepository,
        EntityRepository    $categoryRepository,
        EntityRepository    $promotionRepository,
        EntityRepository    $customerRepository,
        EntityRepository    $customerGroupRepository,
        EntityRepository    $productRepository,
        NumberRangeValueGeneratorInterface $numberRangeValueGenerator,
        EntityRepository $customerExtensionRepository,
        SalesChannelContextPersister $contextPersister,
        SalesChannelContextServiceInterface $contextService,
        EventDispatcherInterface $eventDispatcher

    )
    {
        $this->mailFactory = $mailFactory;
        $this->mailSender = $emailSender;
        $this->accountRegisterVerificationRepository = $accountRegisterVerificationRepository;
        $this->mailTemplateTypeRepository = $mailTemplateTypeRepository;
        $this->systemConfigService = $systemConfigService;
        $this->logEntryRepository = $logEntryRepository;
        $this->categoryRepository = $categoryRepository;
        $this->promotionRepository = $promotionRepository;
        $this->customerRepository = $customerRepository;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->productRepository = $productRepository;
        $this->numberRangeValueGenerator = $numberRangeValueGenerator;
        $this->customerExtensionRepository = $customerExtensionRepository;
        $this->contextPersister = $contextPersister;
        $this->contextService = $contextService;
        $this->eventDispatcher = $eventDispatcher;
    }

    //------------ Get Register Details ---------------
    /**
     * @Route ("store-api/account-register/getRegisterData", name="api.account.register.register.data", methods={"POST"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function getRegisterData(RequestDataBag $data,Context $context): JsonResponse
    {
        $email = $data->get('email');
        $password = $data->get('password');
        $confirmPassword = $data->get('confirm_password');
        if($password !== $confirmPassword){
            return new JsonResponse([
                'type' => 'fail',
                'message'=>'password and confirm password not matched',
                'status' => 404,
            ],Response::HTTP_NOT_FOUND);
        }
        $accountFindCriteria = new Criteria();
        $accountFindCriteria->addFilter(new EqualsFilter('email', $email));
        $accountData = $this->accountRegisterVerificationRepository->search($accountFindCriteria, $context)->first();
        $OTP = random_int(10000, 99999);
        $this->mail($context,$OTP,$email,$password);

        if($accountData != null){
            $data = [];
            $data[] = [
                'id' => $accountData->getId(),
                'email' => $email,
                'password' => $password,
                'confirmPassword'=>$password,
                'otp' => $OTP
            ];
            $this->accountRegisterVerificationRepository->upsert($data, $context);
        }else{
            $data = [];
            $data[] = [
                'id' => Uuid::randomHex(),
                'email' => $email,
                'password' => $password,
                'confirmPassword'=>$password,
                'otp' => $OTP
            ];
            $this->accountRegisterVerificationRepository->create($data, $context);
        }
        return new JsonResponse([
            'type' => 'success',
            'status' => 200,
            'data' => 'OTP send successfully',
        ]);
    }

    /**
     * @Route ("store-api/account-register/mail", name="api.account.register.mail", methods={"POST"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function mail(Context $context,$OTP,$email): JsonResponse
    {
        //-------------  Mail Send Process  -------------
        $mailTemplateCriteria = new Criteria();
        $mailTemplateCriteria->addFilter(new EqualsFilter('technicalName', 'account_verification_opt'));
        $mailTemplateCriteria->addAssociation('mailTemplates');
        $mailTemplateCriteria->addAssociation('mailTemplates.translations');
        $mailTemplateType = $this->mailTemplateTypeRepository->search($mailTemplateCriteria, Context::createDefaultContext())->first();
        $mailTemplateTypeData = $mailTemplateType->mailTemplates->getElements();
        $mailTemplateTypeDataJson = json_decode(json_encode($mailTemplateTypeData), true);
        $newArray = [];
        foreach ($mailTemplateTypeDataJson as $value) {
            array_push($newArray, $value);
        }
        $mailPlainTextData = str_replace("{{ otp }}", $OTP, $newArray[0]['contentPlain']);
        $mailContent = [
            'subject' => $newArray[0]['subject'],
            'text/plain' => $mailPlainTextData,
        ];
        $binAttachmentsData = null;
        $dataSubject = $mailContent['subject'];
        $recipients = array($email => "");
        $dataArray = array("contentPlain" => $mailContent['text/plain']);
        $contents = $this->buildContents($dataArray);
        $mediaUrls = [];
        $data = array(
            "recipients" => $recipients,
            "senderName" => '',
            "salesChannelId" =>'',
            "templateId" => "",
            "customFields" => null,
            "contentHtml" => $contents['text/html'],
            "contentPlain" => $contents['text/plain'],
            "subject" => $dataSubject,
            "mediaIds" => [],
            "binAttachments" => $binAttachmentsData
        );
        $sender = "donotreply@localhost.com";
        $senderEmail = $this->systemConfigService->get('core.basicInformation.email') != "" ? $this->systemConfigService->get('core.basicInformation.email') : $sender;
        $senderWithName = [$senderEmail => ""];
        $binAttachments = null;
        $mail = $this->mailFactory->create(
            $dataSubject,
            $senderWithName,
            $recipients,
            $contents,
            $mediaUrls,
            $data,
            $binAttachments
        );

        // ------------- Mail Logs -------------
        $level = Logger::DEBUG;
        $message = mb_substr($contents['text/plain'], 0, 255);
        $logEntry = ['message' => mb_substr("Account verification Mail Send Successfully.", 0, 255),
            'level' => $level,
            'channel' => mb_substr('AccountRegister', 0, 255),
            'context' => ['source' => 'AccountRegister',
                'additionalData' => $message,
                'shopContext' => $context->getVars(),
            ],
        ];
        $this->logEntryRepository->create([$logEntry], $context);
        // ------------- End -------------
        $this->mailSender->send($mail);
        return new JsonResponse([
            'status' => 200,
            'type' => 'Mail Status',
            'message' => 'Mail Send Successfully'
        ]);
    }
    private function buildContents(array $data): array
    {
        $convertHtml = str_replace("\n", "<br/>", $data['contentPlain']);
        return [
            'text/html' => $convertHtml,
            'text/plain' => $data['contentPlain'],
        ];
    }

    //------------ Verify OTP Details ---------------
    /**
     * @Route ("store-api/account-verify/otp", name="api.account.verify.otp", methods={"POST"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function verifyOtp(RequestDataBag $request, Context $context): JsonResponse
    {
        //Getting user's email and otp details
        $email = $request->get('email');
        $opt = $request->get('otp');
        //verifying data with database
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('email', $email));
        $criteria->addFilter(new EqualsFilter('otp', $opt));
        $verificationResult = $this->accountRegisterVerificationRepository->search($criteria, $context);
        //Email and OTP Not Match With Database
        if (1 !== $verificationResult->count()) {
            $response = false;
            return new JsonResponse([
                'status' => 404,
                'type' => 'Fail',
            ]);
        }
        return new JsonResponse([
            'type' => 'success',
            'status' => 200,
            'data' => 'OTP verified',
        ]);

    }

    /**
     * @Route ("store-api/account-verify/forgotOtpMatch", name="api.account.verify.forgotOtpMatch", methods={"POST"})
     * @param Context $context
     * @return Response
     * @return JsonResponse
     */

    public function forgotOtpMatch(RequestDataBag $request, Context  $context): JsonResponse
    {
        //Getting user's email and otp details
        $opt = $request->get('otp');
        //verifying data with database
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('forgotOtp', $opt));
        $verificationResult = $this->accountRegisterVerificationRepository->search($criteria, $context);
        //Email and OTP Not Match With Database
        if (1 !== $verificationResult->count()) {
            $response = false;
            return new JsonResponse([
                'status' => 404,
                'type' => 'Fail',
            ]);
        }
        return new JsonResponse([
            'type' => 'success',
            'status' => 200,
            'data' => 'OTP verified',
        ]);
    }
    //-------------- Re-send OTP Details --------------
    /**
     * @Route ("store-api/account-resend/otp", name="api.account.resend.otp", methods={"POST"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function resendOtp(RequestDataBag $request, Context $context): JsonResponse
    {
        //getting email id
        $email = $request->get('email');
        //verifying email id with database
        $emailFindCriteria = new Criteria();
        $emailFindCriteria->addFilter(new EqualsFilter('email', $email));
        $emailData = $this->accountRegisterVerificationRepository->search($emailFindCriteria, $context)->first();

        //Checking for UUID exists or not?
        if (!empty($emailData)) {
            $OTP = random_int(10000, 99999);
            $data = [];
            $data[] = [
                'id' => $emailData->getId(),
                'email' => $email,
                'otp' => $OTP
            ];
            $this->mail($context,$OTP,$email);
        }else{
            return new JsonResponse([
                'type' => 'fail',
                'message'=>'this email address does not exist',
                'status' => 404,
            ],Response::HTTP_NOT_FOUND);
        }
        //Insert Or Update as per UUID exists or not exists
        $this->accountRegisterVerificationRepository->upsert($data, $context);
        return new JsonResponse([
            'type' => 'success',
            'status' => 200,
            'data' => "OTP successfully resent",]);

    }

    /**
     * @Route ("store-api/account-register/matchEmployeeCode", name="api.account.register.matchEmployeeCode", methods={"post"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function matchEmployeeCode(RequestDataBag $data,Context $context): JsonResponse
    {
        if ($data->get('emp_code') != null) {
            $emp = $data->get('emp_code');
            $promotionCriteria = new Criteria();
            $promotionCriteria->addFilter(new EqualsFilter('code', $emp));
            $promotionCriteria->addAssociation('discounts');
            $code = $this->promotionRepository->search($promotionCriteria, $context)->first();


            if (!empty($code)) {
                foreach ($code->getDiscounts()->getElements() as $value)
                {
                    $discount[] = [
                        'scope'=> $value->getScope(),
                        'type'=>$value->getType(),
                        'value'=>$value->getValue()
                    ];
                }

                return new JsonResponse([
                    'status' => 200,
                    'type' => 'Success',
                    'codeDiscount' => $discount
                ]);
            } else {
                return new JsonResponse([
                    'status' => 404,
                    'type' => 'Fail',
                ]);
            }
        } else {
            return new JsonResponse([
                'status' => 400,
                'type' => 'fail'
            ]);
        }
    }

    /**
     * @Route ("store-api/account-register/forgotpassword", name="api.account.register.forgotpassword", methods={"post"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function forgotPasswordSendMail(RequestDataBag $data,Context $context): JsonResponse
    {
        $email = $data->get('email');
        if ($email != null) {
            $customerCriteria = new Criteria();
            $customerCriteria->addFilter(new EqualsFilter('email', $email));
            $customer = $this->customerRepository->search($customerCriteria, $context)->first();

            if ($customer != null) {

                $otp = random_int(10000, 99999);
                $data = [];
                $data[] = [
                    'id' => $customer->getId(),
                    'email' => $email,
                    'password' => $otp,
                    'confirmPassword' => $otp,
                    'forgotOtp' => $otp
                ];
                $this->accountRegisterVerificationRepository->upsert($data, $context);
                //------------  Mail Send Process  -----------
                $mailTemplateCriteria = new Criteria();
                $mailTemplateCriteria->addFilter(new EqualsFilter('technicalName', 'forgot_password_opt'));
                $mailTemplateCriteria->addAssociation('mailTemplates');
                $mailTemplateCriteria->addAssociation('mailTemplates.translations');
                $mailTemplateType = $this->mailTemplateTypeRepository->search($mailTemplateCriteria, Context::createDefaultContext())->first();
                $mailTemplateTypeData = $mailTemplateType->mailTemplates->getElements();
                $mailTemplateTypeDataJson = json_decode(json_encode($mailTemplateTypeData), true);
                $newArray = [];
                foreach ($mailTemplateTypeDataJson as $value) {
                    array_push($newArray, $value);
                }
                $mailPlainTextData = str_replace("{{ otp }}", $otp, $newArray[0]['contentPlain']);
                $mailContent = [
                    'subject' => $newArray[0]['subject'],
                    'text/plain' => $mailPlainTextData,
                ];

                $binAttachmentsData = null;
                $dataSubject = $mailContent['subject'];
                $recipients = array($email => "");
                $dataArray = array("contentPlain" => $mailContent['text/plain']);
                $contents = $this->buildContents($dataArray);
                $mediaUrls = [];

                $data = array(
                    "recipients" => $recipients,
                    "senderName" => '',
                    "salesChannelId" => '',
                    "templateId" => "",
                    "customFields" => null,
                    "contentHtml" => $contents['text/html'],
                    "contentPlain" => $contents['text/plain'],
                    "subject" => $dataSubject,
                    "mediaIds" => [],
                    "binAttachments" => $binAttachmentsData
                );
                $sender = "doNotReply@localhost.com";
                $senderEmail = $this->systemConfigService->get('core.basicInformation.email') != "" ? $this->systemConfigService->get('core.basicInformation.email') : $sender;
                $senderWithName = [$senderEmail => ""];
                $binAttachments = null;
                $mail = $this->mailFactory->create(
                    $dataSubject,
                    $senderWithName,
                    $recipients,
                    $contents,
                    $mediaUrls,
                    $data,
                    $binAttachments
                );

                // ------------- Mail Logs -------------
                $level = Logger::DEBUG;
                $message = mb_substr($contents['text/plain'], 0, 255);
                $logEntry = ['message' => mb_substr("Forgot password Mail Send Successfully.", 0, 255),
                    'level' => $level,
                    'channel' => mb_substr('ForgotPassword', 0, 255),
                    'context' => ['source' => 'ForgotPassword',
                        'additionalData' => $message,
                        'shopContext' => $context->getVars(),
                    ],
                ];
                $this->logEntryRepository->create([$logEntry], $context);
                // ------------- End -------------

                $this->mailSender->send($mail);
                return new JsonResponse([
                    'status' => 200,
                    'type' => 'success',
                    'data' => 'We have sent you OTP on your email.',
                    'customer_id' => $customer->getId()
                ]);
            } else {
                return new JsonResponse([
                    'status' => 404,
                    'type' => 'fail'
                ]);
            }
        } else {
            return new JsonResponse([
                'status' => 400,
                'type' => 'fail',
            ]);
        }
    }

    /**
     * @Route ("store-api/account-register/resetPassword", name="api.account.register.register.resetPassword", methods={"POST"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function resetPassword(RequestDataBag $data, Context $context): JsonResponse
    {
//        $email = $data->get('email');
        $customerId = $data->get('customer_id');
        $password = $data->get('password');
        $confirmPassword = $data->get('confirm_password');
        $accountFindCriteria = new Criteria();
        $accountFindCriteria->addFilter(new EqualsFilter('id', $customerId));
        $accountData = $this->customerRepository->search($accountFindCriteria, $context)->first();
        if ($accountData != null) {
            if ($password == $confirmPassword) {
                $data = [];
                $data[] = [
                    'id' => $accountData->getId(),
                    'password' => $password
                ];

                $this->customerRepository->update($data, $context);
                return new JsonResponse([
                    'type' => 'success',
                    'status' => 200,
                    'data' => 'user password updated successfully.',
                    'customer_id' => $accountData->getId()
                ]);
            } else {
                return new JsonResponse([
                    'type' => 'fail',
                    'status' => 404,
                    'data' => 'Password and confirm Password dos not match.',
                ]);
            }

        }else{
            return new JsonResponse([
                'type' => 'fail',
                'status' => 400,
                'data' => 'User does not exist',
            ]);
        }

    }

    /**
     * @Route ("store-api/account-register/listCategory", name="api.account.register.listCategory ", methods={"post"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function listCategory(RequestDataBag $data,Context $context): JsonResponse
    {

        $criteria = new Criteria();
        $criteria->addAssociation('translations.name');
        $categories = $this->categoryRepository->search($criteria, $context)->getElements();
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $categoryList[] = [
                    'id' => $category->getId(),
                    'name' => $category->getTranslated()['name']
                ];
            }

            return new JsonResponse([
                'status' => 200,
                'type' => 'Success',
                'categories' => $categoryList
            ]);
        }else{
            return new JsonResponse([
                'status' => 400,
                'type' => 'fail',
            ]);
        }

    }

    /**
     * @Route ("store-api/account-register/getCategory", name="api.account.register.getCategory ", methods={"post"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function getCategory(RequestDataBag $data,Context $context): JsonResponse
    {

        $categoryId = $data->get('category_id');
        if (!empty($categoryId)) {
            return new JsonResponse([
                'status' => 200,
                'type' => 'success',
                'categoryIds' => $categoryId
            ]);

        } else {
            return new JsonResponse([
                'status' => 400,
                'type' => 'fail',
            ]);
        }

    }

    /**
     * @Route ("store-api/account-register/register", name="api.account.register.register", methods={"post"})
     * @param SalesChannelContext $context
     * @return Response
     * @throws \Exception
     */

    public function registrationComplete(SalesChannelContext $context,RequestDataBag $data): JsonResponse
    {
        $connection = $this->container->get(Connection::class);
        $payment = $connection->executeQuery('SELECT LOWER(HEX(id)) FROM `payment_method`')->fetchOne();
        $salutationId = $connection->executeQuery('SELECT LOWER(HEX(id)) FROM `salutation`')->fetchOne();
        $countryId = $connection->executeQuery('SELECT LOWER(HEX(id)) FROM `country`')->fetchOne();
        $updatedData = $data->getIterator();
        $dob = strtotime($updatedData['birthday']);
        $emailCriteria = new Criteria();
        $emailCriteria->addFilter(new EqualsFilter('email',$updatedData['email']));
        $emailCriteria->addAssociation('customerExtension');
        $mail = $this->customerRepository->search($emailCriteria,$context->getContext())->first();

        if($mail == null)
        {
            $addressId = Uuid::randomHex();
            $customerDataId = Uuid::randomHex();
            $customerData = [
                'id' => $customerDataId,
                'groupId' => TestDefaults::FALLBACK_CUSTOMER_GROUP,
                'defaultPaymentMethodId' => $payment,
                'salesChannelId' => $context->getSalesChannel()->getId(),
                'languageId' => $context->getLanguageId(),
                'defaultShippingAddress' =>[
                    'id' => $addressId,
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann',
                    'street' => 'Musterstra??e 1',
                    'city' => 'Sch??ppingen',
                    'zipcode' => '12345',
                    'salutationId' => $salutationId,
                    'countryId' => $countryId,
                ],
                'defaultBillingAddressId' => $addressId,
                'customerNumber' => $this->numberRangeValueGenerator->getValue(
                    $this->customerRepository->getDefinition()->getEntityName(),
                    $context->getContext(),
                    $context->getSalesChannel()->getId()
                ),
                'firstName' => $updatedData['name'],
                'lastName' => $updatedData['surname'],
                'password' => $updatedData['password'],
                'email' => $updatedData['email'],
                'birthday' => date('Y/m/d h:i:s', $dob),
                'customerExtension' => [
                    'address'=> $updatedData['address'],
                    'employeeCode'=> $updatedData['employee_code'],
                    'categoryId' => $updatedData['category_id'] ,
                    'mobileNumber' => $updatedData['mobile_number']

                ]
            ];
           $this->customerRepository->create([$customerData], $context->getContext());
            $criteria = new Criteria([$customerData['id']]);

            /** @var CustomerEntity $customerEntity */
            $customerEntity = $this->customerRepository->search($criteria, $context->getContext())->first();

            $response = new CustomerResponse($customerEntity);

            $newToken = $this->contextPersister->replace($context->getToken(), $context);

            $this->contextPersister->save(
                $newToken,
                [
                    'customerId' => $customerEntity->getId(),
                    'billingAddressId' => null,
                    'shippingAddressId' => null,
                    'domainId' => $context->getDomainId(),
                ],
                $context->getSalesChannel()->getId(),
                $customerEntity->getId()
            );

            $new = $this->contextService->get(
                new SalesChannelContextServiceParameters(
                    $context->getSalesChannel()->getId(),
                    $newToken,
                    $context->getLanguageId(),
                    $context->getCurrencyId(),
                    $context->getDomainId(),
                    $context->getContext(),
                    $customerEntity->getId()
                )
            );
        $new->addState(...$context->getStates());

            $event = new CustomerLoginEvent($new, $customerEntity, $newToken);
            $this->eventDispatcher->dispatch($event);

            $response->headers->set(PlatformRequest::HEADER_CONTEXT_TOKEN, $newToken);

            // We don't want to leak the hash in store-api
            $customerEntity->setHash('');

            return new JsonResponse([
                'status' => 200,
                'type' => 'success',
                'data' => 'Customer successfully register',
                'token' =>  $newToken,
                'customer_id' => $customerDataId
            ]);
        }else{
            return new JsonResponse([
                'status' => 403,
                'type' => 'fail',
                'data' => 'Customer already there please try other email address '
            ]);
        }
    }

    // ************************* Get Profile ***********************
    /**
     * @Route ("store-api/account-register/getCustomer", name="api.account.register.register.getCustomer", methods={"POST"})
     * @param SalesChannelContext $context
     * @return Response
     * @throws \Exception
     */

    public function getCustomer(RequestDataBag $data,Context $context): JsonResponse
    {
        $customerId = $data->get('customer_id');
        $customerCriteria = new Criteria();
        $customerCriteria->addFilter(new EqualsFilter('id',$customerId));
        $customer = $this->customerRepository->search($customerCriteria,$context)->first();
        $customerExtensionCriteria = new Criteria();
        $customerExtensionCriteria->addFilter(new EqualsFilter('customerId',$customerId));
        $customerExtension = $this->customerExtensionRepository->search($customerExtensionCriteria,$context)->first();

        if($customer != null)
        {
            $customerData = [
                'customer_id' => $customer->getId(),
                'name' => $customer->getfirstName(),
                'surname' => $customer->getlastName(),
                'email' => $customer->getemail(),
                'birthday' => $customer->getbirthday()->format("d M Y"),
                'address'=> $customerExtension->getaddress(),
                'mobile_number' => $customerExtension->getmobileNumber(),
                'employee_code' => $customerExtension->getemployeeCode(),
                'category_id' => $customerExtension->getcategoryId(),
            ];


            return new JsonResponse([
                'status' => 200,
                'type' => 'success',
                'data' => $customerData
            ]);
        }else {
            return new JsonResponse([
                'status' => 403,
                'type' => 'fail',
            ]);
        }
    }


    // ************************* Update Profile ***********************
    /**
     * @Route ("store-api/account-register/updateProfile", name="api.account.register.register.updateProfile", methods={"POST"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function profileUpdate(RequestDataBag $data,Context $context): JsonResponse
    {

        $updatedData = $data->getIterator();
        $dob = strtotime($updatedData['birthday']);
        $emailCriteria = new Criteria();
        $emailCriteria->addFilter(new EqualsFilter('email',$updatedData['email']));
        $emailCriteria->addAssociation('customerExtension');
        $customer = $this->customerRepository->search($emailCriteria,$context)->first();
        if($customer != null)
        {
            $customerData[] = [
                'id' => $customer->getId(),
                'firstName' => $updatedData['name'],
                'lastName' => $updatedData['surname'],
                'password' => $updatedData['password'],
                'email' => $updatedData['email'],
                'birthday' => date('Y/m/d h:i:s', $dob),
                'customerExtension' => [
                    'id'=>$customer->getExtension('customerExtension')->getId(),
                    'address'=> $updatedData['address'],
                    'mobileNumber' => $updatedData['mobile_number'],
                ]
            ];

            $this->customerRepository->update($customerData, $context);

            return new JsonResponse([
                'status' => 200,
                'type' => 'success',
            ]);
        }else {
            return new JsonResponse([
                'status' => 403,
                'type' => 'fail',
            ]);
        }
    }

// ************************* Delete Customer ***********************
    /**
     * @Route ("store-api/account-register/deleteCustomer", name="api.account.register.register.deleteCustomer", methods={"POST"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function deleteCustomer(RequestDataBag $data,Context $context):JsonResponse
    {
        $customerId = $data->get('customer_id');
        $password = $data->get('password');
        $confirmPassword = $data->get('confirm_password');
        $criteria = new Criteria();
        $criteria->addAssociation('customerExtension');
        $criteria->addFilter(new EqualsFilter('id', $customerId));
        $customer = $this->customerRepository->search($criteria, $context)->first();

        if($password == $confirmPassword && $customer != null) {
            $this->customerExtensionRepository->delete([
                [
                    'id' => $customer->getExtension('customerExtension')->getId()
                ]
            ], $context);
            $this->customerRepository->delete([
                [
                    'id' => $customer->getId()
                ]
            ], $context);

            return new JsonResponse([
                'status' => 200,
                'type' => 'success',
            ]);
        }else{
            return new JsonResponse([
                'status' => 403,
                'type' => 'fail',
            ]);
        }
    }
// ************************* Update Employee Code ***********************
    /**
     * @Route ("store-api/account-register/updateEmployeeCode", name="api.account.register.employeecode", methods={"post"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

    public function updateEmployeeCode(RequestDataBag $data,Context $context): JsonResponse
    {
        $emp = $data->get('emp_code');
        $customerId = $data->get('customer_id');
        $promotionCriteria = new Criteria();
        $promotionCriteria->addFilter(new EqualsFilter('code', $emp));
        $promotionCriteria->addAssociation('discounts');
        $code = $this->promotionRepository->search($promotionCriteria, $context)->first();
        $emailCriteria = new Criteria();
        $emailCriteria->addFilter(new EqualsFilter('id',$customerId));
        $emailCriteria->addAssociation('customerExtension');
        $customer = $this->customerRepository->search($emailCriteria,$context)->first();
        if (!empty($code) && !empty($customer)) {
            $customerdata[] =[
                'id' => $customerId,
                'customerExtension' => [
                    'id'=>$customer->getExtension('customerExtension')->getId(),
                    'employeeCode' => $emp

                ]
            ];
            $this->customerRepository->update($customerdata,$context);
            foreach ($code->getDiscounts()->getElements() as $value)
            {
                $discount[] = [
                    'scope'=> $value->getScope(),
                    'type'=>$value->getType(),
                    'value'=>$value->getValue()
                ];
            }

            return new JsonResponse([
                'status' => 200,
                'type' => 'Success',
                'codeDiscount' => $discount,
                'customerId' => $customerId
            ]);
        } else {
            return new JsonResponse([
                'status' => 404,
                'type' => 'Fail',
            ]);
        }

    }


// ************************* Update Category ***********************

    /**
     * @Route ("store-api/account-register/updateCategory", name="api.account.register.updateCategory", methods={"post"})
     * @param RequestDataBag $data
     * @param Context $context
     * @return JsonResponse
     * @throws \Exception
     */

    public function updateCategory(RequestDataBag $data,Context $context): JsonResponse
    {
        $categoryId = $data->get('category_id');
        $customerId = $data->get('customer_id');

        $customerCriteria = new Criteria();
        $customerCriteria->addFilter(new EqualsFilter('id',$customerId));
        $customerCriteria->addAssociation('customerExtension');
        $customer = $this->customerRepository->search($customerCriteria,$context)->first();
        if (!empty($categoryId) && !empty($customerId)) {
            $customerdata[] =[
                'id' => $customerId,
                'customerExtension' => [
                    'id'=>$customer->getExtension('customerExtension')->getId(),
                    'categoryId' => $categoryId

                ]
            ];
            $this->customerRepository->update($customerdata,$context);
            return new JsonResponse([
                'status' => 200,
                'type' => 'success'
            ]);

        } else {
            return new JsonResponse([
                'status' => 400,
                'type' => 'fail',
            ]);
        }
    }

    /**
     * @Route ("store-api/account-register/removeEmployeeCode", name="api.account.register.category", methods={"post"})
     * @param Context $context
     * @return Response
     * @throws \Exception
     */

// ************************* Remove Employee code ***********************
    public function removeEmployeeCode(RequestDataBag  $data,Context $context):JsonResponse
    {
        $customerId = $data->get('customer_id');
        $customerCriteria = new Criteria();
        $customerCriteria->addFilter(new EqualsFilter('id',$customerId));
        $customerCriteria->addAssociation('customerExtension');
        $customer = $this->customerRepository->search($customerCriteria,$context)->first();
        if(!empty($customer))
        {
            $customerdata[] =[
                'id' => $customerId,
                'customerExtension' => [
                    'id'=>$customer->getExtension('customerExtension')->getId(),
                    'employeeCode' => ''

                ]
            ];
            $this->customerRepository->update($customerdata,$context);

            return new JsonResponse([
                'status' => 200,
                'type' => 'success'
            ]);
        }else {
            return new JsonResponse([
                'status' => 400,
                'type' => 'fail',
            ]);
        }
    }

}
