# Create a Premium Store deposit transaction - Elixir
# Uses :httpc / :json from the standard runtime - no external deps.
:inets.start()
:ssl.start()

api_url = ~c"https://yourdomain.com/backend/api/midtrans-create.php"

payload =
  %{
    "order_id" => "DEP-#{System.system_time(:second)}",
    "amount" => 50_000,
    "user_id" => "firebase-uid",
    "email" => "customer@example.com",
    "name" => "Customer Name"
  }
  |> :json.encode()
  |> IO.iodata_to_binary()

{:ok, {_status, _headers, body}} =
  :httpc.request(
    :post,
    {api_url, [], ~c"application/json", payload},
    [],
    []
  )

IO.puts("Response: #{body}")
