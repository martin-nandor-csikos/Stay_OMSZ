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
        if (Auth::user()->adminLevel != 2) {
            abort(403);
        }

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
                        ->update([
                            'cost' => (int) $request->input($serviceCost),
                            'updated_at' => now(),
                        ]);

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

            foreach ($this->bonusFields() as $field => $label) {
                // Only update if the value has changed
                if ((int) $request->input($field) != (int) $settings->$field) {
                    DB::table('settings')
                        ->where('id', $settings->id)
                        ->update([$field => (int) $request->input($field)]);

                    $this->logSettingChange($label, $settings->$field . '%', $request->input($field) . '%');
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
                    $newIsLeader = !empty($data['is_leader']) ? 1 : 0;
                    $newRequiresExam = ($newIsLeader || $newOrder === 1) ? 0 : (!empty($data['requires_exam']) ? 1 : 0);
                    $newMinWeeks = $newIsLeader ? 0 : (isset($data['minimum_successful_weeks']) ? (int) $data['minimum_successful_weeks'] : 2);

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

                    if ($newRequiresExam !== (int) ($rank->requires_exam ? 1 : 0)) {
                        DB::table('ranks')->where('id', $rankId)->update(['requires_exam' => $newRequiresExam]);
                        $this->logSettingChange($rank->name . ' rang vizsgakötelezettsége', $rank->requires_exam ? 'Igen' : 'Nem', $newRequiresExam ? 'Igen' : 'Nem');
                    }

                    if ($newMinWeeks !== (int) $rank->minimum_successful_weeks) {
                        DB::table('ranks')->where('id', $rankId)->update(['minimum_successful_weeks' => $newMinWeeks]);
                        $this->logSettingChange($rank->name . ' rang minimum sikeres heteinek száma', $rank->minimum_successful_weeks, $newMinWeeks);
                    }

                    if ($newIsLeader !== (int) ($rank->is_leader ? 1 : 0)) {
                        DB::table('ranks')->where('id', $rankId)->update(['is_leader' => $newIsLeader]);
                        $this->logSettingChange($rank->name . ' rang leader státusza', $rank->is_leader ? 'Igen' : 'Nem', $newIsLeader ? 'Igen' : 'Nem');
                    }
                }

                foreach ($request->input('new_ranks', []) as $newRankData) {
                    $order = (int) $newRankData['order'];
                    $isLeader = !empty($newRankData['is_leader']) ? 1 : 0;
                    $minWeeks = $isLeader ? 0 : (isset($newRankData['minimum_successful_weeks']) ? (int) $newRankData['minimum_successful_weeks'] : 2);
                    $requiresExam = ($isLeader || $order === 1) ? 0 : (!empty($newRankData['requires_exam']) ? 1 : 0);

                    $newRank = Rank::create([
                        'name' => $newRankData['name'],
                        'salary' => (int) $newRankData['salary'],
                        'rank_order' => $order,
                        'requires_exam' => $requiresExam,
                        'minimum_successful_weeks' => $minWeeks,
                        'is_leader' => $isLeader,
                    ]);

                    DB::table('admin_logs')->insert([
                        'user_id' => Auth::id(),
                        'didWhat' => 'Új rangot hozott létre (név: ' . $newRank->name . ', fizetés: $' . $newRank->salary . ', ' . $newRank->rank_order . '. hely).',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
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
     * Map of the bonus percentage fields to their admin log labels.
     *
     * @return array<string, string>
     */
    private function bonusFields(): array
    {
        return [
            'bonus_first_percentage' => 'Legtöbbet leadott jelentésért járó bónusz',
            'bonus_second_percentage' => 'Második legtöbbet leadott jelentésért járó bónusz',
            'bonus_third_percentage' => 'Harmadik legtöbbet leadott jelentésért járó bónusz',
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

        $request->validate(
            [
                'bonus_first_percentage' => ['required', 'integer', 'min:0', 'max:100'],
                'bonus_second_percentage' => ['required', 'integer', 'min:0', 'max:100'],
                'bonus_third_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            ],
            [
                'bonus_first_percentage.required' => 'A bónusz nem lehet üres.',
                'bonus_first_percentage.max' => 'A bónusz maximum 100% lehet.',
                'bonus_second_percentage.required' => 'A bónusz nem lehet üres.',
                'bonus_second_percentage.max' => 'A bónusz maximum 100% lehet.',
                'bonus_third_percentage.required' => 'A bónusz nem lehet üres.',
                'bonus_third_percentage.max' => 'A bónusz maximum 100% lehet.',
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
            $rules["ranks.$rankId.requires_exam"] = ['nullable', 'boolean'];
            $rules["ranks.$rankId.minimum_successful_weeks"] = ['required', 'integer', 'min:0', 'max:100'];
            $rules["ranks.$rankId.is_leader"] = ['nullable', 'boolean'];

            $messages["ranks.$rankId.name.required"] = 'A rang neve nem lehet üres.';
            $messages["ranks.$rankId.salary.required"] = 'A fizetés nem lehet üres.';
            $messages["ranks.$rankId.salary.integer"] = 'A fizetésnek egy pozitív egész számnak kell lennie.';
            $messages["ranks.$rankId.order.required"] = 'A sorrend nem lehet üres.';
            $messages["ranks.$rankId.minimum_successful_weeks.required"] = 'A minimum sikeres hetek száma nem lehet üres.';
        }

        foreach ($newRanks as $index => $data) {
            $rules["new_ranks.$index.name"] = ['required', 'string', 'max:255'];
            $rules["new_ranks.$index.salary"] = ['required', 'integer', 'min:0', 'max:1000000'];
            $rules["new_ranks.$index.order"] = ['required', 'integer'];
            $rules["new_ranks.$index.requires_exam"] = ['nullable', 'boolean'];
            $rules["new_ranks.$index.minimum_successful_weeks"] = ['required', 'integer', 'min:0', 'max:100'];
            $rules["new_ranks.$index.is_leader"] = ['nullable', 'boolean'];

            $messages["new_ranks.$index.name.required"] = 'Az új rang neve nem lehet üres.';
            $messages["new_ranks.$index.salary.required"] = 'Az új rang fizetése nem lehet üres.';
            $messages["new_ranks.$index.salary.integer"] = 'A fizetésnek egy pozitív egész számnak kell lennie.';
            $messages["new_ranks.$index.minimum_successful_weeks.required"] = 'A minimum sikeres hetek száma nem lehet üres.';
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
        DB::table('admin_logs')->insert([
            'user_id' => Auth::user()->id,
            'didWhat' => 'Frissítette a(z) ' . $label . ' értékét (' . $oldValue . ' --> ' . $newValue . ').',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
