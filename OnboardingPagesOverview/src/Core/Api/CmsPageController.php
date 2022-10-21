<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Api;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route(defaults={"_routeScope"={"store-api"}})
 * @deprecated tag:v6.5.0 - reason:becomes-internal - Will be internal
 */

class CmsPageController extends AbstractController{

/**
 * @var EntityRepositoryInterface
 */
    private $cmsPagesRepository;

    public function __construct(EntityRepositoryInterface $cmsPagesRepository)
    {
        $this->cmsPagesRepository = $cmsPagesRepository;
    }

    /**
     * @Route ("store-api/onboarding-data/getOnboarding", name="api.onboarding.data.getOnboarding", methods={"GET"})
     * @param RequestDataBag $request
     * @param SalesChannelContext $context
     * @return JsonResponse
     */

    public function getOnboarding(Context $context): JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addAssociation('cmsPagesOverview.pageId');
        $criteria->addFilter(new PrefixFilter('cmsPagesOverview.name','Onboarding'));
        $criteria->addSorting(new FieldSorting('cmsPagesOverview.name', FieldSorting::ASCENDING));

        $pageData = $this->cmsPagesRepository->search($criteria, $context)->getElements();

        if($pageData == null){
            return new JsonResponse([
                'type' => 'fail',
                'status' => 404,
                'data' => $pageData,
            ]);
        }else {
            foreach($pageData as $data){

                $filterData[] = [
                    'id'=> $data->id,
                    'page'=>$data->pageId,
                    'title'=>$data->title,
                    'html'=>$data->description,
                    'image'=>$data->media->url
                ];
            }
            return new JsonResponse([
                'type' => 'success',
                'status' => 200,
                'data' => $filterData,
            ]);
        }
    }

    /**
     * @Route ("store-api/cms-page-data/cmsPageData/{slug}", name="api.custom.cms_page_data.cmsPageData", methods={"GET"})
     * @param RequestDataBag $request
     * @param SalesChannelContext $context
     * @return JsonResponse
     */
    public function cmsPageData(string $slug, Context $context) : JsonResponse
    {
        $criteria = new Criteria();
        $criteria->addAssociation('cmsPagesOverview.pageId');

        $criteria->addFilter(
            new NotFilter(
                NotFilter::CONNECTION_OR,
                [
                    new PrefixFilter('cmsPagesOverview.name','Onboarding')

                ]
            )
        );

        $pageData = $this->cmsPagesRepository->search($criteria, $context)->getElements();

        if($pageData == null){
            return new JsonResponse([
                'type' => 'fail',
                'status' => 404,
                'data' => $pageData,
            ]);
        }else {
            foreach($pageData as $data){
                if($data->cmsPagesOverview->slug === $slug){
                    $filterData[] = [
                        'title'=>$data->title,
                        'html'=>$data->description,
                    ];
                }
            }
            return new JsonResponse([
                'type' => 'success',
                'status' => 200,
                'data' => $filterData,
            ]);
        }
    }

}
?>
