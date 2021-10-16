<?php
/**
 * @package     HitarthPattani\CustomerPassword
 * @author      Hitarth Pattani <hitarthpattani@gmail.com>
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace HitarthPattani\CustomerPassword\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use HitarthPattani\CustomerPassword\Model\ChangePassword as ChangePasswordModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ChangePassword extends Command
{
    /**
     * @var State
     */
    private $appState;

    /**
     * @var ChangePasswordModel
     */
    private $changePassword;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @param State $appState
     * @param ChangePasswordModel $changePassword
     */
    public function __construct(
        State $appState,
        ChangePasswordModel $changePassword
    ) {
        $this->appState = $appState;
        $this->changePassword = $changePassword;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('customer:password:change');
        $this->setDescription('Change customer password without sending reset password email');
        $this->addOption(
            'website',
            'w',
            InputOption::VALUE_OPTIONAL,
            'Website code if customer accounts are website scope'
        );
        $this->addArgument('email', InputArgument::REQUIRED, 'Customer Email');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password to set');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;

        $output->setDecorated(true);
        $this->appState->setAreaCode(Area::AREA_GLOBAL);

        try {
            $this->changePassword->execute($this->getEmail(), $this->getPassword(), $this->getWebsite());
            $output->writeln(
                sprintf(
                    '<info>Password update successfully for customer "%s".</info>',
                    $this->getEmail()
                )
            );
            return Cli::RETURN_SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln("<error>{$exception->getMessage()}</error>");
            // we must have an exit code higher than zero to indicate something was wrong
            return Cli::RETURN_FAILURE;
        }
    }

    /**
     * @return string
     */
    private function getEmail(): string
    {
        return $this->input->getArgument('email') ?? '';
    }

    /**
     * @return string
     */
    private function getPassword(): string
    {
        return $this->input->getArgument('password') ?? '';
    }

    /**
     * @return string
     */
    private function getWebsite(): string
    {
        return $this->input->getOption('website') ?? '';
    }
}
