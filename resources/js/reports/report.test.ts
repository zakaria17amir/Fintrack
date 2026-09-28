import { afterEach, describe, expect, it, vi } from "vitest";
import { fetchReport, formatCents, share, toTrendSeries } from "./report";

describe("formatCents", () => {
  it("formats integer cents as dollars", () => {
    expect(formatCents(123456)).toBe("$1,234.56");
    expect(formatCents(0)).toBe("$0.00");
    expect(formatCents(-2500)).toBe("-$25.00");
  });
});

describe("share", () => {
  it("returns a one-decimal percentage and 0 when there is no total", () => {
    expect(share(250, 1000)).toBe(25);
    expect(share(1, 3)).toBe(33.3);
    expect(share(0, 0)).toBe(0);
  });
});

describe("toTrendSeries", () => {
  it("labels months and converts cents to dollars for the chart axes", () => {
    expect(toTrendSeries([{ month: "2026-09", income: 150000, expense: 50050 }])).toEqual([
      { label: "Sep 2026", income: 1500, expense: 500.5 },
    ]);
  });
});

describe("fetchReport", () => {
  afterEach(() => vi.unstubAllGlobals());

  it("requests JSON with the filters, repeating categories[]", async () => {
    const fetch = vi.fn().mockResolvedValue(new Response(JSON.stringify({ ok: 1 }), { status: 200 }));
    vi.stubGlobal("fetch", fetch);

    await fetchReport({ date_from: "2026-04-01", date_to: "2026-09-30", categories: [2, 5] });

    const [url, init] = fetch.mock.calls[0];
    expect(url).toBe("/reports/data?date_from=2026-04-01&date_to=2026-09-30&categories%5B%5D=2&categories%5B%5D=5");
    expect(init.headers.Accept).toBe("application/json");
  });

  it("throws the server's validation message on 422", async () => {
    vi.stubGlobal(
      "fetch",
      vi.fn().mockResolvedValue(
        new Response(JSON.stringify({ message: "The date to field must be a date after or equal to date from." }), { status: 422 }),
      ),
    );

    await expect(fetchReport({ date_from: "2026-09-30", date_to: "2026-09-01", categories: [] })).rejects.toThrow(
      "The date to field must be a date after or equal to date from.",
    );
  });
});
