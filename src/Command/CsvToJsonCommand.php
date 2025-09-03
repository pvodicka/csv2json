<?php
declare( strict_types=1 );

namespace App\Command;

use App\Services\CsvToJson\CsvToJsonConverter;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand( name: 'app:csv-json' )]
class CsvToJsonCommand {

    public function __construct(
        private readonly CsvToJsonConverter $csvToJsonConverter
    ) {
    }

    public function __invoke(
        #[Argument( 'Input file' )] string  $inputFilename,
        #[Argument( 'Output file' )] string $outputFilename,
        OutputInterface $output
    ): int {

        $output->writeln('Converting CSV to JSON');

        $count = $this->csvToJsonConverter->convertFile($inputFilename, $outputFilename, $output);

        $output->writeln($count . ' rows saved to JSON.');

        return $count > 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
