<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Http\Controllers\TicketServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Format the created_at date for each report
        foreach ($userReports as $report) {
            $report->created_at = \Illuminate\Support\Carbon::parse($report->created_at)->format('Y.m.d H:i');
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
    public function create_report_view()
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
    public function store_new_report(Request $request)
    {
        $this->validateReport($request);

        $report['user_id'] = $request->user()->id;
        $report['price'] = $request->cost;
        $report['diagnosis'] = $request->services;
        $report['withWho'] = $request->withWho;
        $report['img'] = $request->img;

        Report::create($report);

        return redirect()->route('reports.create_report_view')->with('successful-creation', 'A jelentés beadása sikeres.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $report = Report::findOrFail($id);

        return view('report.update_report', [
            'report' => $report,
        ]);
    }

    /**
     * Update the specified report
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Report $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_report(Request $request, Report $report)
    {
        /*
        $request->validate([
            'price' => ['required', 'integer', 'max:300000', 'min:0'],
            'diagnosis' => ['required', 'string'],
            'withWho' => ['nullable', 'string'],
            'img' => ['required', 'url', 'unique:reports'],
        ], [
            'price.required' => 'Az ár nem lehet üres.',
            'price.integer' => 'Az árnak egy pozitív egész számnak kell lennie.',
            'price.max' => 'Az ár maximum $300.000 lehet.',
            'price.min' => 'Az ár minimum $0 lehet.',

            'diagnosis.required' => 'A diagnózis nem lehet üres.',
            'diagnosis.string' => 'A diagnózis csak szöveg lehet.',
            'diagnosis.max' => 'A diagnózis maximum 100 karakterből állhat.',

            'withWho.string' => 'A társ mezőben csak szöveg lehet.',

            'img.required' => 'A kép megadása kötelező.',
            'img.url' => 'A képnek érvényes URL-nek kell lennie.',
            'img.max' => 'A kép URL-je maximum 100 karakterből állhat.',
            'img.unique' => 'Ezt a képet már feltöltötted.',
        ]);

        $oldReport = Report::findOrFail($report->id);
        $oldReport->price = $report->price;
        $oldReport->diagnosis = $report->diagnosis;
        $oldReport->withWho = $report->withWho;
        $oldReport->img = $report->img;
        $oldReport->save();

        return redirect()->route('reports.index')->with('successful-update', 'A jelentés frissítése sikeres.');
        */
    }

    /**
     * Delete the specified report.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete_report($id)
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
     * Get the reports for the authenticated user.
     *
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    private function getUserReports($userId)
    {
        return DB::table('reports')
            ->select('id', 'price', 'diagnosis', 'withWho', 'img', 'created_at')
            ->where('user_id', '=', $userId)
            ->get();
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

            'services.required' => 'A diagnózis nem lehet üres.',
            'services.string' => 'A diagnózis csak szöveg lehet.',
            'services.max' => 'A diagnózis maximum 100 karakterből állhat.',

            'withWho.string' => 'A társ mezőben csak szöveg lehet.',

            'img.required' => 'A kép megadása kötelező.',
            'img.url' => 'A képnek érvényes URL-nek kell lennie.',
            'img.max' => 'A kép URL-je maximum 100 karakterből állhat.',
            'img.unique' => 'Ezt a képet már feltöltötted.',
        ]);
    }
}
