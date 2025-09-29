<?php

namespace App\Http\Controllers;

use App\Models\Meter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MeterController extends Controller
{
     public function index() {
        $meters = Meter::withCount('readings')->orderBy('name')->paginate(15);
        return view('meters.index', compact('meters'));
    }

    public function create() { return view('meters.create'); }

    public function store(Request $request) {
        $data = $request->validate([
            'code' => ['required','string','max:50', Rule::unique('meters','code')],
            'name' => ['required','string','max:255'],
            'unit' => ['required','string','max:20'],
            'location_lat' => ['nullable','numeric','between:-90,90'],
            'location_lng' => ['nullable','numeric','between:-180,180'],
        ]);
        $meter = Meter::create($data);
        return redirect()->route('meters.show', $meter)->with('status','Meter created');
    }

    public function show(Meter $meter) {
        $meter->load(['readings' => fn($q) => $q->latest('noted_at')]);
        return view('meters.show', compact('meter'));
    }

    public function edit(Meter $meter) { return view('meters.edit', compact('meter')); }

    public function update(Request $request, Meter $meter) {
        $data = $request->validate([
            'code' => ['required','string','max:50', Rule::unique('meters','code')->ignore($meter->id)],
            'name' => ['required','string','max:255'],
            'unit' => ['required','string','max:20'],
            'location_lat' => ['nullable','numeric','between:-90,90'],
            'location_lng' => ['nullable','numeric','between:-180,180'],
        ]);
        $meter->update($data);
        return redirect()->route('meters.show', $meter)->with('status','Meter updated');
    }

    public function destroy(Meter $meter) {
        $meter->delete();
        return redirect()->route('meters.index')->with('status','Meter deleted');
    }
}
