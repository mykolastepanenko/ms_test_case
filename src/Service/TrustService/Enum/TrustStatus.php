<?php

namespace App\Service\TrustService\Enum;

use InvalidArgumentException;

enum TrustStatus: int
{
    case PENDING_ID = 1;
    case ACTIVE_ID = 2;
    case BANNED_ID = 3;

    /**
     * @return string[]
     */
    public static function labels(): array
    {
        return [
            self::PENDING_ID->value => 'Pending',
            self::ACTIVE_ID->value => 'Active',
            self::BANNED_ID->value => 'Banned',
        ];
    }

    /**
     * @param int $statusId
     *
     * @return string
     */
    public static function labelByStatus(int $statusId): string
    {
        if (!in_array($statusId, self::statuses())) {
            throw new InvalidArgumentException("Invalid status id {$statusId}");
        }

        return self::labels()[$statusId];
    }

    /**
     * @return array
     */
    public static function statuses(): array
    {
        return array_keys(self::labels());
    }
}
