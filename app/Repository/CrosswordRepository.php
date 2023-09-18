<?php

namespace App\Repository;

use App\Models\Crossword;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CrosswordRepository
{

    public function list(string $date)
    {
        $returnData = Cache::remember("crosswords_$date",3600, fn() =>$this->listQuery($date));

        if ($returnData->isNotEmpty()) {
            return $returnData;
        }

        return $this->generateCrosswords($date);
    }

    private function listQuery(string $date)
    {
        return Crossword::select(['answer', 'clue', 'length', 'date',])
            ->where('date', $date)
            ->get();
    }

    private function generateCrosswords(string $date): Collection
    {
        return collect([]);
    }
}
