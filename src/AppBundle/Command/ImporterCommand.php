<?php


namespace AppBundle\Command;

use Exception;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigResolver;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\SearchService;

class ImporterCommand extends ContainerAwareCommand
{
    /**
     * importer constructor.
     */
    public function __construct()
    {
        parent::__construct('app:generate:import_file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $yml = '';
        $id = 1;
        $parentLocation = 112;
        $isCategory = false;
        $lastCategoryId = null;

        $k = 0;

        $csv = array_map('str_getcsv', file('.data/phrases.csv'));
        foreach ($csv as $item) {
            if ($id === 1){
                $k++;
                $isCategory = true;
                $lastCategoryId = $id;
            }

            if ($item[0] !== '') {
                $yml .= $this->getContainer()->get('twig')->render('/full/importer.yml.twig', [
                    'id' => $id,
                    'polish' => $item[0],
                    'english' => $item[1],
                    'german' => $item[2],
                    'russian' => $item[3],
                    'ukrainian' => $item[4],
                    'turkish' => $item[5],
                    'is_category' => $isCategory,
                    'parent_location' => $isCategory ? $parentLocation : '"reference:phrase_'.$lastCategoryId.'"'
                ]);
                $isCategory = false;
                $id++;
            }


            if ( $item[0] === ''){
                $k++;
                $isCategory = true;
                $lastCategoryId = $id;
            }

        }

        $file_put_contents = file_put_contents('src/AppBundle/MigrationVersions/import1.yml', $yml);
    }
}
