<?php
declare(strict_types=1);

namespace App\Tests\Services\CsvToJson;

use App\Dto\JsonUser;
use App\Services\CsvToJson\CsvToJsonConverter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CsvToJsonConverterTest extends TestCase
{
    public function testArrayToObject_WithAllFields_MapsValuesAndNormalizesCorrectly(): void
    {
        $input = [
            'ID' => '42',
            'First name' => 'Alice',
            'Last name' => 'Wonder',
            'email' => 'alice@example.com',
            'password' => 's3cr3t',
            'status' => '1', // should map to JsonUser::ACTIVE
        ];

        $converter = new CsvToJsonConverter();
        $ref = new ReflectionClass($converter);
        $method = $ref->getMethod('arrayToObject');
        $method->setAccessible(true);

        $user = $method->invoke($converter, $input);

        $this->assertInstanceOf(JsonUser::class, $user);
        $this->assertSame('42', $user->ID);
        $this->assertSame('Alice Wonder', $user->full_name);
        $this->assertSame('alice@example.com', $user->email);
        $this->assertSame('s3cr3t', $user->password);
        $this->assertSame(JsonUser::ACTIVE, $user->status);
    }

    public function testArrayToObject_WithMissingFields_UsesDefaultsAndUnknownStatusBecomesNull(): void
    {
        $input = [
            // No ID
            'First name' => 'Bob',
            // No Last name
            // No email
            // No password
            'status' => 'x', // unknown -> null
        ];

        $converter = new CsvToJsonConverter();
        $ref = new ReflectionClass($converter);
        $method = $ref->getMethod('arrayToObject');
        $method->setAccessible(true);

        $user = $method->invoke($converter, $input);

        $this->assertInstanceOf(JsonUser::class, $user);
        $this->assertSame('', $user->ID);
        $this->assertSame('Bob', $user->full_name);
        $this->assertSame('', $user->email);
        $this->assertSame('', $user->password);
        $this->assertNull($user->status, 'Unknown status should normalize to null');
    }
}
