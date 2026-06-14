// Create a Premium Store deposit transaction - Kotlin
import java.net.URI
import java.net.http.HttpClient
import java.net.http.HttpRequest
import java.net.http.HttpResponse

fun main() {
    val apiUrl = "https://yourdomain.com/backend/api/midtrans-create.php"
    val orderId = "DEP-${System.currentTimeMillis() / 1000}"

    val body = """
        {
          "order_id": "$orderId",
          "amount": 50000,
          "user_id": "firebase-uid",
          "email": "customer@example.com",
          "name": "Customer Name"
        }
    """.trimIndent()

    val request = HttpRequest.newBuilder()
        .uri(URI.create(apiUrl))
        .header("Content-Type", "application/json")
        .POST(HttpRequest.BodyPublishers.ofString(body))
        .build()

    val response = HttpClient.newHttpClient()
        .send(request, HttpResponse.BodyHandlers.ofString())

    println("Response: ${response.body()}")
}
