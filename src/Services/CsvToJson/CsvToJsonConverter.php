<?php
declare( strict_types=1 );

namespace App\Services\CsvToJson;

use App\Dto\JsonUser;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Output\OutputInterface;

class CsvToJsonConverter {

    /**
     * @throws \Exception
     */
    public function convertFile(string $inputFilename, string $outputFilename, ?OutputInterface $consoleOutput = null): int {
        $progressIndicator = $consoleOutput !== null ? new ProgressIndicator($consoleOutput) : null;

        $progressIndicator?->start('Working...');

        $rows = [];
        $rowCount = 0;
        if( ( $file = fopen($inputFilename, 'rb') ) !== false ) {
            $header = fgetcsv($file);
            $headerCount = count($header);
            while( ( $row = fgetcsv($file) ) !== false ) {
                $colCount = count($row);
                if( $colCount === $headerCount ) {
                    $entry = array_combine($header, $row);
                    $rows[] = $this->arrayToObject($entry);
                } else {
                    throw new \Exception('Invalid number of columns at line ' . ( $rowCount + 2 ) . " (row " . ( $rowCount + 1 ) . '). Expected: ' . $headerCount . ', Got: ' . $colCount);
                }
                $rowCount++;

                if( $rowCount % 1000 === 0 ) {
                    $progressIndicator?->setMessage('Working..., already processed: ' . $rowCount);
                }
                $progressIndicator?->advance();
            }
            fclose($file);
        } else {
            throw new \Exception('Could not read CSV "' . $inputFilename . '"');
        }

        $progressIndicator?->finish('Done.');

        $json = json_encode($rows, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

        if( file_put_contents($outputFilename, $json) === false ) {
            throw new \Exception('Could not write JSON "' . $outputFilename . '"');
        }

        return $rowCount;
    }

    private function arrayToObject(array $array): JsonUser {
        return new JsonUser(
            $array[ 'ID' ] ?? '',
            $this->normalizeName($array[ 'First name' ] ?? '', $array[ 'Last name' ] ?? ''),
            $array[ 'email' ] ?? '',
            $array[ 'password' ] ?? '',
            $this->normalizeStatus($array[ 'status' ] ?? ''),
        );
    }

    private function normalizeStatus(string $status): ?string {
        return match ( $status ) {
            '0' => JsonUser::ACTIVE,
            '1' => JsonUser::INACTIVE,
            default => null,
        };
    }

    private function normalizeName(string $firstName, string $lastName): string {
        return $firstName . ' ' . $lastName;
    }

}
