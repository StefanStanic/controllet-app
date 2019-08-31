<?php

namespace App\Command;

use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CurrencyPopulationCommand extends Command
{
    protected static $defaultName = 'app:currency-population';
    private $em;
    private $client;


    public function __construct(string $name = null, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->em = $em;
        $this->client = new Client();
    }

    protected function configure()
    {
        $this
            ->setDescription('Populates currencies')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Currency population',
            '========================',
            ''
        ]);

        $output->writeln([
            'Job started on '. date('d-m-Y H:i:s'),
            'Running ...'
        ]);

        //truncate table
        $classMetaData = $this->em->getClassMetadata(Currency::class);
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($classMetaData->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        }
        catch (\Exception $e) {
            $connection->rollback();
        }


        $result = $this->client->get('https://free.currconv.com/api/v7/currencies', [
            'query' => [
                'apiKey' => 'a34b33d8e8738cb32ee5'
            ]
        ])->getBody()->getContents();


        foreach(json_decode($result, true)['results'] as $label => $value){
            $currency = new Currency();
            $currency
                ->setName($value['currencyName'])
                ->setCurrencyLabel($value['id'])
                ->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));

            $this->em->persist($currency);
        }

        $this->em->flush();

        $output->writeln([
            'Job ended on '. date('d-m-Y H:i:s'),
            'shutting down ...'
        ]);
    }
}
