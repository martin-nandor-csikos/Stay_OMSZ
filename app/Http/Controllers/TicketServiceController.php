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
}
