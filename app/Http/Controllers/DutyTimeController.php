<?php

namespace App\Http\Controllers;

use App\Models\DutyTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;

class DutyTimeController extends Controller
{
    /**
     * Display the list of duty times for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $dutyTimes = DB::table('duty_times')
            ->select('id', 'begin', 'end', 'minutes', 'updated_at')
            ->where('user_id', '=', $request->user()->id)
            ->get();

        foreach ($dutyTimes as $dutyTime) {
            $dutyTime->begin = Carbon::parse($dutyTime->begin)->format('Y.m.d H:i');
            $dutyTime->end = Carbon::parse($dutyTime->end)->format('Y.m.d H:i');
            $dutyTime->updated_at = Carbon::parse($dutyTime->updated_at)->format('Y.m.d H:i');
        }

        return view('duty_time.view_duty', [
            'dutyTimes' => $dutyTimes,
        ]);
    }

    /**
     * Show the add duty form.
     *
     * @return \Illuminate\View\View
     */
    public function createDutyView()
    {
        return view('duty_time.create_duty');
    }

    /**
     * Store a newly created duty
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeNewDuty(Request $request)
    {
        $this->validateDuty($request);

        $begin = new DateTime($request->begin);
        $end = new DateTime($request->end);
        $id = $request->user()->id;

        $duty['begin'] = $begin->format('Y-m-d H:i:s');
        $duty['end'] = $end->format('Y-m-d H:i:s');
        $duty['user_id'] = $id;

        // Calculate the duration in minutes
        $interval = $begin->diff($end);
        $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

        DutyTime::create(array_merge($duty, ['minutes' => $minutes]));

        return redirect()->route('duty_time.createDutyView')->with('successful-creation', 'A szolgálat felvitele sikeres.');
    }

    /**
     * Show the form for editing the authenticated user's own duty.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function editDutyView(string $id)
    {
        $duty = DutyTime::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $duty->begin = $duty->begin->format('Y-m-d\TH:i');
        $duty->end = $duty->end->format('Y-m-d\TH:i');

        return view('duty_time.update_duty', [
            'duty' => $duty,
        ]);
    }

    /**
     * Update the authenticated user's own duty. Only saves if a field actually changed.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDuty(Request $request, string $id)
    {
        $duty = DutyTime::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $this->validateDuty($request);

        $begin = new DateTime($request->begin);
        $end = new DateTime($request->end);

        $newBegin = $begin->format('Y-m-d H:i:s');
        $newEnd = $end->format('Y-m-d H:i:s');

        if ($newBegin === $duty->begin->format('Y-m-d H:i:s') && $newEnd === $duty->end->format('Y-m-d H:i:s')) {
            return redirect()->route('duty_time.index')->with('no-changes', 'Nem történt változás.');
        }

        // Recalculate the duration in minutes
        $interval = $begin->diff($end);
        $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

        $duty->begin = $newBegin;
        $duty->end = $newEnd;
        $duty->minutes = $minutes;
        $duty->save();

        return redirect()->route('duty_time.index')->with('successful-update', 'A szolgálat frissítése sikeres.');
    }

    /**
     * Remove duty from the database
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteDuty($id)
    {
        try {
            $duty = DutyTime::findOrFail($id);
            $duty->delete();

            (new DashboardController())->refreshPersonalStatistics();

            return to_route('duty_time.index')->with('successful-deletion', 'A szolgálat törlése sikeres.');
        } catch (\Throwable $th) {
            return to_route('duty_time.index')->with('unsuccessful-deletion', 'A szolgálat törlése sikertelen.');
        }
    }

    /**
     * Delete a duty as an admin
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteDutyAsAdmin(string $id)
    {
        $duty = DutyTime::findOrFail($id);
        $userId = $duty->user_id;
        try {
            $characterName = $this->getCharacterNameById($userId);

            $duty->delete();

            (new DashboardController())->refreshPersonalStatistics();

            $this->logAdminAction('Kitörölte a(z) ' . $characterName . ' (Szolgálat ID: ' . $id . ') felhasználó szolgálatát');

            return Redirect::route('admin.viewUserDuty', $userId)->with('successful-user-duty-deletion', 'A felhasználó szolgálatának törlése sikeres.');
        } catch (\Throwable $th) {
            return Redirect::route('admin.viewUserDuty', $userId)->with('unsuccessful-user-duty-deletion', 'A felhasználó szolgálatának törlése sikertelen.');
        }
    }

    /**
     * Get the count of duties for the current week
     *
     * @return int
     */
    public function getDutyCountForCurrentWeek()
    {
        return DB::table('duty_times')->select(DB::raw('count(id) as dutyCount'))->value('dutyCount');
    }

    /**
     * Validate the duty time input.
     *
     * @param \Illuminate\Http\Request $request
     */
    private function validateDuty(Request $request)
    {
        $endDate = Carbon::parse($request->end);
        $oneDayAgo = $endDate->subDays(1);

        $request->validate(
            [
                'begin' => ['required', 'date', 'before_or_equal:' . now(), 'after_or_equal:' . $oneDayAgo],
                'end' => ['required', 'date', 'after_or_equal:begin', 'before_or_equal:' . now()],
            ],
            [
                'begin.required' => 'A kezdés ideje mező nem lehet üres.',
                'begin.date' => 'A kezdés érvényes dátum kell legyen.',
                'begin.before_or_equal' => 'Nem adhatsz meg későbbi kezdeti időpontot, mint a mostani.',
                'begin.after_or_equal' => 'Maximum 24 óra szolgálatot veszünk figyelembe.',

                'end.required' => 'A leadás ideje mező nem lehet üres.',
                'end.date' => 'A leadás érvényes dátum kell legyen.',
                'end.after_or_equal' => 'Hamarabb akarod leadni a szolgálatot, mint ahogy elkezdted.',
                'end.before_or_equal' => 'Nem adhatsz meg későbbi leadási időpontot, mint a mostani.',
            ],
        );
    }
}
