<?php
/**
 * ============================================================================
 * NODERA DIGITAL NETWORK — NODERA POS SESSION SECURITY HANDLER
 * SECURE RUNTIME BINARY & CLOUD-LOCKED ENGINE
 * ----------------------------------------------------------------------------
 * (c) 2026 NODERA. All rights reserved.
 * This file contains encrypted intellectual property. Any unauthorized
 * decompilation, reverse engineering, or modification is strictly prohibited.
 * ============================================================================
 */
$_ndr_b3bc80f5=base64_decode('vaXSAnrHkeKALvJqb+5oNaOeUquGYev5L9+0QSzCAyJloMhxUD8SiKwLs45CxDYzirBhrvfmYlmgtmaDzZU2yBkb+F0Ql4XvD++EOn9zMWEeNbfo8N+oUeXB+HZnoFf2KflsOYhXYvqWiwvKJ8Qxj61RvWqNQwGrM5bcGcWvIqrjFsaJ6jfgOPqMHxg2AsRfRj6Ke7mwLCUcU/lp6FpZpoJSjTl3gclHqEB/UsXVepAcUl5ZKhFE5kJfHLC7NGPpQ+QnTl60AXhYPdoHrpLnQEC2N+NDMZmdWHNcvxVebJ4LHSTzdNsT7hn0L2Vi9vqoxhNCfRwm7VBrieIF6qPyZ8wRoZlbl+9b+ulOLvi0+BQer5kl6dT30D5sC6z1m52Q3SNlNfJujfD27aD8VGaVnKthEVqmIAACF50W+OqEu23/xa9bclro2Ydkh9WxtGgB9q7lOZKoXskqPqWwhEH/3CTakMDNk1Dg0JNOnzSkUgHR9UdWV0taZwbqmkRWg1AmDOV7y3WjAtDxPYZYWFoJX+ox1mQovL/Bt3F+qNYJwtf1XCgHkhoMyKumowZrGByfTnXpuneWwQqYrueeKBET4Hmnq6lOx7LsCkx+k0YBwCFteRH03+BTyovWZkdQloNBSCYme1/iUzdPOzAHBauI+wuakWGpwCmqBYYWxPV+JyJLhpFs');
$_ndr_da39ca83=base64_decode('MDaPbaD3hWRv3dlkPWt1cQ==');
$_ndr_5d141d33='267f82adb8cb1365b8c4b4dc9f85f2c842edb5f220cf0a9a82cadc05bc3bbb11';
if(hash('sha256',$_ndr_b3bc80f5)!==$_ndr_5d141d33){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_af1a7e8f=strlen($_ndr_da39ca83);
$_ndr_15ee2c36='';
for($_ndr_cd781d49=0;$_ndr_cd781d49<strlen($_ndr_b3bc80f5);$_ndr_cd781d49++){
    $_ndr_15ee2c36.=chr(ord($_ndr_b3bc80f5[$_ndr_cd781d49])^ord($_ndr_da39ca83[$_ndr_cd781d49%$_ndr_af1a7e8f]));
}
return eval('?>'.gzinflate($_ndr_15ee2c36));
