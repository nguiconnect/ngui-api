#!/usr/bin/env bash
set -euo pipefail

# ============================================
# Ngui Connect API - End-to-end Quote Workflow
# ============================================

BASE_URL="${BASE_URL:-http://127.0.0.1:8000}"

# ---- Credentials (set env vars or edit here) ----
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@ngui.test}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-password}"

PROVIDER_EMAIL="${PROVIDER_EMAIL:-prestataire@ngui.test}"
PROVIDER_PASSWORD="${PROVIDER_PASSWORD:-password}"

# ---- Quote payload defaults ----
PROVIDER_ID="${PROVIDER_ID:-3}"
QUOTE_NAME="${QUOTE_NAME:-Test Devis Bash}"
QUOTE_EMAIL="${QUOTE_EMAIL:-client@test.com}"
QUOTE_PHONE="${QUOTE_PHONE:-+44700000000}"
QUOTE_MESSAGE="${QUOTE_MESSAGE:-Bonjour, je veux un devis (script bash).}"

# ---- Status updates ----
PROVIDER_NEW_STATUS="${PROVIDER_NEW_STATUS:-contacted}"
ADMIN_NEW_STATUS="${ADMIN_NEW_STATUS:-done}"

# ---- Helpers ----
need_cmd() {
  command -v "$1" >/dev/null 2>&1 || {
    echo "❌ Missing dependency: $1"
    exit 1
  }
}

json_pretty() {
  # Pretty print JSON if jq exists; otherwise raw
  if command -v jq >/dev/null 2>&1; then jq .; else cat; fi
}

echo_step() {
  echo
  echo "===================="
  echo "$1"
  echo "===================="
}

# ---- Check dependencies ----
need_cmd curl
need_cmd jq

# ---- 1) Login Provider ----
echo_step "1) Login Provider"
PROVIDER_LOGIN_JSON="$(curl -s -X POST "$BASE_URL/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$PROVIDER_EMAIL\",\"password\":\"$PROVIDER_PASSWORD\"}")"

echo "$PROVIDER_LOGIN_JSON" | json_pretty

PROVIDER_TOKEN="$(echo "$PROVIDER_LOGIN_JSON" | jq -r '.token // empty')"
if [[ -z "$PROVIDER_TOKEN" || "$PROVIDER_TOKEN" == "null" ]]; then
  echo "❌ Provider token not found in login response."
  exit 1
fi
echo "✅ provider_token captured."

# ---- 2) Login Admin ----
echo_step "2) Login Admin"
ADMIN_LOGIN_JSON="$(curl -s -X POST "$BASE_URL/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$ADMIN_EMAIL\",\"password\":\"$ADMIN_PASSWORD\"}")"

echo "$ADMIN_LOGIN_JSON" | json_pretty

ADMIN_TOKEN="$(echo "$ADMIN_LOGIN_JSON" | jq -r '.token // empty')"
if [[ -z "$ADMIN_TOKEN" || "$ADMIN_TOKEN" == "null" ]]; then
  echo "❌ Admin token not found in login response."
  exit 1
fi
echo "✅ admin_token captured."

# ---- 3) Create Quote (Public) ----
echo_step "3) Create Quote (public)"
CREATE_QUOTE_JSON="$(curl -s -X POST "$BASE_URL/api/v1/quotes" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d "{\"provider_id\":$PROVIDER_ID,\"name\":\"$QUOTE_NAME\",\"email\":\"$QUOTE_EMAIL\",\"phone\":\"$QUOTE_PHONE\",\"message\":\"$QUOTE_MESSAGE\"}")"

echo "$CREATE_QUOTE_JSON" | json_pretty

QUOTE_ID="$(echo "$CREATE_QUOTE_JSON" | jq -r '.data.id // .id // empty')"
if [[ -z "$QUOTE_ID" || "$QUOTE_ID" == "null" ]]; then
  echo "❌ quote_id not found in create quote response."
  echo "Tip: check backend response structure (data.id)."
  exit 1
fi
echo "✅ quote_id captured: $QUOTE_ID"

# ---- 4) Provider - List My Quotes ----
echo_step "4) Provider - List My Quotes"
PROVIDER_LIST_JSON="$(curl -s "$BASE_URL/api/v1/provider/quotes" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $PROVIDER_TOKEN")"

echo "$PROVIDER_LIST_JSON" | json_pretty

# ---- 5) Provider - Update Quote Status ----
echo_step "5) Provider - Update Quote Status (quote_id=$QUOTE_ID -> $PROVIDER_NEW_STATUS)"
PROVIDER_UPDATE_JSON="$(curl -s -X PATCH "$BASE_URL/api/v1/provider/quotes/$QUOTE_ID" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $PROVIDER_TOKEN" \
  -H "Content-Type: application/json" \
  -d "{\"status\":\"$PROVIDER_NEW_STATUS\"}")"

echo "$PROVIDER_UPDATE_JSON" | json_pretty

# ---- 6) Admin - Update Quote Status ----
echo_step "6) Admin - Update Quote Status (quote_id=$QUOTE_ID -> $ADMIN_NEW_STATUS)"
ADMIN_UPDATE_JSON="$(curl -s -X PATCH "$BASE_URL/api/v1/quotes/$QUOTE_ID" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d "{\"status\":\"$ADMIN_NEW_STATUS\"}")"

echo "$ADMIN_UPDATE_JSON" | json_pretty

echo_step "✅ DONE"
echo "Quote workflow finished successfully."
echo "quote_id=$QUOTE_ID"
