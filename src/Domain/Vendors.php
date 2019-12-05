<?php

namespace App\Domain;

class Vendors
{
    public const LINT = 'lint';
    public const PDEPEND = 'pdepend';
    public const PHPCODESNIFFER = 'phpcodesniffer';
    public const PHPCSFIXER = 'phpcsfixer';
    public const PHPCPD = 'phpcpd';
    public const PHPDCD = 'phpdcd';
    public const PHPLOC = 'phploc';
    public const PHPMETRIC = 'phpmetric';
    public const PHPMD = 'phpmd';
    public const PHPSTAN = 'phpstan';
    public const SECURITY = 'security';

    public static function toStaticArray()
    {
        return [
            self::LINT,
            self::PDEPEND,
            self::PHPCODESNIFFER,
            self::PHPCSFIXER,
            self::PHPCPD,
            self::PHPDCD,
            self::PHPLOC,
            self::PHPMETRIC,
            self::PHPMD,
            self::PHPSTAN,
            self::SECURITY,
        ];
    }
}
