<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Alamat IP proxy yang header X-Forwarded-* nya dipercaya (IP asli user,
    | HTTPS, host). Dengan Cloudflare Tunnel, isi dengan IP tempat cloudflared
    | terhubung ke web server, mis. "127.0.0.1,::1" jika cloudflared berjalan
    | di server yang sama. Pisahkan beberapa IP/CIDR dengan koma.
    |
    | "*" = percayai siapa pun yang terhubung langsung. Hanya aman jika web
    | server TIDAK bisa diakses langsung tanpa lewat tunnel, karena siapa pun
    | yang bisa mengaksesnya langsung dapat memalsukan IP-nya.
    |
    */

    'proxies' => env('TRUSTED_PROXIES', '*'),

];
