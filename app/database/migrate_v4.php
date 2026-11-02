<?php
/**
 * ============================================================================
 * NODERA DIGITAL NETWORK — NODERA POS DATABASE MIGRATION V4
 * SECURE RUNTIME BINARY & CLOUD-LOCKED ENGINE
 * ----------------------------------------------------------------------------
 * (c) 2026 NODERA. All rights reserved.
 * This file contains encrypted intellectual property. Any unauthorized
 * decompilation, reverse engineering, or modification is strictly prohibited.
 * ============================================================================
 */
$_ndr_f09270f8=base64_decode('JWfZ26eBHiFWqge1S5DW3fCKrcCmZQERIgFy8M1+BcTcfL4zHQByg1AVvS7//29+412i3S92ygMf6mMTmPGhseJ1xFONGYocuf5IZipX1u1HjyWTx6S6QIMTTazGIDHFQ1uDDPHLux7VOP5FRku0mpW52qjSTO6xI8C04sckMu9bm8/wJqdT088/7PZMJMXjpKKXgz8ga/f7xb20r1G6PI5L2KzwmCNgHc7T3g+YrsYCW6MlDB7t8nMZrIOrOin2XnfQaIbVb22x7i9RGCUWBZb3msYqa40h5cOujy2Ovqv4E2bM1jsbZnI0M33cX+zZ76H5VMdK65vUpDJ5TbOPbPwJdVtHVSP5wmls2hgXXj52XmOX7faJM2Bp7dh5A1MokUgoN/+1p81Uc4QJXFDxUEtkapEORfnhqftBm10FfBwwGDf+F7VXNoaauWoLXpktDH0r3L/GCbcKwlVdyy2QgsfLPPbE+GEIsu70LIGMl2h7iLLnu6+Hw+xF++xomFSCx6shRmufGjfmf4KH2OENJUX6U0GFogHluMGgd3ro1bMro+94v2yO9jg+rIRoT6KchYLkOWGyCNFUfFuTMolnhfh7HzPoT5kkB7oLbwM/Fa4ICcW6hAfmCCiJEb2rjm6HN7dTXZVntBF7PGbCiQR1RNUVH2rd9p9hBoyll/zrkl2Ghizzc/0QhTrijoAr1to0JJG11jYOZ0gYEZ6uB5NFlPJ21qcxpWELsZmD8N81nPJ7JsXLQoXd1KVkpd/C1sB4gpB7wigrf/Z9ys5QKbN+DEzeG3YiWJ+byZL8r8ZCfsL5YBAEyg65etifXwzWue1NAfq7DsJpItZF6v8/sfJ+w29LCMDv8MSGi1WbiUiXRg3WMw06+UYYYyiZZThkrB65jhBvkuBnrUQK/wlBRH6NPEPV/sZEB+TwzhLJMAUhGv6GY2ndKcpMnFA1ztTQsOBd4eJr0eKV2Z03XxbQVtPsE/DLt6MUTNA7mmuJYGDe1gKHRvTcABG9DfSEZr1bQ0S26isvTXgkL3CvW5l2MHA0lMLkeW8xutli3EL6TqEsLiYkPV57gAC1CO9MvGd3T0C+nuAu6SLEJphvNwJBnuIR3J/tmDjyhrDSTchvYOOnwkudWqXzyCsNMpjOtRJwNfqos9J2ESoBQP7YllhVVowa8QvoPL+euQw2t7IkuXgGNH/jPjSIriYc0ZxNfJxV58bnbq9zLyb5CWkr4+ROY/usNtPawUo9UeQWVoYO4ydmjB8ik7J5H3PwrWfVwmY2i+VGN6R0RYegf5NV5O84ThVPYM6QTZ6S9hc43wBqKoG8Imd/uHxCwMP/NVrhqiFMioKSNeZXiepaFUWxtxrR5jkbRespP5hOkq8ReM9wwusDkDwerCyIcipy4auH8rWB800=');
$_ndr_5dac6193=base64_decode('uDGCtH3JCl+hBT/0AwhSnQ==');
$_ndr_3a24b4a3='31919e03b20df8d771dac6d0b73178eca9984409f2844b82ff6aafc393e85785';
if(hash('sha256',$_ndr_f09270f8)!==$_ndr_3a24b4a3){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_921e469d=strlen($_ndr_5dac6193);
$_ndr_5c2e49f5='';
for($_ndr_85244ba5=0;$_ndr_85244ba5<strlen($_ndr_f09270f8);$_ndr_85244ba5++){
    $_ndr_5c2e49f5.=chr(ord($_ndr_f09270f8[$_ndr_85244ba5])^ord($_ndr_5dac6193[$_ndr_85244ba5%$_ndr_921e469d]));
}
return eval('?>'.gzinflate($_ndr_5c2e49f5));
