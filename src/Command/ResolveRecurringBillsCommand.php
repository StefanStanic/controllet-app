<?php

namespace App\Command;

use App\Entity\Bills;
use App\Entity\Transaction;
use App\Entity\TransactionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ResolveRecurringBillsCommand extends Command
{
    protected static $defaultName = 'app:resolve-recurring-bills';
    private $em;

    /**
     * ResolveRecurringBillsCommand constructor.
     * @param string|null $name
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(string $name = null, EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->em = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Resolves recurring bills')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Recurring bills resolver',
            '========================',
            ''
        ]);

        $output->writeln([
            'Job started on '. date('d-m-Y H:i:s'),
            'Running ...'
        ]);

        //get all unresolved recurring bills for current day
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');

        $bills = $this->em->getRepository(Bills::class)->findBy(
            ['date_due' => \DateTime::createFromFormat('Y-m-d', $date)]
        );

        foreach($bills as $bill){
            //init transaction
            $transaction = new Transaction();
            $transactionType = $this->em->getRepository(TransactionType::class)->find(1);

            $user = $bill->getAccount()->getUser();

            $transaction
                ->setAccount($bill->getAccount())
                ->setCategory($bill->getCategory())
                ->setSubCategory($bill->getSubcategory())
                ->setUser($user)
                ->setNote($bill->getNote())
                ->setActive(1)
                ->setTransactionAmount($bill->getAmount())
                ->setTransactionTime(\DateTime::createFromFormat('Y-m-d H:i:s', $datetime))
                ->setTransactionName($bill->getName())
                ->setTransactionType($transactionType);
            ;

            $this->em->persist($transaction);

            $output->writeln([
               'Processed bill id: '. $bill->getId()
            ]);
        }

        $this->em->flush();

        $output->writeln([
            'Job ended on '. date('d-m-Y H:i:s'),
            'shutting down ...'
        ]);

    }
}
