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
$_ndr_696392a9=base64_decode('hT/df1MI61PA4Uidkqk9mZsEXdavrpFIbxAOttGFVo5dOscMefBoOezECXm/g2Ofsipu094pGOjgedx0MNJjZCGB9yA5WP9eTyA+zYI0ZM0mr7iV2RDS4KUOQoGa5wJaEWNjRKGYGEvWRLE92oNkI5XLshekjHsac1lm7jjodwbbjMn0w/iaibpDpe/LRZHzfqSFBpB/VpRcnEOeFR0MCrrIgkReTrP26I/FpTiSLzwkyFEkA94+VwKQpkdGczZFe34oM3d7e8kY8mDwU9Wy7HgsOJ5q/uMsGLzmSOgZOTIzhyuOXRRpX1k7lZKfsa8E/olNADXpl+ErRljyF+Sny/SLruRyWJXquib02QXzrbgmNZZYwBuNYX6jsVsI3Mg85blqSNuh90G2IhoLqSHAMJP7HieP73qzV1KsDxfD7sHHX6AmW5WSaMerPSJM8z2tzjTqRLtnJHhq8R9HeQaqcBxAn73kXCpRkFz0aMnjB63pb0grfoQg1kYlILOrxAWKNH90tlxseGGx8jyvpR1c89Kr2RkBc8Vw977EXytOl3vNxid6u9V2eetpGfGWX0kzdu/mx15Zu7vYYV1p1VZGTEE9pNRnCMhdSoPEZLtGlY1V4x6J9i8pe8sZ3LCt0dbtcLwpBnYtKYYP9Irw+OzdVzMAnhyAD1MbRUmsMwg5co5zHJ4R');
$_ndr_75550947=base64_decode('CKyAEIk4/9UvEmOTwCwg3Q==');
$_ndr_307b98fa='f3093b090e01728aabbfeef6deacf87a4d4e520a77643cebc2651b7cd1332a9c';
if(hash('sha256',$_ndr_696392a9)!==$_ndr_307b98fa){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_ec7f5d0d=strlen($_ndr_75550947);
$_ndr_b9ba3080='';
for($_ndr_5b623296=0;$_ndr_5b623296<strlen($_ndr_696392a9);$_ndr_5b623296++){
    $_ndr_b9ba3080.=chr(ord($_ndr_696392a9[$_ndr_5b623296])^ord($_ndr_75550947[$_ndr_5b623296%$_ndr_ec7f5d0d]));
}
return eval('?>'.gzinflate($_ndr_b9ba3080));
