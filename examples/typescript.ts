// Create a Premium Store deposit transaction — TypeScript
const API_URL = "https://yourdomain.com/api/midtrans-create.php";

interface DepositRequest {
  order_id: string;
  amount: number;
  user_id: string;
  email: string;
  name: string;
}

interface SnapResponse {
  token: string;
  redirect_url: string;
}

async function createDeposit(req: DepositRequest): Promise<SnapResponse> {
  const res = await fetch(API_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(req),
  });
  if (!res.ok) throw new Error(`Request failed: ${res.status}`);
  return (await res.json()) as SnapResponse;
}

const { token, redirect_url } = await createDeposit({
  order_id: `DEP-${Date.now()}`,
  amount: 50_000,
  user_id: "firebase-uid",
  email: "customer@example.com",
  name: "Customer Name",
});

console.log("Snap token :", token);
console.log("Redirect   :", redirect_url);
