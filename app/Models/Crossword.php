<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 *
 * @property int $id
 * @property string $answer
 * @property string $clue
 * @property int $length
 * @property string $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Crossword extends Model
{
    protected $table = 'crosswords';
    public $timestamps = false;

    protected $fillable = ['answer', 'clue', 'length', 'date', 'direction'];
}
