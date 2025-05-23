<?php

namespace App\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EnumType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $column['columnDefinition'] ?? 'VARCHAR(255)';
    }

    public function getName(): string
    {
        return 'enum';
    }
}