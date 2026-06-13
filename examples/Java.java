// Create a Premium Store deposit transaction — Java (11+)
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;

public class Java {
    static final String API_URL = "https://yourdomain.com/api/midtrans-create.php";

    public static void main(String[] args) throws Exception {
        String orderId = "DEP-" + (System.currentTimeMillis() / 1000);
        String body = """
            {
              "order_id": "%s",
              "amount": 50000,
              "user_id": "firebase-uid",
              "email": "customer@example.com",
              "name": "Customer Name"
            }""".formatted(orderId);

        HttpRequest request = HttpRequest.newBuilder()
                .uri(URI.create(API_URL))
                .header("Content-Type", "application/json")
                .POST(HttpRequest.BodyPublishers.ofString(body))
                .build();

        HttpResponse<String> response = HttpClient.newHttpClient()
                .send(request, HttpResponse.BodyHandlers.ofString());

        System.out.println("Response: " + response.body());
    }
}
