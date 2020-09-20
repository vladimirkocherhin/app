<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Progress;
use AppBundle\Entity\WordProgress;
use DateInterval;
use DateTimeZone;
use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Symfony\Component\Routing\Annotation\Route;


class ProgressController extends  Controller
{
    const WORD_LIMIT = 5;

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request)
    {
        $response = new JsonResponse();

        $response->setData([
            'username' => $this->getUser()->getUsername(),
            'todo' => 'user stats'
        ]);

        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );

        return $response;
    }

    /**
     * @Route("/training/{slug}/submit")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function submit(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        foreach ($data as $item) {
            $location  = $item['id'];
            $status = $item['status'];
            //dump($location, $username, $status); die();
            $searchResult = $this->getDoctrine()->getRepository('AppBundle:WordProgress')
                ->findOneBy([
                    'username' => $this->getUser()->getUsername(),
                    'wordLocationID' => $item['id'],
            ]);

            if ($searchResult){
                $timeNow = new \DateTime("now");
                $searchResult->setStatus($status > 0 ? (string)(1 + (int)$searchResult->getStatus()) : (string)0)
                ->setTimeToReview(  $timeNow->add(new DateInterval('P1M') ));
            }

            if (!$searchResult){
                $progress = new WordProgress();

                $progress
                    ->setStatus('1' == $status? (string)2 : (string)0)
                    ->setUsername($this->getUser()->getUsername())
                    ->setTimeToReview( date_create())
                    ->setWordLocationID($item['id']);
                $em->persist($progress);
            }
        }

        $em->flush();

        return new Response('It worked');
    }


    /**
     * @Route("/training/{slug}")
     * @param string $slug
     * @return Response
     *
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function training(string $slug)
    {
        // find all not legitimate words
        $repo = $this->getDoctrine()->getRepository('AppBundle:WordProgress');

        $qb = $repo->createQueryBuilder('p');
        $query = $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('p.username', '?1'),
                $qb->expr()->gt('p.timeToReview', '?2')
            ))
            ->setParameter(1, $this->getUser()->getUsername())
            ->setParameter(2, new \DateTime('@'.strtotime('now')))
            ->getQuery();

        $indexList = [];
        $indexList[] = 1;
        foreach ($query->getResult() as $result){
            $indexList[] = $result->getWordLocationID();
        }

        // 5 words of a given location
        /** @var $repository Repository */
        $repository = $this->get( 'ezpublish.api.repository' );
        $searchService = $repository->getSearchService();
        $query = new LocationQuery();
        $query->limit = self::WORD_LIMIT;
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\ParentLocationId( $slug ),
            new Criterion\ContentTypeIdentifier(['word']),
            new Criterion\LogicalNot(new Criterion\LocationId($indexList))
        ]);

        $em = $this->getDoctrine()->getManager();
        $results = $searchService->findLocations($query);

        // logged in username
        $username = $this->getUser()->getUsername();

        //Fetch words for fake translations
        $excludedWords = [];
        foreach ($results->searchHits as $excludedWord){
            $excludedWords[] = $excludedWord->valueObject->id;
        }

        /** @var $repository Repository */
        $repository = $this->get( 'ezpublish.api.repository' );
        $searchService = $repository->getSearchService();
        $query = new LocationQuery();
        $query->limit = self::WORD_LIMIT*3;
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\Subtree( '/1/2/54/' ),
            new Criterion\ContentTypeIdentifier(['word']),
            new Criterion\LogicalNot(new Criterion\LocationId($excludedWords))
        ]);

        $em = $this->getDoctrine()->getManager();
        $fakeWords = $searchService->findLocations($query)->searchHits;
        shuffle($fakeWords);


        // json response
        $wordsJSON = [];
        $i = 0;

        foreach ($results->searchHits as $result ){
            $i++;
            $location = $result->valueObject->contentInfo->mainLocationId;
            $word = $result->valueObject->content->fields['word']['eng-GB']->text;
            $translation = $result->valueObject->content->fields['translation']['eng-GB']->text;
            $fakes = [];
            for ($i = 0; $i < 3; $i++){
                $currentWord = array_pop($fakeWords);
                $fakes[] = [
                    'id' => $currentWord->valueObject->id,
                    'name' => $currentWord->valueObject->content->fields['translation']['eng-GB']->text
                ];
            }

            $fakes [] = ['id' => $location, 'name' => $translation];
            shuffle($fakes);

            $wordsJSON[] = [
                'id' => $location,
                'word' => $word,
                'translation' => $translation,
                'fakeTranslations' => $fakes
            ];
        }

        $response = new JsonResponse();

        $response->setData($wordsJSON);

        // create progress if possible
        try {
            $progress = new Progress();
            $progress->setUsername($username);
            $em->persist($progress);
            $em->flush();
        }
        catch (\Exception $e){}

        return $response;
    }
}