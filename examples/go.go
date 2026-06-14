// Create a Premium Store deposit transaction - Go
package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"net/http"
	"time"
)

const apiURL = "https://yourdomain.com/backend/api/midtrans-create.php"

func main() {
	payload := map[string]any{
		"order_id": fmt.Sprintf("DEP-%d", time.Now().Unix()),
		"amount":   50000,
		"user_id":  "firebase-uid",
		"email":    "customer@example.com",
		"name":     "Customer Name",
	}

	body, _ := json.Marshal(payload)
	resp, err := http.Post(apiURL, "application/json", bytes.NewReader(body))
	if err != nil {
		panic(err)
	}
	defer resp.Body.Close()

	var data struct {
		Token       string `json:"token"`
		RedirectURL string `json:"redirect_url"`
	}
	json.NewDecoder(resp.Body).Decode(&data)

	fmt.Println("Snap token :", data.Token)
	fmt.Println("Redirect   :", data.RedirectURL)
}
