<?php

namespace App\Services\Pos;

class ShiftService
{
    public function open(int $outletId, int $userId, float $openingCash): array
    {
        return [
            'status' => 'stub',
            'message' => 'ShiftService.open belum diimplementasikan.',
        ];
    }

    public function close(int $shiftId, float $closingCash): array
    {
        return [
            'status' => 'stub',
            'message' => 'ShiftService.close belum diimplementasikan.',
        ];
    }
}
