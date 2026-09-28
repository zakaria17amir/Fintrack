import { useEffect, useState } from "react";
import {
    Area,
    AreaChart,
    CartesianGrid,
    Cell,
    Legend,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from "recharts";
import { fetchReport, formatCents, share, toQuery, toTrendSeries, type Category, type Filters, type ReportPayload } from "./report";

export interface ReportsAppProps {
    initial: { filters: Filters; categories: Category[]; data: ReportPayload };
}

const card = "bg-white rounded-lg shadow-sm border border-gray-200";
const EMPTY = "No expense data for the selected period.";

export default function ReportsApp({ initial }: ReportsAppProps) {
    const [filters, setFilters] = useState(initial.filters);
    const [data, setData] = useState(initial.data);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [attempt, setAttempt] = useState(0);

    useEffect(() => {
        // The server already rendered the initial filters' data (identity check survives StrictMode).
        if (filters === initial.filters && attempt === 0) return;
        const controller = new AbortController();
        setLoading(true);
        setError(null);
        history.replaceState(null, "", `?${toQuery(filters)}`); // keep the view bookmarkable
        fetchReport(filters, controller.signal)
            .then(setData)
            .catch((e: Error) => controller.signal.aborted || setError(e.message))
            .finally(() => controller.signal.aborted || setLoading(false));
        return () => controller.abort(); // a newer filter change wins
    }, [filters, attempt, initial.filters]);

    const toggleCategory = (id: number) =>
        setFilters((f) => ({
            ...f,
            categories: f.categories.includes(id) ? f.categories.filter((c) => c !== id) : [...f.categories, id],
        }));

    const { totals, byCategory } = data;
    const trend = toTrendSeries(data.monthly);

    return (
        <div>
            <div className={`${card} p-6 mb-6`}>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {(["date_from", "date_to"] as const).map((key) => (
                        <div key={key}>
                            <label htmlFor={key} className="block text-sm font-medium text-gray-700 mb-1">
                                {key === "date_from" ? "From" : "To"}
                            </label>
                            <input
                                type="date"
                                id={key}
                                value={filters[key]}
                                onChange={(e) => e.target.value && setFilters((f) => ({ ...f, [key]: e.target.value }))}
                                className="w-full border-gray-300 rounded-lg text-sm"
                            />
                        </div>
                    ))}
                </div>
                <fieldset>
                    <legend className="block text-sm font-medium text-gray-700 mb-2">
                        Filter by categories <span className="font-normal text-gray-400">(none ticked = all)</span>
                    </legend>
                    <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                        {initial.categories.map((category) => (
                            <label key={category.id} className="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer">
                                <input
                                    type="checkbox"
                                    checked={filters.categories.includes(category.id)}
                                    onChange={() => toggleCategory(category.id)}
                                    className="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300"
                                />
                                <span className="ml-2 text-sm text-gray-700">{category.name}</span>
                            </label>
                        ))}
                    </div>
                </fieldset>
            </div>

            {error && (
                <div role="alert" className="mb-6 flex items-center justify-between rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <span>{error}</span>
                    <button type="button" onClick={() => setAttempt((a) => a + 1)} className="rounded bg-red-600 px-3 py-1.5 text-white hover:bg-red-700">
                        Retry
                    </button>
                </div>
            )}

            <div className={`transition-opacity ${loading ? "opacity-50" : ""}`} aria-busy={loading}>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <Total testId="total-income" label="Total Income" cents={totals.income} tone="text-green-600" />
                    <Total testId="total-expense" label="Total Expenses" cents={totals.expense} tone="text-red-600" />
                    <Total testId="total-net" label="Net" cents={totals.net} tone={totals.net >= 0 ? "text-green-600" : "text-red-600"} />
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div className={`${card} p-6`}>
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Spending by Category</h3>
                        {byCategory.length === 0 ? (
                            <p className="text-gray-500 text-center py-8">{EMPTY}</p>
                        ) : (
                            <div role="img" aria-label="Pie chart of spending by category">
                                <ResponsiveContainer width="100%" height={320}>
                                    <PieChart>
                                        <Pie data={byCategory} dataKey={(c: { total: number }) => c.total / 100} nameKey="name" outerRadius={110}>
                                            {byCategory.map((c) => (
                                                <Cell key={c.id} fill={c.color} stroke="#fff" strokeWidth={2} />
                                            ))}
                                        </Pie>
                                        <Tooltip formatter={(v) => formatCents(Math.round(Number(v) * 100))} />
                                        <Legend />
                                    </PieChart>
                                </ResponsiveContainer>
                            </div>
                        )}
                    </div>

                    <div className={`${card} p-6`}>
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">Monthly Trend</h3>
                        <div role="img" aria-label="Area chart of monthly income and expenses">
                            <ResponsiveContainer width="100%" height={320}>
                                <AreaChart data={trend} margin={{ top: 5, right: 10, left: 10, bottom: 0 }}>
                                    <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                                    <XAxis dataKey="label" tick={{ fontSize: 12 }} />
                                    <YAxis tick={{ fontSize: 12 }} tickFormatter={(v) => `$${v}`} />
                                    <Tooltip formatter={(v) => formatCents(Math.round(Number(v) * 100))} />
                                    <Legend />
                                    <Area type="monotone" dataKey="income" name="Income" stroke="#10B981" fill="#10B98120" />
                                    <Area type="monotone" dataKey="expense" name="Expenses" stroke="#EF4444" fill="#EF444420" />
                                </AreaChart>
                            </ResponsiveContainer>
                        </div>
                    </div>
                </div>

                <div className={`${card} overflow-hidden`}>
                    <div className="p-6 border-b border-gray-200">
                        <h3 className="text-lg font-semibold text-gray-900">Category Breakdown</h3>
                    </div>
                    {byCategory.length === 0 ? (
                        <p className="text-gray-500 text-center py-8">{EMPTY}</p>
                    ) : (
                        <table className="w-full text-sm text-left text-gray-500">
                            <thead className="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3">Category</th>
                                    <th className="px-6 py-3 text-right">Amount</th>
                                    <th className="px-6 py-3 text-right">% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                {byCategory.map((c) => (
                                    <tr key={c.id} className="border-b hover:bg-gray-50">
                                        <td className="px-6 py-4">
                                            <div className="flex items-center">
                                                <span className="w-3 h-3 rounded-full mr-2" style={{ backgroundColor: c.color }} />
                                                <span className="font-medium text-gray-900">{c.name}</span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-right font-semibold">{formatCents(c.total)}</td>
                                        <td className="px-6 py-4 text-right">{share(c.total, totals.expense).toFixed(1)}%</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>
            </div>
        </div>
    );
}

function Total({ testId, label, cents, tone }: { testId: string; label: string; cents: number; tone: string }) {
    return (
        <div data-testid={testId} className={`${card} p-6 text-center`}>
            <p className="text-sm text-gray-500">{label}</p>
            <p className={`text-2xl font-bold ${tone}`}>{formatCents(cents)}</p>
        </div>
    );
}
