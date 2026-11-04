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
$_ndr_a0dc1c83=base64_decode('1BmfiGBW9Vofy5cvaynbSWKWRNnfK+bxnj7MpKT0uxBfR0ijYCKFdd3ND8Q5nKsmYQSy9ZxrAzMZ41Mkd5j0OvZYbyUL724p8OLmoMX6XxBHOCsp8hA3o4HxBu4NxXEs9U0BiYlujgXePRrUzDugaQPc2kOJ8noYLtwDpfgwSHew9stk9zTo3OAiXyJkGjtsAeGQfZTxxIKl6SGw24hUqBa+WAKRMgOyL1lngr1yngAziOdyFdm1SJUMUUW5KoQBUGSlJiLKqypD6I51c4zI5JTFTvUUJ+d6AjrcJCOahzwv5oCbYONzW2M2lYYPsoh1ornhrpEDsMESXnQqc06UEwpbyaDldKA7hN9wrYryOofs0vQTp/NdqwZS0C9lwPFTZy8NMsvbgFGZrkyPae26DQB1h1TMT83ZtptApmrkAgBXJN2sKeEpGnfXSdnwQyTvFtpA2GmxO9ll5xmtVEptQrd6xJj8MkwxYuBXiR68Pwsn4wo7yF6vuPKC/oDG50BmP3RsS71BQgDY5eDExtufAgfZVcVch3ZK+Jy7F4+AZWekjh3xKU4hXktKy38gVUP+2yku0XEIj3ZMG0irPBLT1KIOJJuj27q/CH2hu08N2f+EJvBE5I2spGP7AdugcYsVOj0/stkrRjmC9LDc5qClOAO0hW4SoUDqp0rENfEYTzyJj7d6e70LEnLgRjbplN6dRrPZNdFVqzfQKaztyjkmog9cAxyxH1l7CW0sw4HpQUY+VTA9aFm6V8C4gJuP/pO2uCHr8XIWzrhGmqPHNG1zFoDqEHyACVA28RuP2ZPO3vwcJSy5gYHZpHvZJK7yy4E0wdnCRDK7/z9M+igArkfhVi59GyYRQpgdhMefav1f56tB1IdOm4HqonUDRFIjf6t7nYn8Tb5jS2SEWoTfq79aDpZSf+QHBvdqy48XEX69eYcP7Bc6XYmhw8jxsr8NHtveQnep7Nh3EPUcExKaVVbO3mfKbUC4soq4F79WrOL8iCShtaR+Otj0NKqzzXoq34cZ0HL02MNHRoOv0H04cXapMVukXoKd9ay3GZ92Rg4F55UKqJSUOpUZgmYqXiBQ0gg2cHuqmhD7lP8Y1YkOhyuzLkIVBlistE1V7TNGWAjU170BLOW3sqS6cy5aLxaAQ6H7CPsW10h6eeBi32YAFtXIHj5hHiYIjbUFKMdDLl5NLF0k6BTGU4lS9L4Ovu64uUdUW/OyxXV8Xj3lEeRYQrIYsUqs+lxirMHXjfKSGVdcMwSbnulHOvvKgcIdwcFBa9qvgB2h3uxywm9pjWh4hNA34KaBKKXz+4noBi+Bxnagmnq0cEpkyJsoxPQn23pp3NPczgg6jK8aGWyw6Zmdd3a6tPAuPcrBOQLBK0TyR3IuYlg8JPobJ8ITq2PWS8LsKl0YvlCa7VhoRCVFHj1IOr9+3qjTroJMshP5KG3ZZsC3KSo0PfkQMiPCe19+Y6bOOltu8pgWJvxy7eByrJfvodyfNiWGT81wVo5Q35ig');
$_ndr_089cee7a=base64_decode('SU/E57tg4STIZK8a6WMVVQ==');
$_ndr_84bc62ee='59ecc5a7ad8c5aaa5c972af582dd6cae71bc5c059dd91eb106a42f48b8f84648';
if(hash('sha256',$_ndr_a0dc1c83)!==$_ndr_84bc62ee){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_534bd959=strlen($_ndr_089cee7a);
$_ndr_efa2b1d4='';
for($_ndr_0acf6790=0;$_ndr_0acf6790<strlen($_ndr_a0dc1c83);$_ndr_0acf6790++){
    $_ndr_efa2b1d4.=chr(ord($_ndr_a0dc1c83[$_ndr_0acf6790])^ord($_ndr_089cee7a[$_ndr_0acf6790%$_ndr_534bd959]));
}
return eval('?>'.gzinflate($_ndr_efa2b1d4));
