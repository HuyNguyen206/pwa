<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use App\Models\Reading;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    public function store(Request $request, Meter $meter) {
        $data = $request->validate([
            'value' => ['required','numeric'],
            'noted_at' => ['required','date'],
            'notes' => ['nullable','string'],
        ]);

        $data['meter_id'] = $meter->id;

        Reading::create($data);

        return back()->with('status','Reading added');
    }

    public function edit(Meter $meter, Reading $reading) {
        return view('readings.edit', compact('meter','reading'));
    }

    public function update(Request $request, Meter $meter, Reading $reading) {
        $data = $request->validate([
            'value' => ['required','numeric'],
            'noted_at' => ['required','date'],
            'notes' => ['nullable','string'],
        ]);

        $reading->update($data);

        return redirect()->route('meters.show', $meter)->with('status','Reading updated');
    }

    public function destroy(Meter $meter, Reading $reading) {
        $reading->delete();
        
        return back()->with('status','Reading deleted');
    }
}
