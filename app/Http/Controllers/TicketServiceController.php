<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

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
     * Update the costs of ticket services which were modified
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Facades\Redirect
     */
    public function updateServiceCosts(Request $request)
    {
        $services = $this->getServicesQuery();
        $this->validateServiceCost($request, $services);

        foreach ($services as $serviceName => $cost) {
            $serviceCost = $serviceName . '_cost';

            // Only update if the cost has changed
            if ($request->input($serviceCost) != $cost) {
                try {
                    DB::table('ticket_services')
                        ->where('service_name', $serviceName)
                        ->update(['cost' => (int) $request->input($serviceCost)]);

                    DB::table('admin_logs')->insert(['user_id' => Auth::user()->id, 'didWhat' => 'Frissítette a(z) ' . $serviceName . ' diagnózis árát ($' . $cost . ' --> $' . $request->input($serviceCost) . ').']);
                } catch (Exception $e) {
                    return Redirect::route('admin.index')->with('service-price-not-updated', 'Az ellátások árainak frissítése sikertelen.');
                }
            }
        }

        return Redirect::route('admin.index')->with('service-price-updated', 'Az ellátások árai sikeresen frissültek.');
    }

    /**
     * Validate the service cost inputs
     *
     * @param  \Illuminate\Http\Request  $request
     */
    private function validateServiceCost(Request $request, array $services)
    {
        foreach ($services as $service) {
            $serviceCost = $service->service_name . '_cost';

            $request->validate(
                [
                    $serviceCost => ['required', 'integer', 'max:300000', 'min:1'],
                ],
                [
                    $serviceCost . '.required' => 'Az ár nem lehet üres.',
                    $serviceCost . '.integer' => 'Az árnak egy pozitív egész számnak kell lennie.',
                    $serviceCost . '.max' => 'Az ár maximum $300.000 lehet.',
                    $serviceCost . '.min' => 'Az ár minimum $1 lehet.',
                ],
            );
        }
    }
}
