/* Create a Premium Store deposit transaction - C (libcurl)
 * build: gcc c.c -lcurl -o deposit
 */
#include <curl/curl.h>
#include <stdio.h>
#include <time.h>

int main(void) {
    const char *url = "https://yourdomain.com/backend/api/midtrans-create.php";

    char body[256];
    snprintf(body, sizeof(body),
        "{\"order_id\":\"DEP-%ld\",\"amount\":50000,"
        "\"user_id\":\"firebase-uid\","
        "\"email\":\"customer@example.com\",\"name\":\"Customer Name\"}",
        (long)time(NULL));

    CURL *curl = curl_easy_init();
    if (!curl) return 1;

    struct curl_slist *headers = NULL;
    headers = curl_slist_append(headers, "Content-Type: application/json");

    curl_easy_setopt(curl, CURLOPT_URL, url);
    curl_easy_setopt(curl, CURLOPT_HTTPHEADER, headers);
    curl_easy_setopt(curl, CURLOPT_POSTFIELDS, body);

    CURLcode res = curl_easy_perform(curl);
    if (res != CURLE_OK)
        fprintf(stderr, "request failed: %s\n", curl_easy_strerror(res));

    curl_slist_free_all(headers);
    curl_easy_cleanup(curl);
    return 0;
}
