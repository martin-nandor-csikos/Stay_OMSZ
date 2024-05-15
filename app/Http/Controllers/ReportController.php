<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    { 
        $reports = DB::table('reports')
            ->select('id', 'price', 'diagnosis', 'withWho', 'img', 'created_at')
            ->where('user_id', '=', $request->user()->id)
            ->get();

        return view('report.view_report', [
            'reports' => $reports,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('report.create_report');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $report = $request->validate([
            'price' => ['required', 'integer', 'max:300000'],
            'diagnosis' => ['required', 'string', 'max:100'],
            'withWho' => ['nullable', 'string', 'max:100'],
            'img' => ['required', 'url', 'max:100'],
        ], [
            'price.required' => 'Az ár nem lehet üres.',
            'price.integer' => 'Az árnak egy pozitív egész számnak kell lennie.',
            'price.max' => 'Az árnak maximum 6 számjegyű számnak kell lennie.',

            'diagnosis.required' => 'A diagnózis nem lehet üres.',
            'diagnosis.string' => 'A diagnózis csak szöveg lehet.',
            'diagnosis.max' => 'A diagnózis maximum 100 karakterből állhat.',

            'withWho.string' => 'A társ mezőben csak szöveg lehet.',

            'img.required' => 'A kép megadása kötelező.',
            'img.url' => 'A képnek érvényes URL-nek kell lennie.',
            'img.max' => 'A kép URL-je maximum 100 karakterből állhat.',
        ]);

        $report['user_id'] = $request->user()->id;
        $createdReport = Report::create($report);

        return redirect()->route('reports.create')->with('successful-creation', 'A jelentés beadása sikeres.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        //
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        $validatedData = $request->validate([
            'price' => ['required', 'integer', 'max:300000'],
            'diagnosis' => ['required', 'string'],
            'withWho' => ['nullable', 'string'],
            'img' => ['required', 'url'],
        ], [
            'price.required' => 'Az ár nem lehet üres.',
            'price.integer' => 'Az árnak egy pozitív egész számnak kell lennie.',
            'price.max' => 'Az árnak maximum 6 számjegyű számnak kell lennie.',

            'diagnosis.required' => 'A diagnózis nem lehet üres.',
            'diagnosis.string' => 'A diagnózis csak szöveg lehet.',
            'diagnosis.max' => 'A diagnózis maximum 100 karakterből állhat.',

            'withWho.string' => 'A társ mezőben csak szöveg lehet.',

            'img.required' => 'A kép megadása kötelező.',
            'img.url' => 'A képnek érvényes URL-nek kell lennie.',
            'img.max' => 'A kép URL-je maximum 100 karakterből állhat.',
        ]);
        
        $oldReport = Report::findOrFail($report->id);
        $oldReport->price = $report->price;
        $oldReport->diagnosis = $report->diagnosis;
        $oldReport->withWho = $report->withWho;
        $oldReport->img = $report->img;
        $oldReport->save();

        return redirect()->route('reports.index')->with('successful-update', 'A jelentés frissítése sikeres.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return to_route('reports.index')->with('successful-deletion', 'A jelentés törlése sikeres.');
    }
}
