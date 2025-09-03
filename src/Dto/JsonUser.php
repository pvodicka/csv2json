<?php
declare( strict_types=1 );

namespace App\Dto;

class JsonUser {

    public const ACTIVE = 'ACTIVE';
    public const INACTIVE = 'INACTIVE';

    public function __construct(
        public readonly string $ID,
        public readonly string $full_name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $status
    ) {
    }

}
