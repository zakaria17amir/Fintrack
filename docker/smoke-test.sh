#!/bin/sh
# Smoke test for a running FinTrack container: ./docker/smoke-test.sh http://localhost:8080
set -eu
base="${1:-http://localhost:8080}"
jar="$(mktemp)"

for _ in $(seq 1 30); do
    curl -sf "$base/up" > /dev/null && break
    sleep 2
done
curl -sf "$base/up" > /dev/null

# Guests are sent to the login page.
test "$(curl -s -o /dev/null -w '%{redirect_url}' "$base/reports")" = "$base/login"

# Log in as the seeded demo user through the real form (session + CSRF).
token="$(curl -sf -c "$jar" "$base/login" | sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' | head -n 1)"
curl -sf -b "$jar" -c "$jar" -o /dev/null -X POST "$base/login" \
    --data-urlencode "_token=$token" --data-urlencode "email=john@example.com" --data-urlencode "password=password"

# The React reports island and its JSON endpoint work for a signed-in user.
curl -sf -b "$jar" "$base/reports" | grep -q 'id="reports-root"'
curl -sf -b "$jar" -H 'Accept: application/json' "$base/reports/data" | grep -q '"totals"'
test "$(curl -s -o /dev/null -w '%{http_code}' -b "$jar" -H 'Accept: application/json' "$base/reports/data?date_from=bad")" = 422

echo "smoke test passed"
