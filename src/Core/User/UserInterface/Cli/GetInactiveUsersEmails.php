<?php

declare(strict_types=1);

namespace App\Core\User\UserInterface\Cli;

use App\Common\Bus\QueryBusInterface;
use App\Core\User\Application\Query\GetInactiveUsersEmails\GetInactiveUsersEmailsQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(
    name: 'app:user:get-inactive-emails',
    description: 'Pobieranie emaili nieaktywnych użytkowników'
)]
class GetInactiveUsersEmails extends Command
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly TranslatorInterface $translator
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $emails = $this->queryBus->dispatch(new GetInactiveUsersEmailsQuery());

        if (empty($emails)) {
            $output->writeln($this->translator->trans('cli.user.inactive_users.empty'));

            return Command::SUCCESS;
        }

        $output->writeln($this->translator->trans('cli.user.inactive_users.title'));
        foreach ($emails as $email) {
            $output->writeln(
                $this->translator->trans('cli.user.inactive_users.list_item', ['%email%' => $email])
            );
        }

        return Command::SUCCESS;
    }
}
