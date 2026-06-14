// Create a Premium Store deposit transaction - Node.js (18+)
const API_URL = "https://yourdomain.com/backend/api/midtrans-create.php";

const payload = {
  order_id: `DEP-${Date.now()}`,
  amount: 50000,
  user_id: "firebase-uid",
  email: "customer@example.com",
  name: "Customer Name",
};

const res = await fetch(API_URL, {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(payload),
});

if (!res.ok) throw new Error(`Request failed: ${res.status}`);

const { token, redirect_url } = await res.json();
console.log("Snap token :", token);
console.log("Redirect   :", redirect_url);
