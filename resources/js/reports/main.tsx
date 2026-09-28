import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import ReportsApp, { type ReportsAppProps } from "./ReportsApp";

const root = document.getElementById("reports-root");

if (root) {
    const initial = JSON.parse(root.dataset.initial ?? "{}") as ReportsAppProps["initial"];
    createRoot(root).render(
        <StrictMode>
            <ReportsApp initial={initial} />
        </StrictMode>,
    );
}
