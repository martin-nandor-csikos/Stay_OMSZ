<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TicketServiceController extends Controller
{
    /**
     * Get the list of ticket services with their costs
     *
     * @return array
     */
    public function getServicesQuery()
    {
        return DB::table('ticket_services')->select('service_name', 'cost')->get()->pluck('cost', 'service_name')->toArray();
    }

    /**
     * Get the ticket services with their latest cost update timestamp.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getServicesWithUpdateTimesQuery()
    {
        return DB::table('ticket_services')
            ->select('service_name', 'cost', 'created_at', 'updated_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }
}
