// Create a Premium Store deposit transaction — Swift
import Foundation

let apiURL = URL(string: "https://yourdomain.com/api/midtrans-create.php")!

let payload: [String: Any] = [
    "order_id": "DEP-\(Int(Date().timeIntervalSince1970))",
    "amount": 50_000,
    "user_id": "firebase-uid",
    "email": "customer@example.com",
    "name": "Customer Name",
]

var request = URLRequest(url: apiURL)
request.httpMethod = "POST"
request.setValue("application/json", forHTTPHeaderField: "Content-Type")
request.httpBody = try JSONSerialization.data(withJSONObject: payload)

let semaphore = DispatchSemaphore(value: 0)
URLSession.shared.dataTask(with: request) { data, _, _ in
    defer { semaphore.signal() }
    guard let data = data,
          let json = try? JSONSerialization.jsonObject(with: data) as? [String: Any]
    else { return }
    print("Snap token :", json["token"] ?? "")
    print("Redirect   :", json["redirect_url"] ?? "")
}.resume()
semaphore.wait()
