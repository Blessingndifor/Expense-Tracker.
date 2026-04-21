<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display the initial page OR return JSON data.
     * If the request is from JavaScript (AJAX), return JSON.
     * Otherwise, return the HTML page.
     */
    public function index(Request $request)
{
    $expenses = \App\Models\Expense::latest()->get();

    // If the browser/JS asks for JSON, give it JSON
    if ($request->wantsJson()) {
        return response()->json($expenses);
    }

    // Otherwise, show the HTML page
    return view('expenses.index', compact('expenses'));
}

    // CREATE: Saves data and returns the new item as JSON
  public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string',
        'amount' => 'required|numeric',
        'date'  => 'required|date', // 👈 Add this validation
    ]);

    $expense = Expense::create($validated);

    return response()->json($expense, 201);
}



    // UPDATE: Finds the item, updates it, and returns JSON
    public function update(Request $request, $id)
        {
            $expense = Expense::findOrFail($id);

            $validated = $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'date'  => 'required|date',
            ]);

            $expense->update($validated);

            return response()->json($expense);
        }

    // DELETE: Removes the item and returns a success message
    public function destroy($id)
    {
        Expense::destroy($id);

        return response()->json(['message' => 'Deleted successfully']);
    }
}
