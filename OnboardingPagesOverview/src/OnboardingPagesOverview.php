<?php declare(strict_types=1);

namespace OnboardingPagesOverview;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Uuid\Uuid;

class OnboardingPagesOverview extends Plugin
{
    public array $pagesArr = [
        'Onboarding 01' => 'onboarding-01',
        'Onboarding 02' => 'onboarding-02',
        'Onboarding 03' => 'onboarding-03',
        'Terms And Conditions' => 'terms-and-conditions',
        'Why We Need This?' => 'why-we-need-this',
        'Data Privacy'=> 'data-privacy'
        ];

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext); // TODO: Change the autogenerated stub
    }
    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext); // TODO: Change the autogenerated stub

        /**
         * @var EntityRepositoryInterface $cmsPagesRepository
         */
        $cmsPagesRepository = $this->container->get('cms_pages_overview.repository');

          foreach ($this->pagesArr as $page => $slug){
              $pageId = Uuid::randomHex();
              $pageData[] = [
                  'id'=>$pageId,
                  'name'=>$page,
                  'slug'=>$slug
              ];
          }
        $cmsPagesRepository->create($pageData, $activateContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ( $uninstallContext -> keepUserData () ) {
            return;
        }
        parent::uninstall($uninstallContext); // TODO: Change the autogenerated stub
        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `cms_pages_detail_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `cms_pages_detail`');
        $connection->executeStatement('DROP TABLE IF EXISTS `cms_pages_overview_translation`');
        $connection->executeStatement('DROP TABLE IF EXISTS `cms_pages_overview`');
    }
}
