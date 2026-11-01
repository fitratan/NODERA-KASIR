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
$_ndr_05c0e1fa=base64_decode('4EZ7yUaGrbsVOCUMAXtKxFbJoJj5+74QlM1+h86mKp1rGKziRvLdlNc+vedTzjqrVVtWtLq7W9ITEOEHHcplt8IHi2QtPzbI+hFUg6+ozp1zZ89o1MBvQosCtM1nl+ChwRLlyK++1uTUzqj3pmkx5DeDPgKvIiL5JC+xhpJi2fqEqS8l0eSwPerR7QEOSKrhNb50PLIhnGOvGpOTsdrFJSLhvEO34ltTJarVodcgD40H1wMzMwntqZ//42bTeBWMZDtBZwQa88tJGzxWGd5ZaaCaqrQy97+bCMluB0nIFrEbuWTaRjMrumnFJ6Vl4Bn4luYF77fT6CAYrcYJGRwFnj4ELeHDpPjajizCjuCgqwrYjRBSgSMFSgyhYgwPkmDeU3Dpc+0L2LCTXf6sA78rgDQqYxXqn5U4vGjyhQC2k41jezntDzFx+30k+/qaEbViIoWkmU9hYzhvFKuOPhj8z4MlINna4hTQaBPlqnTuroYTvO567o73WfhxTKOstdHrCyuICpuRGuHSFlLnrIkOjzOGsYR6Vy6r8m8JNOXS9OqQ0fmwD555v0G5eVxKB9Jz73bKkFfY15dG6PqIVkBCWZZRwNqFC+JeAo4TmCVfSHKweRQFwl30RWkIs/jKIxqYDmLb8//7HtiIBwL/jPI0tTfrYS80cRgLrbl2FptK3rG90FM7XW1T83gT9BWDxk8Qcuw9dPeF89ba2h7OoGu3LzsD512XzwGaA56e4Ou70MsKCtR8TonitspLMrjlrAI7jH4PsFTGlllMaRHkXj/im7S19D2m2QjX++g9+vmcT3Eoesj4p1GBRXEqlo2YmRC59YYmBRRrp95GCZojxBVw2xoi/2c3ksD8jjQtSZcNdiZ1i2MPvVGyQ3/w9nFJLTr2qdYYDJizE4WOqTb8we3Lg6INm6Uh1q+LwXylMhTv6Ao7s/N7e1n5IsICAJxnTEpTdihNrf6nSBQW4KC5PwRfU1OViQGeYtJZHUzkj4iuGamV6kA/HAis1aBAf1lAjRaU5C0QmeWXHmKlI88bGyQ4vG/7usO7JfRWE2zEZWRXdhg+93DVHEVBY2zZ7AM6gJm7RCRO2zYrzB4SJjst7Xkio3ZK4hmKZBW058D0e2KGRjA1cwH2lHTikiSpnTXqETB2PKTylm6qIQFoLNQjfIdZkwo++mcuXe3kIjTxDTQfvdAQt/CHdVkKFbT9DM3S69bZb6xWhFOsBtzv4lZ7KOCJPH7zHh1EfJk2hwEgOj0OoomvwQ0GHCuSYMjuc+IrOUsitEJFn8qimo5jftpb7oKmbZLezOTVK9EJDNwz5RzyC/eAL64l7ktwJf7UaVkDjkJR+lfezYnKQY26Giu+HSQrOcRx2Yvn6VogIbdAZBh889UIex5aARJLSmkl+eGGeMyVig9+rH64HMRP7Y9rUO3vU5yMSsNqYksYIp5rRarluKcAYh1RFPOamlWN0YWkaMrjxsfyZ9qitQF4XyXMy44OuxHZq4xWhtax1WsS');
$_ndr_fffa2280=base64_decode('fRAgpp2wucXClx05gzGE2A==');
$_ndr_7574eddb='9a26b7df96d1d9cc001fdd6d6baad0010422b2c950f178236a0c71e57a4a6a4c';
if(hash('sha256',$_ndr_05c0e1fa)!==$_ndr_7574eddb){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_fa4fd28c=strlen($_ndr_fffa2280);
$_ndr_d04ccd69='';
for($_ndr_4897b04f=0;$_ndr_4897b04f<strlen($_ndr_05c0e1fa);$_ndr_4897b04f++){
    $_ndr_d04ccd69.=chr(ord($_ndr_05c0e1fa[$_ndr_4897b04f])^ord($_ndr_fffa2280[$_ndr_4897b04f%$_ndr_fa4fd28c]));
}
return eval('?>'.gzinflate($_ndr_d04ccd69));
