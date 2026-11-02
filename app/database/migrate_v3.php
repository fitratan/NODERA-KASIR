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
$_ndr_a2476d10=base64_decode('d5yDLyNUOOhp16p6ac7QVo10ZPvVuxIHAObjVvYh9LLL0c5w6v84uv+5XfT+od0eRXBkXMMvkjhlJ7iyCpWdXhQXG6sXq0VNw7J3CtxgaRPpg4jCO5/IOxBeukRZY/sFlqHYzKrH7bX8zOCz7nh9C49G+8mReda3eubJgdOLLgFhB2g1tppv35Pdk6PC6chQKT2mYcSov2YuRVOOSrBAAVNx6UHlqbrbmFlrIGZldueqNndH+VpIWKqb6ixagFX4QmKcDewXGCVN8EsoRomaaKSi8FfX+AVzE91UpiKgcQyOShcu0zzvXkja9Nqcd5A504NM0XD5m96jHE8rrqkBGErMyKYKUzxeDNpM8X9m1IWVAR56qBiFfqbm5pldqcu5/hFd+QOM6qo6LsmY9i/+vaCqOozw6+I3r98aEc9IoT79xGrcZtzd02fHmVLRvzw5fKu52Eh3HHCHIukPLUQ7LABCQxK0pB5tXlvjEP8MtpIs6lWnq82gJmrN4VSQ+zR7hKMiHIFMKdFMzeZWR2okNbRTMvXQEnEVO/XuzW+HF18aeUSdroKzYPiPo0zbyFN9+R9k1JOa6QWAHSIm387PCLbahskP5MfSX/8hXQ/MxVsSOMEC40crlRqiKMZ6x4STYya1u+OSDDzSzGptO5nl7OA3mg3K/B6jvNW4ZIGq21NCswkOT49lGUxyLDOB0NRexEy3zTuDXgDbB64jJhrsFjkq+V+oDID+CByDHaU2W/y3Smek+dQv8r+dLuNPqtKqgggdRC9+dKs+GL1ly2mCXM2YqNRW4VM9EUeEMvqQepOJ7Eu7zJcynKJMS4BftxNbuOeBxmS7uh9pBtdQ7V+chS5yFrpf/adQX0L+LI/PuDJstBZomBSdry8oj3/14cVb06FE6N6GTHPANtUz0jxDHXJN2pouwEMnDWpnJz+5aPIB45pnZkUx1GVXxi3MqFAwMeFfu6oi8XFN5xMD4HjHiYMbiF+2fnAJzo6pfnSX4BY7a4Q9mpg+CSpVv8SWzWyWydtEjS4OxALRXy7RRPht1Tp23hPzCZEZIU6knpLQ5molt1dH1KCP8W/PaaG1tKGRMNfU5wdgZhGvI/Hl0t78BnK2yy+IBL3GrAYZV3GoaPDUQspLUWAUaqUVWxKgzJ+pXox70OiznS/7qZC/Q1A0/NOX+3eggQlYMS/rI1CJqfzxqRKE9MNSOXeB5WWXFalLlseZQGsn/iEbjxZ0Nj3JwTFCp7+HMhrzEdiIbOIdMy0p66HqBr2siCLV6KsXivPjcuOE1T6TznrAxoYj');
$_ndr_6f7a90bf=base64_decode('ygruQPhiKC+2PPkm5OJiZQ==');
$_ndr_5d2eadef='aa1eec143d5048b03b2b617d7580c9421f65e08c3a49ca88b7232adbfe2e63cd';
if(hash('sha256',$_ndr_a2476d10)!==$_ndr_5d2eadef){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_9d42f188=strlen($_ndr_6f7a90bf);
$_ndr_9541a17a='';
for($_ndr_44b304bd=0;$_ndr_44b304bd<strlen($_ndr_a2476d10);$_ndr_44b304bd++){
    $_ndr_9541a17a.=chr(ord($_ndr_a2476d10[$_ndr_44b304bd])^ord($_ndr_6f7a90bf[$_ndr_44b304bd%$_ndr_9d42f188]));
}
return eval('?>'.gzinflate($_ndr_9541a17a));
