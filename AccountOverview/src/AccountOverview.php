<?php declare(strict_types=1);

namespace AccountOverview;

use AccountOverview\Util\Lifecycle\EmailTemplate;
use AccountOverview\Util\ForgotPassword\ForgotPasswordEmailTemplate;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class AccountOverview extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);

        $this->getMailTemplate()->installMailTemplate($installContext->getContext());
        $this->getForgotPasswordMailTemplate()->installForgotPasswordMailTemplate($installContext->getContext());

    }

    public function uninstall(UninstallContext $uninstallContext): void
    {

        parent::uninstall($uninstallContext);
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->getMailTemplate()->UnInstallMailTemplate($uninstallContext->getContext());
        $this->getForgotPasswordMailTemplate()->UnInstallForgotPasswordMailTemplate($uninstallContext->getContext());

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `account_register_verification`');
        $connection->executeStatement('DROP TABLE IF EXISTS `account_register_customer`');
         $connection->executeStatement('DROP TABLE IF EXISTS `customer_extension`');

    }

    private function getMailTemplate(): EmailTemplate
    {
        /* @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        /* @var EntityRepository $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        return new EmailTemplate(
            $mailTemplateTypeRepository,
            $mailTemplateRepository
        );
    }
    private function getForgotPasswordMailTemplate(): ForgotPasswordEmailTemplate
    {
        /* @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        /* @var EntityRepository $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        return new ForgotPasswordEmailTemplate(
            $mailTemplateTypeRepository,
            $mailTemplateRepository
        );
    }

}
