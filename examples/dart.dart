// Create a Premium Store deposit transaction - Dart
// deps: http: ^1.0.0
import 'dart:convert';
import 'package:http/http.dart' as http;

void main() async {
  final apiUrl = Uri.parse('https://yourdomain.com/backend/api/midtrans-create.php');

  final payload = {
    'order_id': 'DEP-${DateTime.now().millisecondsSinceEpoch ~/ 1000}',
    'amount': 50000,
    'user_id': 'firebase-uid',
    'email': 'customer@example.com',
    'name': 'Customer Name',
  };

  final response = await http.post(
    apiUrl,
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode(payload),
  );

  final data = jsonDecode(response.body);
  print('Snap token : ${data['token']}');
  print('Redirect   : ${data['redirect_url']}');
}
