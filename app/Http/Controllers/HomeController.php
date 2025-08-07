<?php

namespace App\Http\Controllers;

use App\Models\Cat;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $cats = Cat::with([
            'user:id,name',
            'childrenRecursive.user:id,name',
            'titleTranslation'
        ])->whereNull('parent_id')
            ->whereFollow(1)
            ->orderBy('ord','asc')
            ->get();
        return view('welcome', compact('cats'));
    }
    public function updateOrder(Request $request)
    {
        $cat = Cat::findOrFail($request->id);

        $cat->update([
            'parent_id' => $request->parent_id,
            'ord' => $request->ord
        ]);

        return response()->json(['status' => 'updated']);
    }


}
