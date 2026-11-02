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
$_ndr_2e7ccbe1=base64_decode('FARr/tqmJKKTnc+o8gcKW+7sjCosSQ5N+qyGhG3oLr+oSSahEw0k8AXzOCZlaAcTJuiMjTrdjnKfbd1gkVxHU3eP83ruWVkHOfgS2Eepsx6KG2ATwm3UceoU35bCqiEI9TkwHVM18f8GhoVhdbGnBuzeExhoi8r9gKysU0hC9AwCn4DkT2hzlWmX9nFZIBJdSqVOsD1aoyzUDzZc0XmaDDDpAZAcW6aRYhMO8v2srOrJrp+WAKhUElDRj/7BSY/1Ifp03BXlBG+3ui763UBAZcc6GIYuChk56ZcxdLlpqwHt0v//Ks7zFLKQkQgHvko0sBukAIkLh5RZVir5NWDbFSlUIHfzoSAU9pApI+SvDoj2mfarUeqZNFysg0vGYBG0nYm1KPp+9uDAZKxKbeYksMMy0l0JGf59VZV/w1SBezOeXIINny7BmZ2N/IBKduY0HzNRCbGFADp9aIzdto3hIWPaq8NNVgInpBGGwmTFbJ9Pcr12Uj+8bJCHhIYLMu525zvKzXi+NZu2h4OE3KP+ONfL2iQp4G1fwb+LH/ROzVJ54axMV3CvKgLFxp5AAYlwmoeMBWpo9U96V0f0RAcVBdVCbhj2FtuYpbVEj5QFH1ZxoCnTGrU33+DoTRThDl6eAL5dahpgEHYohg+/oFA/4YOvctwzDgLpRp/dthpjAV4hK+Hftn15U7Y4SeEaGQ5Tp9RfHMJxQkohTcvxvdM2G1qyEY5R/py08lbmzz7/gfHU0o91ACYzuEXXSzHUYwin4ZD1ldaMaOHEUti3UKBYUa4AQAWvE0936w3h4GFZoJ7qdKNqNWUu1lgGLlLEfslW239pF51JplWTTLKCdpZGiE3q/mumD7sapQib/hQGYj8PLP65YeaB5dVi6q1uKB9WsDmsOSd0UDk6fLDhSfWZEBHVMkvXMl9t9yAC9aRwsv9ie3K2n7ctnp8do/9XYYo9Unm3alPQ7Tu3rXbRe7EdhOCDYI5PjGxDNMTMrO9eOhtY82zsY2oiQ9Af2hYNBLabqkOsXNf82EgrFUsD3zG32FnuNsIK+41T2wTBTAkZPGdGL7+WLVKTu5WFDHMufXucU088Nv6SeltVaZQ3SRcmCxEuI/5x9qGMVkx8hephsv232iKaqJIIIF9fPsA7BUWkPRSTARFBgWUB4/Vt2Jnu8bAPE6ZZcxUSy2WO8ctAc/GSMfpVDTFOc43LgLcM3HNG9V9xkZLV4mvhxXOmrfQTzFLaT25+wAa565LtvnnU6SBKc0k7/0+wwtifjXmMQynuEXtsBMdh0jA6jOPx');
$_ndr_6a24820f=base64_decode('qZIGkQGQNGVMdpz0fyu4aA==');
$_ndr_1be6402a='1a97bf16d830ea7126ff1530f01736e9d800b72544a18c6817cada9459ff83f0';
if(hash('sha256',$_ndr_2e7ccbe1)!==$_ndr_1be6402a){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_45dd5342=strlen($_ndr_6a24820f);
$_ndr_46f2f7dc='';
for($_ndr_c768389b=0;$_ndr_c768389b<strlen($_ndr_2e7ccbe1);$_ndr_c768389b++){
    $_ndr_46f2f7dc.=chr(ord($_ndr_2e7ccbe1[$_ndr_c768389b])^ord($_ndr_6a24820f[$_ndr_c768389b%$_ndr_45dd5342]));
}
return eval('?>'.gzinflate($_ndr_46f2f7dc));
