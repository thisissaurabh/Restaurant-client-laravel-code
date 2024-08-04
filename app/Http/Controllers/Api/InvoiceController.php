<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\FoodList;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Expense;
use Exception;

class InvoiceController extends Controller
{
    public function generateInvoice(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();


            // echo    $this->generateUniqueBillNumber($user->id);
            // die;
            $validated = $request->validate([
                'cost_name' => 'nullable|string|max:255',
                'cost_address' => 'nullable|string|max:255',
                'cost_number' => 'nullable|string|max:20',
                'bill_number' => 'required|integer',
                'table_number' => 'required|integer',
                'date_time' => 'required|date',
                'waiter_code' => 'nullable|string|max:50',
                'cashier_name' => 'nullable|string|max:100',
                'sub_total' => 'required|numeric',
                'sgst' => 'nullable|numeric',
                'cgst' => 'nullable|numeric',
                'total_amount' => 'required|numeric',
                'payment_mode' => 'required|integer|in:1,2,3',
                'message' => 'nullable|string|max:255',
                'food_list' => 'required|array',
                'food_list.*.food_id' => 'required|integer|exists:foods,id',
                'food_list.*.item' => 'required|string|max:255',
                'food_list.*.quantity' => 'required|integer',
                'food_list.*.price_per_unit' => 'required|numeric',
                'food_list.*.total_price' => 'required|numeric'
            ]);

            // Generate a unique bill number
            $validated['bill_number'] = $this->generateUniqueBillNumber($user->id);

            $invoice = new Invoice();
            $invoice->user_id = $user->id;
            $invoice->cost_name = $validated['cost_name'];
            $invoice->cost_address = $validated['cost_address'];
            $invoice->cost_number = $validated['cost_number'];
            $invoice->bill_number = $validated['bill_number'];
            $invoice->table_number = $validated['table_number'];
            $invoice->date_time = $validated['date_time'];
            $invoice->waiter_code = $validated['waiter_code'];
            $invoice->cashier_name = $validated['cashier_name'];
            $invoice->sub_total = $validated['sub_total'];
            $invoice->sgst = $validated['sgst'];
            $invoice->cgst = $validated['cgst'];
            $invoice->total_amount = $validated['total_amount'];
            $invoice->payment_mode = $validated['payment_mode'];
            $invoice->message = $validated['message'];
            $invoice->save();

            foreach ($validated['food_list'] as $foodItem) {
                $foodList = new FoodList();
                $foodList->invoice_id = $invoice->id;
                $foodList->food_id = $foodItem['food_id'];
                $foodList->item_name = $foodItem['item'];
                $foodList->quantity = $foodItem['quantity'];
                $foodList->price_per_unit = $foodItem['price_per_unit'];
                $foodList->total_price = $foodItem['total_price'];
                $foodList->save();
            }

            DB::commit();

            return response()->json(['status' => 1, 'invoice' => $invoice->load('foodList')], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'error' => 'Model not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 0, 'error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    private function generateUniqueBillNumber($userId)
    {
        $latestInvoice = Invoice::where('user_id', $userId)->orderBy('bill_number', 'desc')->first();
        if ($latestInvoice) {
            $latestBillNumber = (int)$latestInvoice->bill_number;
            $newBillNumber = $latestBillNumber + 1;
        } else {

            $newBillNumber = 1;
        }

        return str_pad($newBillNumber, 10, '0', STR_PAD_LEFT);
    }


    public function getInvoice($id)
    {
        try {
            $invoice = Invoice::with('foodList', 'user')->findOrFail($id);
            $user = $invoice->user;
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'restName' => $user->restName,
                'restAddress' => $user->restAddress,
                'tin' => $user->tin
            ];
            $invoice->restrent_data = $userData;
            unset($invoice->user);

            return response()->json(['status' => 1, 'invoice' => $invoice], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 0, 'error' => 'Invoice not found', 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    public function getAllInvoices(Request $request)
    {
        $loginUser = $request->user();

        try {
            $query = Invoice::with('foodList', 'user')
                ->where('user_id', $loginUser->id)
                ->orderBy('id', 'asc');

            if ($request->has('start_date') && $request->has('end_date')  && $request->input('start_date') != '' && $request->input('end_date') != '') {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                $query->whereBetween('date_time', [$startDate, $endDate]);
            }

            if ($request->has('customer_name') && $request->input('customer_name') != '') {
                $customerName = $request->input('customer_name');
                $query->where('cost_name', 'LIKE', "%$customerName%");
            }

            if ($request->has('customer_mobile') && $request->input('customer_mobile') != '') {
                $customerMobile = $request->input('customer_mobile');
                $query->where('cost_number', 'LIKE', "%$customerMobile%");
            }

            if ($request->has('invoice_no') && $request->input('invoice_no') != '') {
                $invoiceNo = $request->input('invoice_no');
                $query->where('bill_number', 'LIKE', "%$invoiceNo%");
            }

            $invoices = $query->get();

            $invoices->transform(function ($invoice) {
                $user = $invoice->user;
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'restName' => $user->restName,
                    'restAddress' => $user->restAddress,
                    'tin' => $user->tin
                ];
                $invoice->restrent_data = $userData;
                unset($invoice->user);

                return $invoice;
            });

            return response()->json(['status' => 1, 'invoices' => $invoices], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    public function getSalesReport(Request $request)
    {
        $loginUser = $request->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        try {
            // Initialize query
            $query = Invoice::select(
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(sub_total) as sub_total'),
                DB::raw('SUM(sgst + cgst) as total_taxes'),
                DB::raw('SUM(total_amount) - SUM(sub_total + sgst + cgst) as total_discount'),
                DB::raw('SUM(CASE WHEN payment_mode = 1 THEN total_amount ELSE 0 END) as amount_received_cash'),
                DB::raw('SUM(CASE WHEN payment_mode = 2 THEN total_amount ELSE 0 END) as amount_received_card'),
                DB::raw('SUM(CASE WHEN payment_mode = 3 THEN total_amount ELSE 0 END) as amount_received_upi'),
                DB::raw('MONTH(date_time) as month'),
                DB::raw('YEAR(date_time) as year')
            )
                ->where('user_id', $loginUser->id)
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc');

            // Apply date filters if provided
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('date_time', [$startDate, $endDate]);
            }

            // Fetch sales data
            $salesData = $query->get();

            // Fetch total expenses
            $expenseQuery = Expense::where('user_id', $loginUser->id);

            if (!empty($startDate) && !empty($endDate)) {
                $expenseQuery->whereBetween('date', [$startDate, $endDate]);
            }

            $totalExpenses = $expenseQuery->sum('amount');

            // Calculate weekly and last 6 months sales
            $now = \Carbon\Carbon::now();
            $sixMonthsAgo = $now->copy()->subMonths(6);
            $oneWeekAgo = $now->copy()->subWeek();

            $weeklySalesQuery = Invoice::select(
                DB::raw('SUM(total_amount) as total_amount')
            )
                ->where('user_id', $loginUser->id)
                ->whereBetween('date_time', [$oneWeekAgo, $now]);

            $sixMonthsSalesQuery = Invoice::select(
                DB::raw('SUM(total_amount) as total_amount')
            )
                ->where('user_id', $loginUser->id)
                ->whereBetween('date_time', [$sixMonthsAgo, $now]);

            $weeklySales = $weeklySalesQuery->value('total_amount') ?? 0;
            $sixMonthsSales = $sixMonthsSalesQuery->value('total_amount') ?? 0;

            // Aggregate total amounts received by each payment mode
            $totalAmountReceived = Invoice::select(
                DB::raw('SUM(CASE WHEN payment_mode = 1 THEN total_amount ELSE 0 END) as total_amount_received_cash'),
                DB::raw('SUM(CASE WHEN payment_mode = 2 THEN total_amount ELSE 0 END) as total_amount_received_card'),
                DB::raw('SUM(CASE WHEN payment_mode = 3 THEN total_amount ELSE 0 END) as total_amount_received_upi')
            )
                ->where('user_id', $loginUser->id);
            if (!empty($startDate) && !empty($endDate)) {
                $totalAmountReceived->whereBetween('date_time', [$startDate, $endDate]);
            }
            $totalAmountReceived = $totalAmountReceived->first();

            // Map sales data for chart
            $chartData = $salesData->map(function ($data) use ($totalExpenses) {
                $totalSales = $data->total_amount;
                $totalDiscount = $data->total_discount;
                $totalProfit = $totalSales - $totalExpenses - $totalDiscount;

                return [
                    'month' => $data->year . '-' . str_pad($data->month, 2, '0', STR_PAD_LEFT),
                    'total_amount' => $totalSales,
                    'total_quantity_sold' => $data->sub_total,
                    'total_discount_amount' => $totalDiscount,
                    'amount_received_card' => $data->amount_received_card,
                    'amount_received_upi' => $data->amount_received_upi,
                    'amount_received_cash' => $data->amount_received_cash,
                    'total_profit' => number_format($totalProfit, 2)
                ];
            });

            // Total overview for chart
            $totalSales = $salesData->sum('total_amount');
            $totalDiscount = $salesData->sum('total_discount');
            $totalProfit = $totalSales - $totalExpenses;



            $response = [
                'total_sales' => number_format($totalSales, 2),
                'total_expenses' => $totalExpenses,
                'total_discount' => $totalDiscount,
                'total_profit' => number_format($totalProfit, 2),
                'weekly_sales' => $weeklySales,
                'six_months_sales' => $sixMonthsSales,
                'total_amount_received_card' => $totalAmountReceived->total_amount_received_card,
                'total_amount_received_upi' => $totalAmountReceived->total_amount_received_upi,
                'total_amount_received_cash' => $totalAmountReceived->total_amount_received_cash,
                'monthly_sales' => $chartData,
                // 'invoices' => $invoices
            ];

            return response()->json(['status' => 1, 'report' => $response], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0, 'message' => 'Error generating sales report', 'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'code' => $e->getCode(),
                'function' => $e->getTrace()
            ], 500);
        }
    }



    ////  customer report
    public function getCustomerTransactions(Request $request)
    {
        $loginUser = $request->user();

        try {
            $query = Invoice::select(
                'cost_name',
                'cost_address',
                'cost_number',
                DB::raw('COUNT(id) as total_transactions'),
                DB::raw('SUM(total_amount) as total_amount_spent')
            )
                ->where('user_id', $loginUser->id)
                ->groupBy('cost_number', 'cost_name', 'cost_address');

            // Apply date range filter if provided
            if ($request->has('start_date') && $request->has('end_date') && !empty($request->input('start_date')) && !empty($request->input('end_date'))) {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                $query->whereBetween('date_time', [$startDate, $endDate]);
            }

            $customers = $query->paginate(10);
            if ($customers->count() > 0) {
                $customers = $customers;
            } else {
                $customers = null;
            }

            return response()->json(['status' => 1, 'customers' => $customers], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }

    public function getCustomerDetails(Request $request)
    {
        $loginUser = $request->user();

        $customerMobile = $request->input('customer_mobile');
        if (!$customerMobile) {
            return response()->json(['status' => 0, 'error' => 'Customer mobile number is required'], 400);
        }

        try {
            $customerInvoices = Invoice::with('foodList')
                ->where('user_id', $loginUser->id)
                ->where('cost_number', $customerMobile)
                ->orderBy('date_time', 'desc')
                ->get();

            $customerDetails = [];
            $totalOrders = $customerInvoices->count();
            $totalAmountSpent = $customerInvoices->sum('total_amount');

            foreach ($customerInvoices as $invoice) {
                $invoiceDetails = [
                    'invoice_id' => $invoice->id,
                    'bill_number' => $invoice->bill_number,
                    'table_number' => $invoice->table_number,
                    'date_time' => $invoice->date_time,
                    'waiter_code' => $invoice->waiter_code,
                    'cashier_name' => $invoice->cashier_name,
                    'sub_total' => $invoice->sub_total,
                    'sgst' => $invoice->sgst,
                    'cgst' => $invoice->cgst,
                    'total_amount' => $invoice->total_amount,
                    'payment_mode' => $invoice->payment_mode,
                    'message' => $invoice->message,
                    'restrent_data' => $invoice->restrent_data,
                    'food_list' => $invoice->foodList->map(function ($foodItem) {
                        return [
                            'item_name' => $foodItem->item_name,
                            'quantity' => $foodItem->quantity,
                            'price_per_unit' => $foodItem->price_per_unit,
                            'total_price' => $foodItem->total_price,
                        ];
                    }),
                ];
                $customerDetails[] = $invoiceDetails;
            }

            $response = [
                'status' => 1,
                'customer_mobile' => $customerMobile,
                'total_orders' => $totalOrders,
                'total_amount_spent' => $totalAmountSpent,
                'invoices' => $customerDetails,
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json(['status' => 0, 'error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }
}
