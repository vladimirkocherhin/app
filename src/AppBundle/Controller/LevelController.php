<?php


namespace AppBundle\Controller;


use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\QueryType\QueryTypeRegistry;
use  eZ\Publish\API\Repository\Values\Content\Query;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\SearchService;

class LevelController extends Controller
{


    public function fullViewAction(Request $request, ContentView $view)
    {
        $query = new LocationQuery();

        $query->filter = new Criterion\LogicalAnd([
            new Criterion\ContentTypeIdentifier('topic'),
            new Criterion\ParentLocationId(55),
            new Criterion\Visibility(Criterion\Visibility::VISIBLE),
        ]);
//        $query->sortClauses = [
//            new SortClause\Location\Path(Query::SORT_ASC),
//        ];

        $results  = $this->container->get('ezpublish.api.service.search')->findLocations($query);

        $items = [];
        foreach ($results->searchHits as $searchHit) {
            $items[] = [
                'id' => $searchHit->valueObject->contentInfo->mainLocationId,
                'name' => $searchHit->valueObject->content->fields['name']['eng-GB']->text,
            ];
        }


        $response = new JsonResponse();
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

        $response->setData($items);

        return $response;
    }
}