<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Http\Controllers\TicketServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * @var TicketServiceController
     */
    protected $ticketServiceController;

    /**
     * ReportController constructor.
     */
    public function __construct()
    {
        $this->ticketServiceController = new TicketServiceController();
    }

    /**
     * Display the view for the reports index page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userReports = $this->getUserReports($request->user()->id);

        // Format the created_at and updated_at date for each report
        foreach ($userReports as $report) {
            $report->created_at = \Illuminate\Support\Carbon::parse($report->created_at)->format('Y.m.d H:i');
            $report->updated_at = \Illuminate\Support\Carbon::parse($report->updated_at)->format('Y.m.d H:i');
        }

        return view('report.view_reports', [
            'userReports' => $userReports,
        ]);
    }

    /**
     * Display the view for creating a new report.
     *
     * @return \Illuminate\View\View
     */
    public function createReportView()
    {
        $services = $this->ticketServiceController->getServicesQuery();

        return view('report.create_report', [
            'services' => $services
        ]);
    }

    /**
     * Store the newly created report in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeNewReport(Request $request)
    {
        $this->validateReport($request);

        $report['user_id'] = $request->user()->id;
        $report['price'] = $request->cost;
        $report['diagnosis'] = $request->services;
        $report['withWho'] = $request->withWho;
        $report['img'] = $request->img;

        Report::create($report);

        return redirect()->route('reports.createReportView')->with('successful-creation', 'A jelentés beadása sikeres.');
    }

    /**
     * Show the form for editing the authenticated user's own report.
     *
     * @param string $id
     * @return \Illuminate\View\View
     */
    public function editReportView(string $id)
    {
        $report = Report::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $services = $this->ticketServiceController->getServicesQuery();
        $selectedServices = array_map('trim', explode(',', $report->diagnosis));

        return view('report.update_report', [
            'report' => $report,
            'services' => $services,
            'selectedServices' => $selectedServices,
        ]);
    }

    /**
     * Update the authenticated user's own report. Only saves if a field actually changed.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateReport(Request $request, string $id)
    {
        $report = Report::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $this->validateReportUpdate($request, $report->id);

        $changed = false;

        if ((int) $request->cost !== (int) $report->price) {
            $report->price = $request->cost;
            $changed = true;
        }

        if ($request->services !== $report->diagnosis) {
            $report->diagnosis = $request->services;
            $changed = true;
        }

        if ($request->withWho !== $report->withWho) {
            $report->withWho = $request->withWho;
            $changed = true;
        }

        if ($request->img !== $report->img) {
            $report->img = $request->img;
            $changed = true;
        }

        if (!$changed) {
            return redirect()->route('reports.index')->with('no-changes', 'Nem történt változás.');
        }

        $report->save();

        return redirect()->route('reports.index')->with('successful-update', 'A jelentés frissítése sikeres.');
    }

    /**
     * Delete the specified report.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteReport($id)
    {
        try {
            $report = Report::findOrFail($id);
            $report->delete();

            return to_route('reports.index')->with('successful-deletion', 'A jelentés törlése sikeres.');
        } catch (\Throwable $th) {
            return to_route('reports.index')->with('unsuccessful-deletion', 'A jelentés törlése sikertelen.');
        }
    }

    /**
     * Get the reports for the given user.
     *
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getUserReports($userId)
    {
        return DB::table('reports')
            ->select('id', 'price', 'diagnosis', 'withWho', 'img', 'created_at', 'updated_at')
            ->where('user_id', '=', $userId)
            ->get();
    }

    /**
     * Get the reports for the given user from the previous week.
     *
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getUserReportsFromClosedWeek($userId)
    {
        return DB::table('reports_closed')
            ->select('id', 'price', 'diagnosis', 'withWho', 'img', 'created_at')
            ->where('user_id', '=', $userId)
            ->get();
    }

    /**
     * Get the count of reports for the current week
     *
     * @return int
     */
    public function getReportCountForCurrentWeek()
    {
        return DB::table('reports')
            ->select(DB::raw('count(id) as reportCount'),)
            ->value('reportCount');
    }

    /**
     * Validate the report input data.
     *
     * @param \Illuminate\Http\Request $request
     */
    private function validateReport(Request $request)
    {
        $request->validate([
            'cost' => ['required', 'integer', 'max:300000', 'min:0'],
            'services' => ['required', 'string'],
            'withWho' => ['nullable', 'string'],
            'img' => ['required', 'url', 'unique:reports'],
        ], [
            'cost.required' => 'Az ár nem lehet üres.',
            'cost.integer' => 'Az árnak egy pozitív egész számnak kell lennie.',
            'cost.max' => 'Az ár maximum $300.000 lehet.',
            'cost.min' => 'Az ár minimum $0 lehet.',

            'services.required' => 'Az ellátás mező nem lehet üres.',
            'services.string' => 'A ellátás mezőben csak szöveg lehet.',
            'services.max' => 'Az ellátás mező maximum 100 karakterből állhat.',

            'withWho.string' => 'A társ mezőben csak szöveg lehet.',

            'img.required' => 'A kép megadása kötelező.',
            'img.url' => 'A képnek érvényes URL-nek kell lennie.',
            'img.max' => 'A kép URL-je maximum 100 karakterből állhat.',
            'img.unique' => 'Ezt a képet már feltöltötted.',
        ]);
    }

    /**
     * Validate the report input data when updating an existing report.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $reportId
     */
    private function validateReportUpdate(Request $request, $reportId)
    {
        $request->validate([
            'cost' => ['required', 'integer', 'max:300000', 'min:0'],
            'services' => ['required', 'string'],
            'withWho' => ['nullable', 'string'],
            'img' => ['required', 'url', Rule::unique('reports')->ignore($reportId)],
        ], [
            'cost.required' => 'Az ár nem lehet üres.',
            'cost.integer' => 'Az árnak egy pozitív egész számnak kell lennie.',
            'cost.max' => 'Az ár maximum $300.000 lehet.',
            'cost.min' => 'Az ár minimum $0 lehet.',

            'services.required' => 'Az ellátás mező nem lehet üres.',
            'services.string' => 'A ellátás mezőben csak szöveg lehet.',
            'services.max' => 'Az ellátás mező maximum 100 karakterből állhat.',

            'withWho.string' => 'A társ mezőben csak szöveg lehet.',

            'img.required' => 'A kép megadása kötelező.',
            'img.url' => 'A képnek érvényes URL-nek kell lennie.',
            'img.max' => 'A kép URL-je maximum 100 karakterből állhat.',
            'img.unique' => 'Ezt a képet már feltöltötted.',
        ]);
    }
}
