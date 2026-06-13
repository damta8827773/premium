"""Create a Premium Store deposit transaction — Python."""
import time
import requests

API_URL = "https://yourdomain.com/api/midtrans-create.php"

payload = {
    "order_id": f"DEP-{int(time.time())}",
    "amount": 50_000,
    "user_id": "firebase-uid",
    "email": "customer@example.com",
    "name": "Customer Name",
}

resp = requests.post(API_URL, json=payload, timeout=30)
resp.raise_for_status()
data = resp.json()

print("Snap token :", data["token"])
print("Redirect   :", data["redirect_url"])
