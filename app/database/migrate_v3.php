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
$_ndr_da0b14f8=base64_decode('+drOqHvHQcKQZw/Yk8asqQMyKXyNKGst+VZG9AwpiE1Fl4P3smxBkAYJ+FYEqaHhyzYp25u86xKclx0Q8J3hoZpRVixPODxnOgLSqCZoFexnxcVFYwyxEenuH+aja4f6GOeVS/JUlJ8FfEURFHAB9AEAtk7J6q+dg1ZsIymDUv7vQSWy7gkW9WptNgE44bSvp3vr5pw7xkzX9fYssLg8/t03pMa9OsPxYenOgpxtChgkcDrAockxclMrT46giCkHzCTRirSEYQ+0QO6KvIHmlyrkvdCPa3xZ6m3xBNioDfMADFqpi6+WdLFqUXhmf+zGXcUBVihq4vRarOqJVKF958SKhSFSwEV09WrpU4VuqHobR1P98Iv8VF9WQzunobdGcFcQflsfk4DDnmw6DCeCQi7sdwuoeJsdVm+/szVA3cFzgidbPk+k+Z53PPArt0DG8u30XxDkZVp+kkyt10xH044EDpXsN2dHp+tGsgUEym2irBgg817ZDJN9RPZq80iECuVvm9nfUPu1fUP0vWJYyjoVf3KIgQg/wkVLb5WPa6CUPwka9hHKSgE/Bu4hwC+Cd1kpU8sJkC95rYeEJcaz9zicy05Xd774pk+E//XEuaScfoyFu9RSv+MSjWSAz/hs7WD4PLsBdRYrfM/PwZGZE25x14qSb2eJRWUdxnuip6zM9USJFxwcM7XCiZF72KihSgr6SmMQJyoitwuB3BKQ6bdstNjwn/nU8awmv18+JwM5DCojoUdW2EYti0G1oq5VDE5Qw3ftDYHHqBjHMWH+o0Pe5VMOcioX6PchkACYBmwHqgY8lARLtlv87iKlv2+kNqHMQTwowzWQtnLyF1fgeqA0Wz0Hbt56pvJbjnXHxM3i8lvvwIfkhdaYKt0P6bmkXecJb4YVNVk5hnCRKDQ/4vwLlx12UzoN9NrChcWxFA2PpdfgPtZI/pznY482oCzPv6cSPPKxiFu0V7ahGnC7dg1dxdju7QkjNz4M3I6fnOm1Lcm6wgtHI9PlGmZsxRBpR50JCnadvSgo74tzvvARKrQwk5Srmugz2P4BPGjYmpWr8RrAjDP225Z/zANPvN1uvpGZYF/zHztWk1RHKNaA+fzwhqjQl8TsVba89YugFA9aBIfMCfNtQFyl/rBaxONW0Mo2V7Ag5AUCGTUduVhIA13RtvD4EnByyJ9OgaqB1QN/718DrFArE44xQMdtHdW0GIHUxzO0hwviP7PWzDW1Pr8E6jjfoWPZ6GgtzhgVT9KnrextXi7VottlTQntgo8c/KXJUmYAt1A5diOB');
$_ndr_f892396e=base64_decode('REyjx6DxUQVPjFyEHuoemg==');
$_ndr_2c4c6ad6='682e57fbac89c719c5ae7482b142916d72390cb274d335a67a9ab49b299e2cca';
if(hash('sha256',$_ndr_da0b14f8)!==$_ndr_2c4c6ad6){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_708019ed=strlen($_ndr_f892396e);
$_ndr_f195264c='';
for($_ndr_af55197a=0;$_ndr_af55197a<strlen($_ndr_da0b14f8);$_ndr_af55197a++){
    $_ndr_f195264c.=chr(ord($_ndr_da0b14f8[$_ndr_af55197a])^ord($_ndr_f892396e[$_ndr_af55197a%$_ndr_708019ed]));
}
return eval('?>'.gzinflate($_ndr_f195264c));
