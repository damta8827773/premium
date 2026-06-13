# API Client Examples

Each sample in this folder calls the **create payment transaction** endpoint:

```
POST /api/midtrans-create.php
```

It sends a deposit request and prints the returned Snap `token` and
`redirect_url`. The same flow is shown in **20 programming languages** so you
can integrate Premium Store from whatever stack you already use.

Before running any sample, replace `https://yourdomain.com` with your own
deployed URL.

| # | Language | File | Run |
|---|----------|------|-----|
| 1 | Python | [python.py](python.py) | `python python.py` |
| 2 | JavaScript (Node) | [node.js](node.js) | `node node.js` |
| 3 | TypeScript | [typescript.ts](typescript.ts) | `ts-node typescript.ts` |
| 4 | PHP | [php.php](php.php) | `php php.php` |
| 5 | Go | [go.go](go.go) | `go run go.go` |
| 6 | Rust | [rust.rs](rust.rs) | `cargo run` |
| 7 | Java | [Java.java](Java.java) | `java Java.java` |
| 8 | Kotlin | [kotlin.kt](kotlin.kt) | `kotlin kotlin.kt` |
| 9 | C# | [csharp.cs](csharp.cs) | `dotnet run` |
| 10 | Ruby | [ruby.rb](ruby.rb) | `ruby ruby.rb` |
| 11 | Swift | [swift.swift](swift.swift) | `swift swift.swift` |
| 12 | Dart | [dart.dart](dart.dart) | `dart run dart.dart` |
| 13 | C++ | [cpp.cpp](cpp.cpp) | compile with libcurl |
| 14 | C | [c.c](c.c) | compile with libcurl |
| 15 | Bash (curl) | [curl.sh](curl.sh) | `bash curl.sh` |
| 16 | PowerShell | [powershell.ps1](powershell.ps1) | `pwsh powershell.ps1` |
| 17 | Perl | [perl.pl](perl.pl) | `perl perl.pl` |
| 18 | Elixir | [elixir.exs](elixir.exs) | `elixir elixir.exs` |
| 19 | Scala | [scala.scala](scala.scala) | `scala scala.scala` |
| 20 | R | [r.R](r.R) | `Rscript r.R` |

> These are integration examples only — they contain no credentials. The
> server key never leaves the backend.
