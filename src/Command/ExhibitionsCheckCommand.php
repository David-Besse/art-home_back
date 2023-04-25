<?php

namespace App\Command;

use DateTime;
use App\Repository\ExhibitionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExhibitionsCheckCommand extends Command
{
    protected static $defaultName = 'app:exhibitions:check';
    protected static $defaultDescription = 'Add a short description for your command';
    private $exhibitionRepository;
    private $entityManager;

    public function __construct(ExhibitionRepository $exhibitionRepository, ManagerRegistry $doctrine)
    {
        $this->exhibitionRepository = $exhibitionRepository;
        $this->entityManager = $doctrine->getManager();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Vérification de la validité de l\'exposition');

        $exhibitions = $this->exhibitionRepository->findAll();

        foreach($exhibitions as $exhibition)
        {
            $exhibitionDate = $exhibition->getStartDate();
            $now = new DateTime();
            $interval = $exhibitionDate->diff($now);

            if($interval->m == 1)
            {
                $exhibition->setStatus(0);

            }
        }

        $this->entityManager->flush();


        $io->success('Vérification des expositions effectuées');

        return Command::SUCCESS;
    }
}
