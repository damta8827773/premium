// Create a Premium Store deposit transaction - C++ (libcurl)
// build: g++ cpp.cpp -lcurl -o deposit
#include <curl/curl.h>
#include <ctime>
#include <iostream>
#include <string>

static size_t writeCb(void* ptr, size_t size, size_t nmemb, std::string* out) {
    out->append(static_cast<char*>(ptr), size * nmemb);
    return size * nmemb;
}

int main() {
    const std::string url = "https://yourdomain.com/backend/api/midtrans-create.php";
    std::string body =
        "{\"order_id\":\"DEP-" + std::to_string(std::time(nullptr)) + "\","
        "\"amount\":50000,\"user_id\":\"firebase-uid\","
        "\"email\":\"customer@example.com\",\"name\":\"Customer Name\"}";

    CURL* curl = curl_easy_init();
    if (!curl) return 1;

    std::string response;
    struct curl_slist* headers = curl_slist_append(nullptr, "Content-Type: application/json");

    curl_easy_setopt(curl, CURLOPT_URL, url.c_str());
    curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);
    curl_easy_setopt(curl, CURLOPT_POSTFIELDS, body.c_str());
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, writeCb);
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &response);
    curl_easy_perform(curl);

    curl_slist_free_all(headers);
    curl_easy_cleanup(curl);

    std::cout << "Response: " << response << "\n";
    return 0;
}
