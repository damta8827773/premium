# Create a Premium Store deposit transaction — R
library(httr)
library(jsonlite)

api_url <- "https://yourdomain.com/api/midtrans-create.php"

payload <- list(
  order_id = paste0("DEP-", as.integer(Sys.time())),
  amount   = 50000,
  user_id  = "firebase-uid",
  email    = "customer@example.com",
  name     = "Customer Name"
)

response <- POST(
  api_url,
  body = toJSON(payload, auto_unbox = TRUE),
  content_type_json()
)

data <- content(response, "parsed")
cat("Snap token :", data$token, "\n")
cat("Redirect   :", data$redirect_url, "\n")
