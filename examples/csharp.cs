// Create a Premium Store deposit transaction — C# (.NET)
using System.Text;
using System.Text.Json;

const string apiUrl = "https://yourdomain.com/api/midtrans-create.php";

var payload = new
{
    order_id = $"DEP-{DateTimeOffset.UtcNow.ToUnixTimeSeconds()}",
    amount = 50000,
    user_id = "firebase-uid",
    email = "customer@example.com",
    name = "Customer Name",
};

using var http = new HttpClient();
var content = new StringContent(
    JsonSerializer.Serialize(payload), Encoding.UTF8, "application/json");

var response = await http.PostAsync(apiUrl, content);
var json = await response.Content.ReadAsStringAsync();

using var doc = JsonDocument.Parse(json);
Console.WriteLine($"Snap token : {doc.RootElement.GetProperty("token")}");
Console.WriteLine($"Redirect   : {doc.RootElement.GetProperty("redirect_url")}");
