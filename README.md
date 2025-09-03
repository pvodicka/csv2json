CSV to JSON Converter
======================

Code/Service for conversion from CSV file to JSON data file

Console Command Example
---------------

```bash
php bin/console app:csv-json data_ukol.csv data_ukol.json

```
PHP Call Example
---------------

```php
use App\Services\CsvToJson\CsvToJsonConverter;

$csvToJsonConverter = new CsvToJsonConverter();
$rowsProcessed = $csvToJsonConverter->convertFile('data_ukol.csv', 'data_ukol.json');

```
