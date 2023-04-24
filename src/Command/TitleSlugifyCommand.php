<?php

namespace App\Command;

use App\Service\MySlugger;
use App\Repository\ArtworkRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TitleSlugifyCommand extends Command
{
    protected static $defaultName = 'app:title:slugify';
    protected static $defaultDescription = 'Slugify the title of each artwork';

    private $sluggerManager;
    private $artworkRepository;
    private $entityManager;

    public function __construct(ArtworkRepository $artworkRepository, MySlugger $sluggerManager, ManagerRegistry $doctrine)
    {
        $this->artworkRepository = $artworkRepository;
        $this->sluggerManager = $sluggerManager;
        $this->entityManager = $doctrine->getManager();

        // On ramène le constructeur parent
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
        
        //On met un message qui explique la manip
        $io->info('Mise à jour de nos slugs dans la BDD');

        $artworks = $this->artworkRepository->findAll();

        foreach($artworks as $artwork)
        {
            $title = $artwork->getTitle();
            $artwork->setSlug($this->sluggerManager->slugify($title));       
        }

        $this->entityManager->flush();

        // affiche ce message en cas de succès 
        $io->success('Mise à jour éxécutée');

        return Command::SUCCESS;
    }
}
