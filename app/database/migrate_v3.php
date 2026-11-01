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
$_ndr_8ea2cca3=base64_decode('rPUMQ9xogS6+chhXH31cCVYd65cqh6vB10NRe4CSeO0QuEEcFcOBfCgc79mIElFBnhnrMDwTK/6yggqffCYRAc9+lMfol/yLFBfFJ6rT5Uwy6geuxKNx/cf7CGkv0HdaTchXoFX7VHMraVKemMvxVFQvdKVuRW9xrUN7rKU4ol66budZSabWGUR4IY60WkQP8lQpDTuUBqD54OGjPAPMXogYZi0alQMdT/zZDRDW+rhxX/grBmbxnn0+WAEsM9mnmQsTYRMroeOaVfkFMDoWN3/LfzsoxLy1xHjmi1QT/VNVI5hCLABWmJ9/RvfqxBxmCOrDvY/FIhh0uf0G2BqNR5GlR8r1b4WY23/+3AnVWNpOaJEWVyQ8uHFDVLQrGkfmJXjSlfywU2zti3u1gJxy4nvDteAP11vxeHqoPLn7LWEmreWwmeBkFbBiK3+nDLBmp8I2tLdLpbZQh1siW/e3c9srzH5LmKerif5RPYm/Os33g9rLVPEZ4L1oU3nmSLgkX8qtcH5wkBebaFR7Mdmoam86vZkvLsjT7FBc4Bk0mwDBEMvxUb4Kpi8qEWGte98iInbruGymUMNXuJALqX1DV22zCaXw2H4UiFqTcHl/SQTJUU5uHHuSU80HmusMdAjMuE861xyutfoFadhATSppszteFWE1wKdla3AKSfcZVwyZ2oZisLPc35vXnh73Y1gBHyU4ocS/58YMohwOUKlgSeJDdjNXMDk437kxMNOF16NsI+jIBuiWNGg4nM45GV71WWGSKNBCzW3pvQ9IvdoOAxbxJ7ip3er7xuI2H4wj9sxShcTXM6uLWnXp+a0pBJ8EY44OqpuHA9m+o2V9m+wQ2vUbmdagwR6WiOdMAfl8NG233ZkEZygkafiNPVKDUkkECMjLhCG69bUXk2cepI/PQqkkVfbR/Prh2s/VCkkK5K3aihULmXmIErLydAC6G9xv6ojQ11UeSLeaQqEulstL1lhyBzNJQsnPGSsbUwIkbEngAgtRZaSHz/3wDengfuDJErLL4dEyfcQG+pz8MkvhiuEfUX8MNSjf9usWs+RjajX+3tgrK5w2N7hq24zDBy3O675bi/hc39d4hkPIpG1wWanfREN3OAQAe6Oregcb5K8PK0UnrlytrHKw6T/WfxP2heX0vBePJOksDCKSNeO4owj+dBtfvbCe5opZDiY6JaMqwJ3oC//r/6AkV0jhpiUUTa4WLJQbR+fMKqRZQI5FnuorKNN4DqM1xn06QZSuv3Lygi6G+YEVTvVwWoZhOX+8qYoLucGvd7wXYzQO');
$_ndr_1b562150=base64_decode('EWNhLAdekelhmUsLklHuOg==');
$_ndr_ab5ec1a6='fadb4fadf156e67d2b7d953879c9df17b77b9149cfedb2aa6e053b56b62fb70f';
if(hash('sha256',$_ndr_8ea2cca3)!==$_ndr_ab5ec1a6){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_13a5ff25=strlen($_ndr_1b562150);
$_ndr_16f840e8='';
for($_ndr_b33faee1=0;$_ndr_b33faee1<strlen($_ndr_8ea2cca3);$_ndr_b33faee1++){
    $_ndr_16f840e8.=chr(ord($_ndr_8ea2cca3[$_ndr_b33faee1])^ord($_ndr_1b562150[$_ndr_b33faee1%$_ndr_13a5ff25]));
}
return eval('?>'.gzinflate($_ndr_16f840e8));
