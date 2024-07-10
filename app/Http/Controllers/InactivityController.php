<?php

namespace App\Http\Controllers;

use App\Models\Inactivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use Carbon\Carbon;

class InactivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $inactivities = DB::table('inactivities')
            ->select('id', 'begin', 'end', 'reason', 'status')
            ->where('user_id', '=', $request->user()->id)
            ->get();

        return view('inactivity.view_inactivity', [
            'inactivities' => $inactivities,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inactivity.create_inactivity');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'begin' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:begin'],
            'reason' => ['required', 'string', 'max:255'],
        ], [
            'begin.required' => 'Az inaktivitás kezdete nem lehet üres.',
            'begin.date' => 'Az inaktivitás kezdete érvényes dátum kell legyen.',

            'end.required' => 'Az inaktivitás vége nem lehet üres.',
            'end.date' => 'Az inaktivitás vége érvényes dátum kell legyen.',
            'end.after_or_equal' => 'Az inaktivitás vége nem lehet hamarabb, mint az inaktivitás kezdete.',

            'reason.required' => 'Az indok mező nem lehet üres.',
            'reason.string' => 'Az indok csak szöveg lehet.',
            'reason.max' => 'Az indok mezőbe maximum 255 karaktert írhatsz.',
        ]);

        $begin = new DateTime($request->begin);
        $end = new DateTime($request->end);
        $id = $request->user()->id;

        $inactivity['begin'] = $begin->format('Y-m-d H:i:s');
        $inactivity['end'] = $end->format('Y-m-d H:i:s');
        $inactivity['reason'] = $request->reason;
        $inactivity['user_id'] = $id;

        $createdInactivity = Inactivity::create($inactivity);

        return redirect()->route('inactivity.index')->with('successful-creation', 'Az inaktivitási kérelem sikeresen létrehozva.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $inactivity = Inactivity::findOrFail($id);
            $inactivity->delete();
            
            return to_route('inactivity.index')->with('successful-deletion', 'Az inaktivitás törlése sikeres.');
        } catch (\Throwable $th) {
            return to_route('inactivity.index')->with('unsuccessful-deletion', 'Az inaktivitás törlése sikertelen.');
        }
    }
}
