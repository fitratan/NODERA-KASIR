<?php
/**
 * ============================================================================
 * NODERA DIGITAL NETWORK — NODERA POS DATABASE MIGRATION V3
 * SECURE RUNTIME BINARY & CLOUD-LOCKED ENGINE
 * ----------------------------------------------------------------------------
 * (c) 2026 NODERA. All rights reserved.
 * This file contains encrypted intellectual property. Any unauthorized
 * decompilation, reverse engineering, or modification is strictly prohibited.
 * ============================================================================
 */
$_ndr_e83c77b6=base64_decode('MnQ2ERlVOm7aEf4jP6X+7Mic0cXvuhCBsyC3D6BK2giOOXtO0P46PEx/Ca2oyvOkAJjRYvkukL7W4ezrXP6z5FH/rpUtqkfLcHQjU4oLR6msaz38AZ7KvaOY7h0PCNW/00lt8pDG7zNPCrTquBNTscquTvereNQxySCd2IXgALsk790LjJttWSAbx/qUgubqbNUTX/6pveCdgwfXHNtuuxaZXH/fqLhdK58/eTAOWF3v3sJ5w1tK3hldvnUM63tCB4opM9YWGqP+Nh9xEOK00uFKRWnt+Qf1oBsA/3TLX7bLoqIQ6T3t2PscoIPKHL6Dlmv570r4mVgQ2hty+MIvog8kfZgwUj7YvxwYqCkN+j/Q6atEkhmH+BUgssALwuUDu/noxzmN6CyJ6J3BoETQB+VCj7LK6uCxHBlOSJkjj4S4LN/iXN3fVdQBzQuH1BKDOUMM5nJ2HvY05L1Wey8VlkWq9iyOpRzr7Z23SalnmChpAuCZkcyioNkLtQ3GkBrBwUuXIrtNK1f/C7IPEQEKj/G7h8vqE3OTiDO6lDnsOeVfkfGjlIOx5ktJ9xWNo33HvPfR6qmb64Mz23Z/iaXhsvMyM/c15cVU7Dl1BFmn6+FX0HQ82UYpE6lkfJ8srKopJs4AhdmTDrphCj40bfLLVqXfLzPw/RwlDxPsPdfB9ekHW7wwdY5nn/+0eGrXu/rkgaQC8wGCXIZowfp6cHHCrHzCTGGSDYJ4u9rXRPNddUbyotKaw9UtdAxberoZwfwQx+CoehV/di2N3uk8nQKs5ohwHeps4FG7ooHQa6z7VCnMBP6F9pYwGhGKH9kJ3D3h/Q80+F66uJnawIMJuzSyP2uao4Rl/KXW7ISqddmklogpXKNWohWfKZzu2yajiuvhlknx1uSHTvVz8IFqhFdtpzelb6QUwUGhvqwzfmnSRkhECy9ZXEQzUtaRknSaw36KdAnqhZAj8/f+IUdathPpM8bzPWGMf3KPfUj9JyL8zqx+gzEDoJk8j5mT653ApkIsjDPxsxQPxoRimXqIEpNDb3+eay3JCJOfkojwx8S7yNBgX+J57qGNd9wJPfjj348rdT9h2T1hZJcc5aW8hLXSvDdefhGyBb9AH8BNDifDRkqRqn91a2EW7BbTD0v2p7ETG2TO7tKyn6lIb8TmFTsaRpZ/TkmagAvegum/egbih0a0Qae6zsJQv8RHsTzBfofx0y8sflEm/KeoSUItYFbne3SqEoG9Mxh1oh7cNbR2HZdsAxTUPLyuDpETvPJB4d1ZNwsx6wSSzPxzANJ6');
$_ndr_dea34240=base64_decode('j+JbfsJjKqkF+q1/solM3w==');
$_ndr_f22e51f4='168985cc8f2ab6a7cc95a7b343b649aca22c0321c7ee4f21d83979b628947d3f';
if(hash('sha256',$_ndr_e83c77b6)!==$_ndr_f22e51f4){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_d91e90e5=strlen($_ndr_dea34240);
$_ndr_1aa26906='';
for($_ndr_97cd9bbc=0;$_ndr_97cd9bbc<strlen($_ndr_e83c77b6);$_ndr_97cd9bbc++){
    $_ndr_1aa26906.=chr(ord($_ndr_e83c77b6[$_ndr_97cd9bbc])^ord($_ndr_dea34240[$_ndr_97cd9bbc%$_ndr_d91e90e5]));
}
return eval('?>'.gzinflate($_ndr_1aa26906));
