<?php


namespace AppBundle\Controller;


use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

abstract class AbstractController extends Controller
{
    /**
     * @param $result
     * @param string $name
     * @param string $language
     * @return mixed
     */
    public function getFieldByLanguage($result, string $name, string $language)
    {
        try {
            return $result->valueObject->content->fields[$name][$language]->text;
        } catch (\Exception $e){
            return $result->valueObject->content->fields[$name]["eng-GB"]->text;
        }
    }

    public function getLocations(int $parentLocation, string $contentTypeId)
    {
        $repository = $this->get( 'ezpublish.api.repository' );
        $searchService = $repository->getSearchService();
        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\ParentLocationId($parentLocation),
            new Criterion\ContentTypeIdentifier([$contentTypeId]),
        ]);
        $query->limit = 1000; //todo solr int max

        $em = $this->getDoctrine()->getManager();
        return $searchService->findLocations($query);
    }
}