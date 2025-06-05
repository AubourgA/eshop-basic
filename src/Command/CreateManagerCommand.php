<?php

namespace App\Command;

use App\Entity\Manager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Commande Symfony permettant de créer un utilisateur `Manager` via le terminal.
 *
 * Cette commande est destinée aux environnements de développement uniquement.
 * Elle permet d’enregistrer un nouveau manager avec :
 * - Un email
 * - Un mot de passe (hashé automatiquement)
 * - Un département
 * - Un matricule généré automatiquement (6 chiffres)
 * - Le rôle `ROLE_ADMIN`
 *
 * La commande est utile pour initialiser un compte administrateur dans un contexte local ou pour les tests.
 *
 * Dépendances injectées :
 * - EntityManagerInterface : pour la persistance du manager
 * - UserPasswordHasherInterface : pour hasher le mot de passe
 * - KernelInterface : pour vérifier que l’environnement est bien `dev`
 *
 * En cas d'exécution dans un autre environnement que `dev`, la commande est bloquée pour éviter toute création non contrôlée.
 *
 * Utilisation :
 * ```bash
 * php bin/console app:create-manager
 * ```
 *
 * Alias disponible : `app:add-manager`
 */
#[AsCommand(
    name: 'app:create-manager',
    description: 'Creates a new manager.',
    hidden: false,
    aliases: ['app:add-manager']
)]
class CreateManagerCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private KernelInterface $kernel
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Manager Creator',
            '============',
            '',
        ]);

        $io = new SymfonyStyle($input, $output);

        if ($this->kernel->getEnvironment() !== 'dev') {
            $io->error('Cette commande ne peut être exécutée qu’en environnement de développement.');
            return Command::FAILURE;
        }

        $helper = $this->getHelper('question');

        // Email
        $emailQuestion = new Question('Email de l\'admin : ');
        $email = $helper->ask($input, $output, $emailQuestion);

        // Mot de passe
        $passwordQuestion = new Question('Mot de passe : ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $plainPassword = $helper->ask($input, $output, $passwordQuestion);

        // Département
        $departementQuestion = new Question('Département : ');
        $departement = $helper->ask($input, $output, $departementQuestion);

        // Création du Manager
        $manager = new Manager();
        $manager->setEmail($email);
        $manager->setDepartement($departement);
        $manager->setRoles(['ROLE_ADMIN']);

        // Matricule aléatoire à 6 chiffres
        $manager->setMatricule(str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT));

        // Hash du mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($manager, $plainPassword);
        $manager->setPassword($hashedPassword);

        // Persistance
        $this->entityManager->persist($manager);
        $this->entityManager->flush();

        $io->success('Admin créé avec succès !');
        $io->note(sprintf("Email: %s\nMatricule: %s", $email, $manager->getMatricule()));

        return Command::SUCCESS;
    }
}