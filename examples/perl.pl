#!/usr/bin/env perl
# Create a Premium Store deposit transaction — Perl
use strict;
use warnings;
use HTTP::Tiny;
use JSON::PP;

my $api_url = "https://yourdomain.com/api/midtrans-create.php";

my $payload = encode_json({
    order_id => "DEP-" . time(),
    amount   => 50000,
    user_id  => "firebase-uid",
    email    => "customer\@example.com",
    name     => "Customer Name",
});

my $res = HTTP::Tiny->new->post($api_url, {
    headers => { "Content-Type" => "application/json" },
    content => $payload,
});

my $data = decode_json($res->{content});
print "Snap token : $data->{token}\n";
print "Redirect   : $data->{redirect_url}\n";
