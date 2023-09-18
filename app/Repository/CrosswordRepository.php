<?php

namespace App\Repository;

use App\Enum\Model\CrosswordDirectionEnum;
use App\Models\Crossword;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CrosswordRepository
{
    /**
     * NOTE/TODO Normally would keep static database seeding data in a separate file in the "storage/app/for_deployment" directory
     */
    const RAW_DATA = [
        '2023-07-25' => [
            ["answer" => "SNOB", "clue" => "Haughty person", "length" => 4, "date" => "2023-07-25", "direction" => 'across'],
            ["answer" => "ATONED", "clue" => "Apologized for one's sins", "length" => 6, "date" => "2023-07-25", "direction" => 'across'],
            ["answer" => "PORTER", "clue" => "Luggage carrier at a hotel", "length" => 6, "date" => "2023-07-25", "direction" => 'across'],
            ["answer" => "TUTORS", "clue" => "One-on-one teachers", "length" => 6, "date" => "2023-07-25", "direction" => 'across'],
            ["answer" => "STEPS", "clue" => "Studies recommend taking 8,000+ of these each day", "length" => 5, "date" => "2023-07-25", "direction" => 'across'],
            ["answer" => "STOUT", "clue" => "Sturdy", "length" => 5, "date" => "2023-07-25", "direction" => 'down'],
            ["answer" => "NORTE", "clue" => "Direction from Mexico to the U.S., en español", "length" => 5, "date" => "2023-07-25", "direction" => 'down'],
            ["answer" => "ONTOP", "clue" => "Victorious", "length" => 5, "date" => "2023-07-25", "direction" => 'down'],
            ["answer" => "BEERS", "clue" => "7-Across and 1-Down, by different meanings", "length" => 5, "date" => "2023-07-25", "direction" => 'down'],
            ["answer" => "APTS", "clue" => "Many N.Y.C. dwellings=> Abbr.", "length" => 4, "date" => "2023-07-25", "direction" => 'down'],
            ["answer" => "DRS", "clue" => "Hosp. personnel", "length" => 3, "date" => "2023-07-25", "direction" => 'down'],
        ]
    ];

    public function list(string $date): Collection
    {
        $returnData = Cache::remember($this->getListCacheKey($date), 3600, fn() => $this->listQuery($date));

        if ($returnData->isNotEmpty()) {
            return $returnData;
        }

        return $this->generateCrosswords($date);
    }

    private function listQuery(string $date): Collection
    {
        return Crossword::select(['answer', 'clue', 'length', 'date',])
            ->where('date', $date)
            ->get();
    }

    private function generateCrosswords(string $date): Collection
    {
        $returnData = collect();

        if (!isset(self::RAW_DATA[$date])) {
            return $returnData;
        }
        foreach (self::RAW_DATA[$date] as $rawEntry) {
                $returnData->push(Crossword::firstOrCreate([
                    'answer' => $rawEntry['answer'],
                    'date' => $rawEntry['date'],
                    'direction' => $rawEntry['direction'],
                ], $rawEntry));
        }

        return $returnData->isEmpty() ? $returnData : Cache::remember($this->getListCacheKey($date), 3600, fn() => $returnData);
    }

    private function getListCacheKey(string $date): string
    {
        return "crosswords_$date";
    }
}
