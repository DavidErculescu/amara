<?php

namespace App\Command;

use App\Services\ReviewService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FeefoClearCacheCommand extends Command
{
    protected static $defaultName = 'feefo:clear-cache';

    protected function configure(): void
    {
        $this->setDescription('Clears the product reviews cache')
            ->addArgument('id', InputArgument::OPTIONAL, 'Id of the product')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $productId = $input->getArgument('id');

        if (!$productId)
        {
            $productId = -1;
        }

        $reviewService = new ReviewService();
        $result = $reviewService->clearCache($productId);

        $output->writeln("Number of cache entries deleted: {$result}");

        return 0;
    }
}
