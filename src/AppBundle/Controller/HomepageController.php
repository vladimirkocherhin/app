<?php


namespace AppBundle\Controller;


use eZ\Bundle\EzPublishCoreBundle\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;


class HomepageController extends Controller
{
    private $contentService;

    private $locationService;

    public function __construct(ContentService $contentService, LocationService $locationService)
    {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function fullViewAction(Request $request)
    {
        $rootLocationId = $this->getConfigResolver()->getParameter('content.tree_root.location_id');
        $rootLocation = $this->locationService->loadLocation($rootLocationId);
        dump($rootLocation); die();

        $response = new JsonResponse();
        $response->setData(['data' => 123]);

        return $response;
    }
}