// Shapes returned by GET /reports/data. Money is integer cents until it is formatted.

export interface Filters {
    date_from: string;
    date_to: string;
    categories: number[];
}

export interface Category {
    id: number;
    name: string;
    color: string;
}

export interface ReportPayload {
    totals: { income: number; expense: number; net: number };
    byCategory: (Category & { total: number })[];
    monthly: { month: string; income: number; expense: number }[];
}

const usd = new Intl.NumberFormat("en-US", { style: "currency", currency: "USD" });

export function formatCents(cents: number): string {
    return usd.format(cents / 100);
}

/** Percentage of `total` in `all`, one decimal; 0 when there is nothing to divide by. */
export function share(total: number, all: number): number {
    return all === 0 ? 0 : Math.round((total / all) * 1000) / 10;
}

/** Monthly rows → chart points in dollars with "Sep 2026" labels. */
export function toTrendSeries(monthly: ReportPayload["monthly"]) {
    return monthly.map(({ month, income, expense }) => ({
        label: new Date(`${month}-01T00:00:00`).toLocaleDateString("en-US", { month: "short", year: "numeric" }),
        income: income / 100,
        expense: expense / 100,
    }));
}

export function toQuery(filters: Filters): string {
    const params = new URLSearchParams({ date_from: filters.date_from, date_to: filters.date_to });
    filters.categories.forEach((id) => params.append("categories[]", String(id)));
    return params.toString();
}

export async function fetchReport(filters: Filters, signal?: AbortSignal): Promise<ReportPayload> {
    const response = await fetch(`/reports/data?${toQuery(filters)}`, {
        headers: { Accept: "application/json" },
        credentials: "same-origin",
        signal,
    });
    const body = await response.json();
    if (!response.ok) throw new Error(body.message ?? "Could not load the report.");
    return body;
}
