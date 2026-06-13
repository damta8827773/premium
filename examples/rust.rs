// Create a Premium Store deposit transaction — Rust
// deps: reqwest = { version = "0.12", features = ["json", "blocking"] }
//       serde_json = "1"
use std::time::{SystemTime, UNIX_EPOCH};

fn main() -> Result<(), Box<dyn std::error::Error>> {
    let api_url = "https://yourdomain.com/api/midtrans-create.php";
    let ts = SystemTime::now().duration_since(UNIX_EPOCH)?.as_secs();

    let payload = serde_json::json!({
        "order_id": format!("DEP-{ts}"),
        "amount": 50_000,
        "user_id": "firebase-uid",
        "email": "customer@example.com",
        "name": "Customer Name",
    });

    let data: serde_json::Value = reqwest::blocking::Client::new()
        .post(api_url)
        .json(&payload)
        .send()?
        .json()?;

    println!("Snap token : {}", data["token"]);
    println!("Redirect   : {}", data["redirect_url"]);
    Ok(())
}
