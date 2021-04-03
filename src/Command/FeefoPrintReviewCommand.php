<?php

namespace App\Command;

use App\Services\ReviewService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FeefoPrintReviewCommand extends Command
{
    protected static $defaultName = 'feefo:print-reviews';

    protected function configure(): void
    {
        $this->setDescription('Prints out the Feefo reviews for a product')
            ->addArgument('id', InputArgument::REQUIRED, 'Id of the product')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $productId = $input->getArgument('id');

        $reviewService = new ReviewService();
        $result = $reviewService->getReviews($productId);

        $output->writeln("There are {$result['count']} reviews and the average is {$result['average']}%");

        return 0;
    }
}
