<?php

namespace App\Http\Controllers;

use App\Models\DutyTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class DutyTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $dutyTimes = DB::table('duty_times')
            ->select('id', 'begin', 'end', 'minutes')
            ->where('user_id', '=', $request->user()->id)
            ->get();

        return view('duty_time.view_duty', [
            'dutyTimes' => $dutyTimes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('duty_time.create_duty');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Somehow I have to add 2 hours to now() in order for the before_or_equal to work. Otherwise now() would be 2 hours ago.
        // What the fuck
        $twoHoursLater = now()->addHours(2);
        $validatedData = $request->validate([
            'begin' => ['required', 'date', 'before_or_equal:' . $twoHoursLater],
            'end' => ['required', 'date', 'after_or_equal:begin', 'before_or_equal:' . $twoHoursLater],
        ], [
            'begin.required' => 'A kezdés ideje nem lehet üres.',
            'begin.date' => 'A kezdés érvényes dátum kell legyen.',
            'begin.before_or_equal' => 'A kezdési idő nem lehet későbbi, mint a jelenlegi idő.',

            'end.required' => 'A leadás ideje nem lehet üres.',
            'end.date' => 'A leadás érvényes dátum kell legyen.',
            'end.after_or_equal' => 'A leadási idő nem lehet hamarabb, mint a kezdési idő.',
            'end.before_or_equal' => 'A leadási idő nem lehet későbbi, mint a jelenlegi idő.',
        ]);

        $begin = new DateTime($request->begin);
        $end = new DateTime($request->end);
        $id = $request->user()->id;

        $duty['begin'] = $begin->format('Y-m-d H:i:s');
        $duty['end'] = $end->format('Y-m-d H:i:s');
        $duty['user_id'] = $id;
        
        $interval = $begin->diff($end);
        $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

        $createdDuty = DutyTime::create(array_merge($duty, ['minutes' => $minutes]));

        return redirect()->route('duty_time.create')->with('successful-creation', 'A szolgálat felvitele sikeres.');
    }


    /**
     * Display the specified resource.
     */
    public function show(DutyTime $dutyTime)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DutyTime $dutyTime)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DutyTime $dutyTime)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $duty = DutyTime::findOrFail($id);
            $duty->delete();
            
            return to_route('duty_time.index')->with('successful-deletion', 'A szolgálat törlése sikeres.');
        } catch (\Throwable $th) {
            return to_route('duty_time.index')->with('unsuccessful-deletion', 'A szolgálat törlése sikertelen.');
        }
    }
}
