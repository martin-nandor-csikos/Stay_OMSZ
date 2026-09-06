<?php

namespace App\Http\Controllers;

use App\Models\Inactivity;
use App\Enums\InactivityStatus;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class InactivityController extends Controller
{
    /**
     * @var AdminController
     */
    protected $adminController;

    /**
     * InactivityController constructor.
     */
    public function __construct()
    {
        $this->adminController = new AdminController();
    }

    /**
     * Show the list of inactivities for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $inactivities = DB::table('inactivities')
            ->select('id', 'begin', 'end', 'reason', 'status')
            ->where('user_id', '=', $request->user()->id)
            ->get();

        // Reformat the begin and end dates
        foreach ($inactivities as $inactivity) {
            $inactivity->begin = Carbon::parse($inactivity->begin)->format('Y.m.d');
            $inactivity->end = Carbon::parse($inactivity->end)->format('Y.m.d');
        }

        return view('inactivity.view_inactivity', [
            'inactivities' => $inactivities,
        ]);
    }

    /**
     * Show the Create new inactivity form.
     *
     * @return \Illuminate\View\View
     */
    public function createInactivityView()
    {
        return view('inactivity.create_inactivity');
    }

    /**
     * Store a newly created inactivity in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeNewInactivity(Request $request)
    {
        $this->validateInactivity($request);

        $begin = new DateTime($request->begin);
        $end = new DateTime($request->end);
        $userId = $request->user()->id;

        $inactivity['begin'] = $begin->format('Y-m-d H:i:s');
        $inactivity['end'] = $end->format('Y-m-d H:i:s');
        $inactivity['reason'] = $request->reason;
        $inactivity['user_id'] = $userId;

        Inactivity::create($inactivity);

        return redirect()->route('inactivity.index')->with('successful-creation', 'Az inaktivitási kérelem sikeresen létrehozva.');
    }

    /**
     * Remove an inactivity request as a user.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteInactivity(int $id)
    {
        try {
            $inactivity = Inactivity::findOrFail($id);

            if ($inactivity->status != InactivityStatus::Accepted->value) {
                $inactivity->delete();

                return to_route('inactivity.index')->with('successful-deletion', 'Az inaktivitás törlése sikeres.');
            }
            return to_route('inactivity.index')->with('unsuccessful-deletion', 'Az inaktivitás törlése sikertelen.');
        } catch (\Throwable $th) {
            return to_route('inactivity.index')->with('unsuccessful-deletion', 'Az inaktivitás törlése sikertelen.');
        }
    }

    /**
     * Accept an inactivity request.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptInactivity($id)
    {
        if ($this->modifyInactivityStatus($id, InactivityStatus::Accepted)) {
            return Redirect::route('admin.index')->with('updateinactivity-success', 'Az inaktivitási kérelem sikeresen elfogadva.');
        }

        return Redirect::route('admin.index')->with('updateinactivity-failed', 'Az inaktivitási kérelem elfogadása meghiúsult.');
    }

    /**
     * Decline an inactivity request.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function declineInactivity($id)
    {
        if ($this->modifyInactivityStatus($id, InactivityStatus::Declined)) {
            return Redirect::route('admin.index')->with('updateinactivity-success', 'Az inaktivitási kérelem sikeresen elutasítva.');
        }

        return Redirect::route('admin.index')->with('updateinactivity-failed', 'Az inaktivitási kérelem elutasítása meghiúsult.');
    }

    /**
     * Delete an inactivity request as an admin.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteInactivityAsAdmin(string $id)
    {
        $inactivity = Inactivity::findOrFail($id);
        $userId = $inactivity->user_id;
        try {
            $characterName = $this->adminController->getCharacterNameById($userId);
            $inactivity->delete();
            $this->adminController->logAdminAction('Kitörölte a(z) ' . $characterName . ' (Inaktivitás ID: ' . $id . ') felhasználó inaktivitási kérelmét');

            return Redirect::route('admin.index')->with('destroyinactivity-success', 'Az inaktivitási kérelem sikeresen törölve.');
        } catch (\Throwable $th) {
            return Redirect::route('admin.index')->with('destroyinactivity-failed', 'Az inaktivitási kérelem törlése meghiúsult.');
        }
    }

    /**
     * Modify the status of an inactivity request.
     *
     * @param string $id
     * @param InactivityStatus $status
     * @return bool
     */
    private function modifyInactivityStatus(string $id, InactivityStatus $status)
    {
        try {
            $inactivity = Inactivity::findOrFail($id);
            DB::table('inactivities')
                ->where('id', $id)
                ->update(['status' => $status->value]);

            $characterName = $this->adminController->getCharacterNameById($inactivity->user_id);
            $this->adminController->logAdminAction('Frissítette ' . $characterName . ' inaktivitási kérelmét (' . $inactivity->status . ' -> ' . $status->value . ')');

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Validate the inactivity request data.
     *
     * @param \Illuminate\Http\Request $request
     */
    private function validateInactivity(Request $request)
    {
        $request->validate(
            [
                'begin' => ['required', 'date'],
                'end' => ['required', 'date', 'after_or_equal:begin'],
                'reason' => ['required', 'string', 'max:255'],
            ],
            [
                'begin.required' => 'Az inaktivitás kezdete mező nem lehet üres.',
                'begin.date' => 'Az inaktivitás kezdete érvényes dátum kell legyen.',

                'end.required' => 'Az inaktivitás vége nem lehet üres.',
                'end.date' => 'Az inaktivitás vége érvényes dátum kell legyen.',
                'end.after_or_equal' => 'Az inaktivitás vége nem lehet korábbi, mint a kezdete.',

                'reason.required' => 'Az indok mező nem lehet üres.',
                'reason.string' => 'Az indok csak szöveg lehet.',
                'reason.max' => 'Az indok mezőbe maximum 255 karaktert írhatsz.',
            ],
        );
    }
}
