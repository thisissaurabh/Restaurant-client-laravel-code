<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        $login_user = $request->user();
        $pagedata =  $request->pageData;
        $expenses = Expense::where('user_id', $login_user->id)->paginate($pagedata ?? 10);
        return response()->json(['status' => 1, 'expense' => $expenses], 200);
    }


    public function store(Request $request)
    {
        $login_user = $request->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'person_name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);
        try {
            $expense = Expense::create([
                'title' => $request->title,
                'amount' => $request->amount,
                'description' => $request->description,
                'person_name' => $request->person_name,
                'date' => $request->date,
                'user_id' => $login_user->id,
            ]);
            return response()->json(['status' => 1, 'message' => 'Expense created successfully', 'expense' => $expense], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error creating expense: ', 'error' => $e->getMessage()], 500);
        }
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        $login_user = $request->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'person_name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);
        try {
            $expense = Expense::where('id', $id)->where('user_id', $id)->first();
            if (!$expense) {
                return response()->json(['status' => 0, 'message' => 'Expense not found'], 404);
            }
            $expense->update($request->all());
            return response()->json(['status' => 1, 'message' => 'Expense updated successfully', 'expense' => $expense], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error updating expense: ', 'error' => $e->getMessage()], 500);
        }



        $expense->update($request->all());
        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Request $request, $id)
    {
        $login_user = $request->user();
        try {
            $expense = Expense::where('id', $id)->where('user_id', $login_user->user_id)->first();
            if (!$expense) {
                return response()->json(['status' => 0, 'message' => 'Unauthorized id'], 401);
            }
            $expense->delete();
            return response()->json(['status' => 1, 'message' => 'Expense deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error deleting expense: ', 'error' => $e->getMessage()], 500);
        }
    }

    /// report



    public function report(Request $request)
    {
        $login_user = $request->user();

        try {
            // Get optional filters from the request
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $highest = $request->input('highest');
            $lowest = $request->input('lowest');

            // Base query for monthly expenses
            $query = Expense::select(
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('MONTH(date) as month'),
                DB::raw('YEAR(date) as year')
            )->where('user_id', $login_user->id);

            // Apply date filter if provided
            if ($startDate && $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            // Group and order the results
            $monthlyExpenses = $query->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get();

            // Calculate the total amount of all expenses
            $totalExpensesQuery = Expense::where('user_id', $login_user->id);

            // Apply date filter if provided
            if ($startDate && $endDate) {
                $totalExpensesQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $totalExpenses = $totalExpensesQuery->sum('amount');

            // Prepare chart data
            $chartData = $monthlyExpenses->map(function ($expense) {
                return [
                    'month' => $expense->year . '-' . str_pad($expense->month, 2, '0', STR_PAD_LEFT),
                    'total_amount' => $expense->total_amount
                ];
            });

            // Apply highest/lowest filter if provided
            if ($highest) {
                $chartData = $chartData->sortByDesc('total_amount')->values();
            } elseif ($lowest) {
                $chartData = $chartData->sortBy('total_amount')->values();
            }

            if (count($chartData) > 0) {
                $chartData = $chartData;
            } else {
                $chartData = null;
            }

            $response = [
                'total_expenses' => $totalExpenses,
                'monthly_expenses' => $chartData
            ];

            return response()->json(['status' => 1, 'report' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error creating expense report', 'error' => $e->getMessage()], 500);
        }
    }
}
