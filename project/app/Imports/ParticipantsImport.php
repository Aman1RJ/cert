<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\Participant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantsImport implements ToModel, WithHeadingRow
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function model(array $row)
    {
        return new Participant([
            'event_id' => $this->event->id,
            'name' => $row['name'] ?? $row['participant_name'] ?? '',
            'email' => $row['email'] ?? '',
            'data' => $row,
        ]);
    }
}