<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MetaFieldSeries;
use App\MetaField;

class AdminMetaFieldSeriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $series = MetaFieldSeries::with('meta_field')->paginate(50);
        return view('admin.meta_field_series.index', ['series' => $series]);
    }

    public function create()
    {
        $meta_fields = MetaField::all();
        return view('admin.meta_field_series.form', ['meta_fields' => $meta_fields, 'series' => new MetaFieldSeries()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'meta_field_id' => 'required|exists:meta_fields,id',
        ]);
        $s = new MetaFieldSeries();
        $s->meta_field_id = $request->input('meta_field_id');
        $s->prefix = $request->input('prefix');
        $s->date_format = $request->input('date_format');
        $s->next_sequence = intval($request->input('next_sequence')) ?: 1;
        $s->pad_length = intval($request->input('pad_length')) ?: 0;
        $s->save();
        return redirect('/admin/meta-field-series');
    }

    public function edit($id)
    {
        $series = MetaFieldSeries::findOrFail($id);
        $meta_fields = MetaField::all();
        return view('admin.meta_field_series.form', ['series' => $series, 'meta_fields' => $meta_fields]);
    }

    public function update(Request $request, $id)
    {
        $s = MetaFieldSeries::findOrFail($id);
        $s->prefix = $request->input('prefix');
        $s->date_format = $request->input('date_format');
        $s->next_sequence = intval($request->input('next_sequence')) ?: $s->next_sequence;
        $s->pad_length = intval($request->input('pad_length')) ?: $s->pad_length;
        $s->save();
        return redirect('/admin/meta-field-series');
    }

    public function destroy($id)
    {
        $s = MetaFieldSeries::findOrFail($id);
        $s->delete();
        return redirect('/admin/meta-field-series');
    }
}
