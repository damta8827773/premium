// Create a Premium Store deposit transaction - Scala
import java.net.URI
import java.net.http.{HttpClient, HttpRequest, HttpResponse}

@main def createDeposit(): Unit =
  val apiUrl  = "https://yourdomain.com/backend/api/midtrans-create.php"
  val orderId = s"DEP-${System.currentTimeMillis() / 1000}"

  val body =
    s"""{
       |  "order_id": "$orderId",
       |  "amount": 50000,
       |  "user_id": "firebase-uid",
       |  "email": "customer@example.com",
       |  "name": "Customer Name"
       |}""".stripMargin

  val request = HttpRequest.newBuilder()
    .uri(URI.create(apiUrl))
    .header("Content-Type", "application/json")
    .POST(HttpRequest.BodyPublishers.ofString(body))
    .build()

  val response = HttpClient.newHttpClient()
    .send(request, HttpResponse.BodyHandlers.ofString())

  println(s"Response: ${response.body()}")
