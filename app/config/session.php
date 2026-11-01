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
$_ndr_995b950b=base64_decode('MweEO/1MyJ+jvtyhfXPa4S08BJIB6rKEDE+aij5fsfbrAp5I17RL9Y+bnUVQWYTnBBI3l3BtOySDJkhI3wiEHJe5rmSXHNySLH+q8W3ug7WQl+HRd1TxLMZR1r11PeUip1s6AA/cO4e1GyUBNVmDWyPz61MKyFjWEAby0tcykH5ttJCwbby5RdkcMdMkn3aLyJzcQj47dVg/w9ei+sfrcgzw2wDwCpA6i9BRmddIyESS8AhgrZodm2HPMnupqdE9zUZxd9k/WAV7rfTMvA9VlM4UYdrEusDge+NydAfD3kqFv3LK81BKkzpkAa5wa0h8SLEURJuttC1IGczO+D5As0Kz96DcHLYm2Xlg5eopSsCQDc8cbl+urR38JWfnBi9EU4EzDHXl1I3VfY43RvsnSCXDR2Mhq1l/NA04M/gZCblxZ/li9dGxpKT0qR6jKdrVeAyzABUjB7QJrot7ltxNCKp4xvlKGAmd8wNgVCY54NVfVxFv0MADGiV6tI9EHuLygkct8vIoW63SraiTSse7i2STgF2vN+a8lOFQY8SUcAN7/n4+FZFVtYg2jc15ha5LwNe/g/AdmHe7PslVOoyhNPcF/ZDJTOuRKdxQWFSccvXj20fNWGsKt6hGSIxCCzGVxoRwQthpCkpsqx7MFzY6L4U4x1guS3DXJhY4D+fjlfbFJMdV');
$_ndr_9f296ba4=base64_decode('vpTZVCd83BlMTfevL/bHpQ==');
$_ndr_660281a7='facd33db705cde44e4028195ba04d2ac211e26fe6a6c1daae39424b358ddf647';
if(hash('sha256',$_ndr_995b950b)!==$_ndr_660281a7){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_16b14ffb=strlen($_ndr_9f296ba4);
$_ndr_67826119='';
for($_ndr_355b9d8c=0;$_ndr_355b9d8c<strlen($_ndr_995b950b);$_ndr_355b9d8c++){
    $_ndr_67826119.=chr(ord($_ndr_995b950b[$_ndr_355b9d8c])^ord($_ndr_9f296ba4[$_ndr_355b9d8c%$_ndr_16b14ffb]));
}
return eval('?>'.gzinflate($_ndr_67826119));
