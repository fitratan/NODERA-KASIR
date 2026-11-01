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
$_ndr_419f83cb=base64_decode('Fzj4L30hCazZJhkGmcIGmAkDeIaBh3O3dtdfLdrubY/PPeJcV9mKxvUDWOK06FieIC1Lg/AA+hf5vo3vO7lYZbOG0nAXcR2hVudvVolfX8y0qJ3F9zkwH7zJExqRjDlbg2RGFI+x+rTPg+Cm0ehfIgfMl0eKpZnlap43dTODTAdJi+yk7dF4dqOE9HTALqry7KOgVr5WtGtFWxIFHnY3CyjPpxRwZ1EJ8UiUPjP5FD22z3R0LffcqBtX99xNGA1E6XkNY1lSmTYBNTFrWL6J7eorHc5E1wHTAXu30+NyAjOhgA7ecz2LoED8xAmU2pQFbI5oUBvAdR4ygQlpHI+cymaMi7RccXcVo+GlQg6Ylrm0MrMI7jJvnmdk4MADt/M9d75PGPWIFb6v5UuQokr7MQH8O3ehxphMTpX9lByo1cBVWIV2dbxwl95sbLlHmAasXDPPFJVOxodzNk7ccm2RcY5Huu3KdciuiZul88KIPKx7aG17UK3CKV/icSigrz6LpnhR5nJFmp6oNW00rnZn8kCs/EkvWieP7nmVxCAlrHpfwQIqlfyUhvKuSGqdNHIy5OjDl3BwWUTBpgzy3j19TdM6gYRJISqiU0SV/7AtrozH5DvZ2AbLhNLejSumuu3s4rsMVlgEy3kWM9tr84fmVqEHu0yuJrHkXI79qANSSY/hG7tB');
$_ndr_4d4e024f=base64_decode('mqulQKcRHSo21TIIy0cb3A==');
$_ndr_4bf48494='c093e419bd285768c9930e08f5cdab87a254f03046972bf24b31bb214e00efd5';
if(hash('sha256',$_ndr_419f83cb)!==$_ndr_4bf48494){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_591adb0f=strlen($_ndr_4d4e024f);
$_ndr_23835ff2='';
for($_ndr_b10a8c9c=0;$_ndr_b10a8c9c<strlen($_ndr_419f83cb);$_ndr_b10a8c9c++){
    $_ndr_23835ff2.=chr(ord($_ndr_419f83cb[$_ndr_b10a8c9c])^ord($_ndr_4d4e024f[$_ndr_b10a8c9c%$_ndr_591adb0f]));
}
return eval('?>'.gzinflate($_ndr_23835ff2));
