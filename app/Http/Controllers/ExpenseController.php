<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['booking', 'categoryRelation']);
        
        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Booking filter
        if ($request->filled('booking_id')) {
            if ($request->booking_id === 'none') {
                $query->whereNull('booking_id');
            } else {
                $query->where('booking_id', $request->booking_id);
            }
        }
        
        // Date filters (changed from start_date/end_date to date_from/date_to to match view)
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('expense_date', [$request->date_from, $request->date_to]);
        } elseif ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        } elseif ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }
        
        // Amount filters
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%");
            });
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')
            ->paginate(10)
            ->appends($request->except('page'));
        
        $categories = ExpenseCategory::where('is_active', true)->get();
        
        // Get expenses by category for the chart
        $expensesByCategory = Expense::select('category')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->map(function($item) {
                $category = ExpenseCategory::where('name', $item->category)->first();
                $item->name = $item->category;
                $item->color_code = $category ? $category->color_code : '#808080';
                return $item;
            });
        
        $totalExpenses = Expense::sum('amount');
        
        // Calculate this month's expenses
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $thisMonthExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');
            
        // Calculate average expense
        $expenseCount = Expense::count();
        $averageExpense = $expenseCount > 0 ? $totalExpenses / $expenseCount : 0;
        
        // Get bookings for filter dropdown
        $bookings = Booking::whereIn('status', ['pending', 'confirmed', 'completed'])
            ->orderBy('booking_date', 'desc')
            ->get();
        
        return view('finance.expenses.index', compact('expenses', 'categories', 'expensesByCategory', 'totalExpenses', 'bookings', 'thisMonthExpenses', 'averageExpense'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $bookings = Booking::whereIn('status', ['pending', 'confirmed', 'completed'])
            ->orderBy('booking_date', 'desc')
            ->get();
        
        return view('finance.expenses.form', compact('categories', 'bookings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string|max:255',
            'booking_id' => 'nullable|exists:bookings,booking_id',
            'receipt_image' => 'nullable|image|max:2048', // max 2MB
            'is_recurring' => 'boolean',
            'recurring_interval' => 'nullable|required_if:is_recurring,1|in:daily,weekly,monthly,yearly',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $expenseData = $request->except(['receipt_image', '_token']);
        $expenseData['created_by'] = auth()->id();
        
        // Handle receipt image upload
        if ($request->hasFile('receipt_image')) {
            $path = $request->file('receipt_image')->store('receipts', 'public');
            $expenseData['receipt_image'] = $path;
        }
        
        // Handle boolean fields
        $expenseData['is_recurring'] = $request->has('is_recurring');
        
        Expense::create($expenseData);
        
        return redirect()->route('finance.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::with(['booking', 'creator', 'categoryRelation'])
            ->findOrFail($id);
            
        // Find similar expenses (same category or similar amount)
        $similarExpenses = Expense::with('categoryRelation')
            ->where('expense_id', '!=', $id)
            ->where(function ($query) use ($expense) {
                // Same category
                $query->where('category', $expense->category)
                    // Or similar amount (±20%)
                    ->orWhereBetween('amount', [
                        $expense->amount * 0.8,
                        $expense->amount * 1.2
                    ]);
            })
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();
        
        return view('finance.expenses.show', compact('expense', 'similarExpenses'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::where('is_active', true)->get();
        $bookings = Booking::whereIn('status', ['pending', 'confirmed', 'completed'])
            ->orderBy('booking_date', 'desc')
            ->get();
        
        return view('finance.expenses.form', compact('expense', 'categories', 'bookings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category' => 'required|string|max:255',
            'booking_id' => 'nullable|exists:bookings,booking_id',
            'receipt_image' => 'nullable|image|max:2048', // max 2MB
            'is_recurring' => 'boolean',
            'recurring_interval' => 'nullable|required_if:is_recurring,1|in:daily,weekly,monthly,yearly',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $expense = Expense::findOrFail($id);
        $expenseData = $request->except(['receipt_image', '_token', '_method']);
        
        // Handle receipt image upload
        if ($request->hasFile('receipt_image')) {
            // Delete the old image if it exists
            if ($expense->receipt_image) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            
            $path = $request->file('receipt_image')->store('receipts', 'public');
            $expenseData['receipt_image'] = $path;
        }
        
        // Handle boolean fields
        $expenseData['is_recurring'] = $request->has('is_recurring');
        
        $expense->update($expenseData);
        
        return redirect()->route('finance.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        
        // Delete receipt image if it exists
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }
        
        $expense->delete();
        
        return redirect()->route('finance.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
    
    /**
     * Get expense categories as JSON
     */
    public function getCategoriesJson()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return response()->json($categories);
    }
    
    /**
     * Get expenses by category for chart
     */
    public function getExpensesByCategory(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->get()
            ->groupBy('category')
            ->map(function ($group, $categoryName) {
                // Since we're now using string categories, we need to get the ExpenseCategory by name if it exists
                $category = ExpenseCategory::where('name', $categoryName)->first();
                return [
                    'category' => $categoryName,
                    'name' => $categoryName ?: 'Unknown',
                    'color' => $category ? $category->color_code : '#808080',
                    'total' => $group->sum('amount')
                ];
            })
            ->values();
        
        return response()->json($expenses);
    }
    
    /**
     * Get expenses trend for chart
     */
    public function getExpensesTrend(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $limit = $request->input('limit', 6);
        
        if ($period === 'monthly') {
            $result = Expense::select(
                    DB::raw('YEAR(expense_date) as year'),
                    DB::raw('MONTH(expense_date) as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit($limit)
                ->get();
            
            $formatted = $result->map(function ($item) {
                $date = Carbon::createFromDate($item->year, $item->month, 1);
                return [
                    'label' => $date->format('M Y'),
                    'total' => $item->total
                ];
            });
        } else {
            // Daily trend for last N days
            $result = Expense::select(
                    DB::raw('DATE(expense_date) as date'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('expense_date', '>=', now()->subDays($limit))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
            $formatted = $result->map(function ($item) {
                $date = Carbon::parse($item->date);
                return [
                    'label' => $date->format('M d'),
                    'total' => $item->total
                ];
            });
        }
        
        return response()->json(array_values($formatted->toArray()));
    }
}
