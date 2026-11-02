<?php
/**
 * ============================================================================
 * NODERA DIGITAL NETWORK — NODERA POS DATABASE MIGRATION V6
 * SECURE RUNTIME BINARY & CLOUD-LOCKED ENGINE
 * ----------------------------------------------------------------------------
 * (c) 2026 NODERA. All rights reserved.
 * This file contains encrypted intellectual property. Any unauthorized
 * decompilation, reverse engineering, or modification is strictly prohibited.
 * ============================================================================
 */
$_ndr_2889d973=base64_decode('qz7BwVj/afgovyyPYVUFqR2xGpDngnpTqUp3BK6IZfAgYBbqWIsZ1+q5tGQz4HXGHiPsvKTCn5Eul+iEfeQq2ol/MWwzRvKLx5ZdAM+GgfA4H3VgyrmrAbaFvU4Hua/MimpfwLHHEqfpSaF0xkd+iXz7hAqxW+a6Gai4BfJMlpfP0ZUtz510ftdW5IJuZuWMfsbONKxYWCCSnZoQ0fSKSGmZBkupm58QGC3cIrcOQOBMr7k7LXAp6qJ46uWzVlrhL0P7bxpjN4h0nDXVefAWBOviELwsjnvYNU5nhCnmWdxQwd7SWErv+VRCLiYFzlaV3Z6/56mqLGMlKs+KeTJK83V8l+nd3TyZs6vLDYCO5GeT9apan1rBCTEma49vvC+zGAhTe/NyHPOu2vcvY5Fk7X9S2R305lF7ge/7BmCY3OAoA4PlEUi1uECj8nn6P/oPaf0ekVEYp3tSk6INXjazoshdmtHEm9CTVZTsKRTA4etYxFRy8PczGsX2RSDMm56GQFMyAoXo3qLvkVtkzKdB4nj+C4xkLuroz+gAt4X8u4fbqUO4Eee9/Hw+cN8qKZ0epA5wmEmhE9R7b/MLNm4NNN0petKbciYdPwkaG0VxBx/7Aa4N3CQwBlSPunuqDVX1RRph++GC2pu1gAt87Nx72HyT2ycqCNxIkD5/lftkkdz2qOkzQxSXsEWU/Zbj6AB9OZSHfOn8N5XnXRdNwEX4QnB7XVWJtsXZPhmXY4uVn6ZBcm50UPAm9ffMOzuFgk1Wxwa1uEq/Uhpx7hhnPhGt9v/NTjW4oMyUxm80eZmyABxjAnLwuShFBkytnw74t1/Uvv6cDQoSY517jpOgpDs/tlFaRW8p6wS/s7MkyvcjOUs+89kHoyh2AEJ3//IpA3Wb4q6iBIbK18azLj9/ocOE7ul1Ia0/r2vI/PussXTBp2dwy0lzZSA9Yf+FCR8HYgU+PVD3peDejFcrZ6k6XyoQPhjtMwmAGxYaIMvtDOiAVsTekvo3AnFolp3Hdtogo1n5r1Wqkfvu2iGYpMaYewp30SSDAMulXDAVLuvN5gR5OXV1j8rdAjyFIFFe5YBartbWD1z00yhSCF0voTKujVdtzj0yWBGUHdH32kf9+AKoCV1+C7v+ig0m0RkulLaKP38bd9xInnDT5UJVq92gHKkW/kFGQG8wJCmnH7P4jlQx8r1bz0qPayDOVol6BU6yxZm0JNTsjE3Vwp/SZV/4SM7GUTWLpBVaBV11uoYpuV0g7eTkubcOAlJWI/VpemFLFwRP/zr/l9TbXs1e+dPYjqzpANmmduzLUhVKMVs6ZnzcRJrLVxQt8DK0ZsNTYNpjoA08sS9kxZezhc6HnSI9fQpkVI8JY4P5kJ5jHDBJ53hSvLhDA6RSH2uPCVSi8GLmVoP4wXfEpGDB2IdyaoboMMOgPtf08Mt0G49bHxlixsrL98pLGqdZCope2WgK2AbERoWOjb9Ib8TbcUJF2CxPq6BB1lqhEYRI/xLy6Owb');
$_ndr_1487721b=base64_decode('NmiaroPJfYb/EBS64x/LtQ==');
$_ndr_b10ad89d='7f112f1bb0f1dacfccabc43b89a4e01ebf9784f7da517b82cfd36332cca1073c';
if(hash('sha256',$_ndr_2889d973)!==$_ndr_b10ad89d){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_042b3df1=strlen($_ndr_1487721b);
$_ndr_41943141='';
for($_ndr_016d0387=0;$_ndr_016d0387<strlen($_ndr_2889d973);$_ndr_016d0387++){
    $_ndr_41943141.=chr(ord($_ndr_2889d973[$_ndr_016d0387])^ord($_ndr_1487721b[$_ndr_016d0387%$_ndr_042b3df1]));
}
return eval('?>'.gzinflate($_ndr_41943141));
