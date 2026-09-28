import { describe, expect, it, vi } from "vitest";
import { render, screen, waitFor, within } from "@testing-library/react";
import userEvent from "@testing-library/user-event";
import type { ReactNode } from "react";
import ReportsApp from "./ReportsApp";
import type { ReportPayload } from "./report";

const fetchReport = vi.hoisted(() => vi.fn());
vi.mock("./report", async (importOriginal) => ({ ...(await importOriginal<typeof import("./report")>()), fetchReport }));
vi.mock("recharts", async (importOriginal) => ({
    ...(await importOriginal<typeof import("recharts")>()),
    ResponsiveContainer: ({ children }: { children: ReactNode }) => <div>{children}</div>,
}));

const data: ReportPayload = {
    totals: { income: 10000, expense: 4500, net: 5500 },
    byCategory: [{ id: 1, name: "Food", color: "#EF4444", total: 4500 }],
    monthly: [{ month: "2026-09", income: 10000, expense: 4500 }],
};

const initial = {
    filters: { date_from: "2026-04-01", date_to: "2026-09-30", categories: [] },
    categories: [
        { id: 1, name: "Food", color: "#EF4444" },
        { id: 2, name: "Salary", color: "#10B981" },
    ],
    data,
};

describe("ReportsApp", () => {
    it("paints the first render from the server payload without fetching", () => {
        render(<ReportsApp initial={initial} />);

        expect(within(screen.getByTestId("total-net")).getByText("$55.00")).toBeInTheDocument();
        expect(within(screen.getByTestId("total-expense")).getByText("$45.00")).toBeInTheDocument();
        expect(screen.getByRole("cell", { name: "100.0%" })).toBeInTheDocument();
        expect(fetchReport).not.toHaveBeenCalled();
    });

    it("refetches when a category is ticked and shows the new figures", async () => {
        fetchReport.mockResolvedValueOnce({
            totals: { income: 10000, expense: 0, net: 10000 },
            byCategory: [],
            monthly: data.monthly,
        });
        render(<ReportsApp initial={initial} />);

        await userEvent.click(screen.getByRole("checkbox", { name: "Salary" }));

        expect(fetchReport).toHaveBeenLastCalledWith(
            { date_from: "2026-04-01", date_to: "2026-09-30", categories: [2] },
            expect.any(AbortSignal),
        );
        await waitFor(() => expect(within(screen.getByTestId("total-net")).getByText("$100.00")).toBeInTheDocument());
        expect(screen.getAllByText("No expense data for the selected period.").length).toBeGreaterThan(0);
    });

    it("shows the error with a retry that refetches", async () => {
        fetchReport.mockRejectedValueOnce(new Error("The date to field must be a date after or equal to date from."));
        fetchReport.mockResolvedValueOnce(data);
        render(<ReportsApp initial={initial} />);

        await userEvent.click(screen.getByRole("checkbox", { name: "Food" }));

        const alert = await screen.findByRole("alert");
        expect(alert).toHaveTextContent("The date to field must be a date after or equal to date from.");
        await userEvent.click(within(alert).getByRole("button", { name: "Retry" }));
        await waitFor(() => expect(screen.queryByRole("alert")).not.toBeInTheDocument());
        expect(fetchReport).toHaveBeenCalledTimes(2);
    });
});
