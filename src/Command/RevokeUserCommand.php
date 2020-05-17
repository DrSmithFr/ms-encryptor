<?php


namespace App\Command;


use Exception;
use App\Enum\SecurityRoleEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;

class RevokeUserCommand extends Command
{
    private EntityManagerInterface  $entityManager;

    private UserRepository          $repository;

    public function __construct(
        UserRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->repository    = $repository;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setName('encryptor:user:revoke')
            ->addArgument('uuid', InputArgument::REQUIRED, 'UUID of user')
            ->addOption('user', 'u', InputOption::VALUE_NONE, 'Add role user')
            ->addOption('admin', 'a', InputOption::VALUE_NONE, 'Add role admin')
            ->addOption('super-admin', 's', InputOption::VALUE_NONE, 'Add role super-admin');
    }

    /**
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $uuid = $input->getArgument('uuid');
        $io   = new SymfonyStyle($input, $output);

        $io->title('Promoting ' . $uuid);

        $user = $this
            ->repository
            ->loadUserByUsername($uuid);

        if (!$user) {
            throw new UserNotFoundException('uuid', $input->getArgument('uuid'));
        }

        if ($input->getOption('user')) {
            $user->removeRole(SecurityRoleEnum::USER);
        }

        if ($input->getOption('admin')) {
            $user->removeRole(SecurityRoleEnum::ADMIN);
        }

        if ($input->getOption('super-admin')) {
            $user->removeRole(SecurityRoleEnum::SUPER_ADMIN);
        }

        $this->entityManager->flush();

        $io->success(
            sprintf(
                '%s updated with roles : %s',
                $uuid,
                implode(', ', $user->getRoles())
            )
        );
    }
}
