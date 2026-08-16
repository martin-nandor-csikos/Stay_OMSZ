<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RankController extends Controller
{
    /**
     * Get every rank ordered from lowest (1) to highest.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRanksQuery()
    {
        return DB::table('ranks')->orderBy('rank_order')->get();
    }
}
