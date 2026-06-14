# Create a Premium Store deposit transaction - Ruby
require "net/http"
require "json"
require "uri"

API_URL = "https://yourdomain.com/backend/api/midtrans-create.php"

payload = {
  order_id: "DEP-#{Time.now.to_i}",
  amount: 50_000,
  user_id: "firebase-uid",
  email: "customer@example.com",
  name: "Customer Name"
}

uri = URI(API_URL)
response = Net::HTTP.post(uri, payload.to_json, "Content-Type" => "application/json")
data = JSON.parse(response.body)

puts "Snap token : #{data['token']}"
puts "Redirect   : #{data['redirect_url']}"
