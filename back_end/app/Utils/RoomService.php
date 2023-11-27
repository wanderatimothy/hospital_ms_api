<?php

namespace App\Utils;

class RoomService
{

    public const ROOM_OCCUPANCY_STATUSES = [
        'occupied',
        'vacant',
        'reserved',
        'out_of_order',
        'not_applicable'
    ];

    public const ROOM_SEARCHABLE_COLUMNS = [
        'label', 'purpose', 'updated_at', 'unique_number'
    ];

}