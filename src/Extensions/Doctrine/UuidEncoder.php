<?php

namespace Allsoftware\SymfonyKernelTabler\Extensions\Doctrine;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/*
 * Encode UUID to base32.
 * Reformate the UUID to be suited to an URL as it only has alphanumerical characters.
 *
 * NOTE : base32 has fewer characters and therefore is slightly longer than base64,
 * but base32 do not have non alphanumerical characters (=, + or /).
 */
class UuidEncoder
{
    public function encode(UuidInterface $uuid): string
    {
        return gmp_strval(
            gmp_init(
                str_replace('-', '', $uuid->toString()),
                16
            ),
            62
        );
    }

    public function decode(string $encoded): ?UuidInterface
    {
        try {
            return Uuid::fromString(array_reduce(
                [20, 16, 12, 8],
                function ($uuid, $offset) {
                    return substr_replace($uuid, '-', $offset, 0);
                },
                str_pad(
                    gmp_strval(
                        gmp_init($encoded, 62),
                        16
                    ),
                    32,
                    '0',
                    STR_PAD_LEFT
                )
            ));
        } catch (\Throwable $e) {
            return null;
        }
    }
}
