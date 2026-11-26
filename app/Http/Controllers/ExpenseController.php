<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Greenhouse;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    public function index(Request $request): Response
    {
        $greenhouses = Greenhouse::orderBy('code')->get(['id', 'code', 'name', 'crop']);

        $greenhouse = Greenhouse::when(
            $request->integer('greenhouse'),
            fn ($q, $id) => $q->where('id', $id),
            fn ($q) => $q->orderBy('code')
        )->firstOrFail();

        $expenses = Expense::where('greenhouse_id', $greenhouse->id)
            ->orderByDesc('spent_on')
            ->get(['id', 'category', 'amount', 'spent_on', 'note']);

        $totalThisMonth = Expense::where('greenhouse_id', $greenhouse->id)
            ->whereBetween('spent_on', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $byCategory = Expense::where('greenhouse_id', $greenhouse->id)
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'label' => Expense::CATEGORIES[$row->category] ?? $row->category,
                'total' => (float) $row->total,
            ]);

        $byMonth = Expense::where('greenhouse_id', $greenhouse->id)
            ->selectRaw("to_char(spent_on, 'YYYY-MM') as month, sum(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'month' => $row->month,
                'total' => (float) $row->total,
            ]);

        $totalAll = (float) Expense::where('greenhouse_id', $greenhouse->id)->sum('amount');

        $expectedKg = (float) ($greenhouse->yieldForecasts()->orderByDesc('horizon_days')->first()?->expected_kg ?? 0);
        $costPerKg = $expectedKg > 0 ? round($totalAll / $expectedKg, 0) : null;
        $costPerHa = $greenhouse->area_ha > 0 ? round($totalAll / $greenhouse->area_ha, 0) : null;

        return Inertia::render('Expenses', [
            'greenhouses' => $greenhouses,
            'greenhouseId' => $greenhouse->id,
            'categories' => Expense::CATEGORIES,
            'expenses' => $expenses,
            'stats' => [
                'total_all' => $totalAll,
                'total_this_month' => (float) $totalThisMonth,
                'cost_per_kg' => $costPerKg,
                'cost_per_ha' => $costPerHa,
            ],
            'byCategory' => $byCategory,
            'byMonth' => $byMonth,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'greenhouse_id' => ['required', 'exists:greenhouses,id'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(Expense::CATEGORIES))],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent_on' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        Expense::create($data);

        return back();
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return back();
    }

    public function suppliers(string $category): Response
    {
        abort_unless(array_key_exists($category, Expense::CATEGORIES), 404);

        $suppliers = Supplier::where('category', $category)
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'location', 'note']);

        return Inertia::render('Suppliers', [
            'category' => $category,
            'categoryLabel' => Expense::CATEGORIES[$category],
            'suppliers' => $suppliers,
        ]);
    }
}
