<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Rank;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SettingController extends Controller
{
    /**
     * @var TicketServiceController
     */
    protected $ticketServiceController;

    /**
     * @var RankController
     */
    protected $rankController;

    /**
     * SettingController constructor.
     */
    public function __construct()
    {
        $this->ticketServiceController = new TicketServiceController();
        $this->rankController = new RankController();
    }

    /**
     * Get the current settings row.
     *
     * @return \stdClass
     */
    public function getSettingsQuery()
    {
        return DB::table('settings')->first();
    }

    /**
     * Update the ticket service costs and the minimum settings.
     * Only fields that actually changed are saved and logged.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $services = $this->ticketServiceController->getServicesQuery();
        $settings = $this->getSettingsQuery();
        $existingRanks = $this->rankController->getRanksQuery()->keyBy('id');
        $canEditRanks = Auth::user()->adminLevel == 2;

        $this->validateSettings($request, $services);

        if ($canEditRanks) {
            $this->validateRanks($request, $existingRanks);
        }

        try {
            foreach ($services as $serviceName => $cost) {
                $serviceCost = $serviceName . '_cost';

                // Only update if the cost has changed
                if ($request->input($serviceCost) != $cost) {
                    DB::table('ticket_services')
                        ->where('service_name', $serviceName)
                        ->update(['cost' => (int) $request->input($serviceCost)]);

                    $this->logSettingChange($serviceName . ' diagnózis ára', '$' . $cost, '$' . $request->input($serviceCost));
                }
            }

            foreach ($this->minimumFields() as $field => $label) {
                // Only update if the value has changed
                if ((int) $request->input($field) != (int) $settings->$field) {
                    DB::table('settings')
                        ->where('id', $settings->id)
                        ->update([$field => (int) $request->input($field)]);

                    $this->logSettingChange($label, $settings->$field, $request->input($field));
                }
            }

            if ($canEditRanks) {
                $submittedRanks = $request->input('ranks', []);

                // Any existing rank missing from the submission was removed client-side, so delete it
                foreach ($existingRanks as $rankId => $rank) {
                    if (!array_key_exists((string) $rankId, $submittedRanks)) {
                        DB::table('ranks')->where('id', $rankId)->delete();
                        $this->logSettingChange($rank->name . ' rang törölve', $rank->rank_order, '-');
                    }
                }

                foreach ($submittedRanks as $rankId => $data) {
                    if (!isset($existingRanks[$rankId])) {
                        continue;
                    }

                    $rank = $existingRanks[$rankId];
                    $newName = $data['name'];
                    $newSalary = (int) $data['salary'];
                    $newOrder = (int) $data['order'];

                    if ($newName !== $rank->name) {
                        DB::table('ranks')->where('id', $rankId)->update(['name' => $newName]);
                        $this->logSettingChange($rank->name . ' rang neve', $rank->name, $newName);
                    }

                    if ($newSalary !== (int) $rank->salary) {
                        DB::table('ranks')->where('id', $rankId)->update(['salary' => $newSalary]);
                        $this->logSettingChange($rank->name . ' rang fizetése', '$' . $rank->salary, '$' . $newSalary);
                    }

                    if ($newOrder !== (int) $rank->rank_order) {
                        DB::table('ranks')->where('id', $rankId)->update(['rank_order' => $newOrder]);
                        $this->logSettingChange($rank->name . ' rang sorrendje', $rank->rank_order, $newOrder);
                    }
                }

                foreach ($request->input('new_ranks', []) as $newRankData) {
                    $newRank = Rank::create([
                        'name' => $newRankData['name'],
                        'salary' => (int) $newRankData['salary'],
                        'rank_order' => (int) $newRankData['order'],
                    ]);

                    DB::table('admin_logs')->insert(['user_id' => Auth::id(), 'didWhat' => 'Új rangot hozott létre (név: ' . $newRank->name . ', fizetés: $' . $newRank->salary . ', ' . $newRank->rank_order . '. hely).']);
                }
            }
        } catch (Exception $e) {
            return Redirect::route('admin.index')->with('settings-not-updated', 'A beállítások frissítése sikertelen.');
        }

        return Redirect::route('admin.index')->with('settings-updated', 'A beállítások sikeresen frissültek.');
    }

    /**
     * Map of the minimum setting fields to their admin log labels.
     *
     * @return array<string, string>
     */
    private function minimumFields(): array
    {
        return [
            'minimum_report_count' => 'Minimum jelentés szám',
            'minimum_duty_time' => 'Minimum szolgálati idő',
            'double_week_report_count' => 'Dupla hét jelentés szám',
            'double_week_duty_time' => 'Dupla hét szolgálati idő',
        ];
    }

    /**
     * Validate the service cost and minimum setting inputs.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    private function validateSettings(Request $request, array $services)
    {
        foreach ($services as $serviceName => $cost) {
            $serviceCost = $serviceName . '_cost';

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

        $request->validate(
            [
                'minimum_report_count' => ['required', 'integer', 'min:0', 'max:1000'],
                'minimum_duty_time' => ['required', 'integer', 'min:0', 'max:100000'],
                'double_week_report_count' => ['required', 'integer', 'min:0', 'max:1000'],
                'double_week_duty_time' => ['required', 'integer', 'min:0', 'max:100000'],
            ],
            [
                'minimum_report_count.required' => 'A minimum jelentés szám nem lehet üres.',
                'minimum_duty_time.required' => 'A minimum szolgálati idő nem lehet üres.',
                'double_week_report_count.required' => 'A dupla hét jelentés szám nem lehet üres.',
                'double_week_duty_time.required' => 'A dupla hét szolgálati idő nem lehet üres.',
            ],
        );
    }

    /**
     * Validate the rank inputs (existing edits + new pending ranks). Also ensures the submitted
     * orders form a valid 1..n permutation across the final set of ranks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Support\Collection  $existingRanks
     */
    private function validateRanks(Request $request, $existingRanks)
    {
        $submittedRanks = $request->input('ranks', []);
        $newRanks = $request->input('new_ranks', []);
        $rules = [];
        $messages = [];

        foreach ($submittedRanks as $rankId => $data) {
            if (!isset($existingRanks[$rankId])) {
                continue;
            }

            $rules["ranks.$rankId.name"] = ['required', 'string', 'max:255'];
            $rules["ranks.$rankId.salary"] = ['required', 'integer', 'min:0', 'max:1000000'];
            $rules["ranks.$rankId.order"] = ['required', 'integer'];

            $messages["ranks.$rankId.name.required"] = 'A rang neve nem lehet üres.';
            $messages["ranks.$rankId.salary.required"] = 'A fizetés nem lehet üres.';
            $messages["ranks.$rankId.salary.integer"] = 'A fizetésnek egy pozitív egész számnak kell lennie.';
            $messages["ranks.$rankId.order.required"] = 'A sorrend nem lehet üres.';
        }

        foreach ($newRanks as $index => $data) {
            $rules["new_ranks.$index.name"] = ['required', 'string', 'max:255'];
            $rules["new_ranks.$index.salary"] = ['required', 'integer', 'min:0', 'max:1000000'];
            $rules["new_ranks.$index.order"] = ['required', 'integer'];

            $messages["new_ranks.$index.name.required"] = 'Az új rang neve nem lehet üres.';
            $messages["new_ranks.$index.salary.required"] = 'Az új rang fizetése nem lehet üres.';
            $messages["new_ranks.$index.salary.integer"] = 'A fizetésnek egy pozitív egész számnak kell lennie.';
        }

        $request->validate($rules, $messages);

        $submittedNames = [];

        foreach ($submittedRanks as $rankId => $data) {
            if (isset($existingRanks[$rankId])) {
                $submittedNames[] = mb_strtolower(trim($data['name']));
            }
        }

        foreach ($newRanks as $data) {
            $submittedNames[] = mb_strtolower(trim($data['name']));
        }

        if (count(array_unique($submittedNames)) !== count($submittedNames)) {
            throw ValidationException::withMessages([
                'rank_name' => 'Van már ilyen nevű rank',
            ]);
        }

        $submittedOrders = [];

        foreach ($submittedRanks as $rankId => $data) {
            if (isset($existingRanks[$rankId])) {
                $submittedOrders[] = (int) $data['order'];
            }
        }

        foreach ($newRanks as $data) {
            $submittedOrders[] = (int) $data['order'];
        }

        $rankCount = count($submittedOrders);

        foreach ($submittedOrders as $order) {
            if ($order < 1 || $order > $rankCount) {
                throw ValidationException::withMessages([
                    'rank_order' => 'A sorrendnek 1 és ' . $rankCount . ' között kell lennie.',
                ]);
            }
        }

        if (count(array_unique($submittedOrders)) !== $rankCount) {
            throw ValidationException::withMessages([
                'rank_order' => 'Minden rangnak egyedi sorrenddel kell rendelkeznie 1 és ' . $rankCount . ' között.',
            ]);
        }
    }

    /**
     * Log an admin action for a changed setting.
     *
     * @param string $label
     * @param mixed $oldValue
     * @param mixed $newValue
     */
    private function logSettingChange($label, $oldValue, $newValue)
    {
        DB::table('admin_logs')->insert(['user_id' => Auth::user()->id, 'didWhat' => 'Frissítette a(z) ' . $label . ' értékét (' . $oldValue . ' --> ' . $newValue . ').']);
    }
}
