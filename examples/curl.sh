#!/usr/bin/env bash
# Create a Premium Store deposit transaction — Bash + curl
set -euo pipefail

API_URL="https://yourdomain.com/api/midtrans-create.php"
ORDER_ID="DEP-$(date +%s)"

curl -sS -X POST "$API_URL" \
  -H "Content-Type: application/json" \
  -d @- <<JSON
{
  "order_id": "$ORDER_ID",
  "amount": 50000,
  "user_id": "firebase-uid",
  "email": "customer@example.com",
  "name": "Customer Name"
}
JSON
