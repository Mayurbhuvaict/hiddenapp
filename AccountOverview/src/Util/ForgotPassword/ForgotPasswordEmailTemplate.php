<?php declare(strict_types=1);

namespace AccountOverview\Util\ForgotPassword;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class ForgotPasswordEmailTemplate
{
    public const TEMPLATE_TYPE_NAME = 'Forgot Password OTP';
    public const TEMPLATE_TYPE_NAME_DE = 'Passwort vergessen OTP';

    public const TEMPLATE_TYPE_TECHNICAL_NAME = 'forgot_password_opt';

    public const TEMPLATE_DESCRIPTION = 'Sending otp to customer';
    public const TEMPLATE_DESCRIPTION_DE = 'Senden von otp an den Kunden';

    public const SUBJECT_ENG = 'Forgot Password OTP';
    public const SUBJECT_DE = 'Passwort vergessen OTP';

    public const CONTENT_ENG = "Hello User,\n\n Your request for forgot passwod approve and your OTP is : {{ otp }}\n Thanks.";
    public const CONTENT_DE = "Hallo Benutzer,\n\n Ihre Anfrage für vergessenes Passwort genehmigen und Ihr OTP ist: {{ otp }}\n Vielen Dank.";

    public const CONTENT_HTML_EN = "Hello User,<br/><br/> Your request for forgot passwod approve and your OTP is : {{ otp }}<br/> Thanks.";
    public const CONTENT_HTML_DE = "Hallo Benutzer,<br/><br/> Ihre Anfrage für vergessenes Passwort genehmigen und Ihr OTP ist : {{ otp }}<br/> Vielen Dank.";
    private EntityRepository $mailTemplateTypeRepository;
    private EntityRepository $mailTemplateRepository;

    public function __construct(
        EntityRepository $mailTemplateTypeRepository,
        EntityRepository $mailTemplateRepository
    )
    {
        $this->mailTemplateTypeRepository = $mailTemplateTypeRepository;
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    public function installForgotPasswordMailTemplate(Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('technicalName', self::TEMPLATE_TYPE_TECHNICAL_NAME));
        $mailTemplateExist = $this->mailTemplateTypeRepository->search($criteria, $context)->getTotal();

        if ($mailTemplateExist == 0) {
            $this->createdeleteForgotPasswordMailTemplateMailTemplateContent($context);
        }
    }

    public function UnInstallForgotPasswordMailTemplate(Context $context): void
    {
        $this->deleteForgotPasswordMailTemplate($context);
    }



    private function createdeleteForgotPasswordMailTemplateMailTemplateContent(Context $context)
    {
        $mailTemplateTypeId = Uuid::randomHex();
        $mailTemplateId = Uuid::randomHex();
        $mailTemplateType = [
            [
                'id' => $mailTemplateTypeId,
                'name' => [
                    'en-GB' => self::TEMPLATE_TYPE_NAME,
                    'de-DE' => self::TEMPLATE_TYPE_NAME_DE
                ],
                'technicalName' => self::TEMPLATE_TYPE_TECHNICAL_NAME,
                'availableEntities' => [
                    'esmxUpsLabel' => 'forgot_password',
                    'salesChannel' => 'sales_channel'
                ]
            ]
        ];

        $mailTemplate = [
            [
                'id' => $mailTemplateId,
                'mailTemplateTypeId' => $mailTemplateTypeId,
                'senderName' => [
                    'en-GB' => '{{ salesChannel.name }}',
                    'de-DE' => '{{ salesChannel.name }}'
                ],
                'description' => [
                    'en-GB' => self::TEMPLATE_DESCRIPTION,
                    'de-DE' => self::TEMPLATE_DESCRIPTION_DE
                ],
                'subject' => [
                    'en-GB' => self::SUBJECT_ENG,
                    'de-DE' => self::SUBJECT_DE
                ],
                'contentPlain' => [
                    'en-GB' => self::CONTENT_ENG,
                    'de-DE' => self::CONTENT_DE
                ],
                'contentHtml' => [
                    'en-GB' => self::CONTENT_HTML_EN,
                    'de-DE' => self::CONTENT_HTML_DE
                ]
            ]
        ];

        $this->mailTemplateTypeRepository->create($mailTemplateType, $context);
        $this->mailTemplateRepository->create($mailTemplate, $context);
    }

    private function deleteForgotPasswordMailTemplate(Context $context)
    {
        $myCustomMailTemplateType = $this->mailTemplateTypeRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('technicalName', self::TEMPLATE_TYPE_TECHNICAL_NAME)),
            $context)->first();

        $mailTemplateId = $this->mailTemplateRepository->search(
            (new Criteria())
                ->addFilter(new EqualsFilter('mailTemplateTypeId', $myCustomMailTemplateType->getId())),
            $context)->first();

        $this->mailTemplateRepository->delete([
            ['id' => $mailTemplateId->getId()]
        ], $context);

        $this->mailTemplateTypeRepository->delete([
            ['id' => $myCustomMailTemplateType->getId()]
        ], $context);
    }
}

