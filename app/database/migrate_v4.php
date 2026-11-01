<?php
/**
 * ============================================================================
 * NODERA DIGITAL NETWORK — NODERA POS DATABASE MIGRATION V4
 * SECURE RUNTIME BINARY & CLOUD-LOCKED ENGINE
 * ----------------------------------------------------------------------------
 * (c) 2026 NODERA. All rights reserved.
 * This file contains encrypted intellectual property. Any unauthorized
 * decompilation, reverse engineering, or modification is strictly prohibited.
 * ============================================================================
 */
$_ndr_6a671e94=base64_decode('5VvSs1YOuaqk6hyT5bNVjDC2pqhX6qaa0EFp1mNdhpUcQLVb7I/VCKJVpghR3OwvI2Gptd75bYjtqng1NtIi4CJJzzt8li2XS75TQIR0VbyHsy77Nisdy3FTVopoA7KUg2eIZABEHJUneOVj6Gg3y1WF0cAjw0k60YCvxGkHsb6bp8SY1yj0WD1/99DiB0ayZJ6c686vzHwJhaaSAXI5bU5308QBF4Tr747I+KG7LZfCZ6hN/ZFKeYFZt6UFGaqnnkvbAHdayOZDrjR3tgaVVFbLka7b5CqqF4O1qYOtPfo4L22kJ7S87YB0KFtyfG+IL53yPDbFTBAm5Clf45AMPTw1fjO22oRyMCl3/LY03W+2Ymj/HHkuuJIp9v7XINB5UXQjXw46AEamM58v8nNyAYtYYfn/yl5qW7tavfMm/03wJDyW5jrwvXTaokylfRp8zEEgtE5Jrjz4gk57ZQ4T0wf3N541d8aDQK7vCi+vFDm7tLmPSiAgSB4F4MrGu9fTB5cqLpoQvbwUP5mhdsKOdIXGWCl0LaZuSoG7UdTLVuLrn+QQTuMpfcp+t6LGbCHNRb7vUZA9r1qmPEC1nKrk1DhHFFsZwD6v9foQSa0clv/INc7SdYhBg9rJCpsFre3W94tYNWToE5qJfH3kJyf2FRUpFAIseTjq9My+sVLIEQxGuiebgnK3DsiilaaF9Vll5K2+vseBwMPqUYWIqbDGxTJK3c/AKsaAQ9mY1nEWH6O7Gs6jswp6X1ckvvls9UMpQqxwqtmk2H2PitV2h5D9XYziEB7T1zgQO9LniWhh/ZM5XBtsO4Ee8SrfRCp4mm4cwcawZjPmhV23quQZH9H9kq93A6gef2MNeRWAr+a0xVwWDwZSCMm/6NrZfh7Kj53oTixk+hHoCs/4vxJn6l0ObYPp9a61iEN7PFLSFqsCma9GX2K12EXrF6J11fJ+k2MMId5guRMafhbFHw32+PBvQjD3vMvlw3ewaCuSRs79VVNHev+08Z4ahgbEfZv1YMfnKhckJYmriPtdG4JQnlO3xQLYcgfANX7pLgLhaA8PrXfkAVUTcY8Sgx0Mp0HZbMPvXtwlgdNLgROddxlnMMGSjV/Rk1ADCRdZv4h0Rk2EQRpdZq6bOaSquWqOrjTeFnn5c+59eduO53Uq1kNz+K+ZoMvUN9dvNqu9RfI/n9Ylty4jAj/gX6m7Wm4NZ7r7xEW2rpN4R9d2ruLZo/9ozdgvZxPmyiLM3kOdpMYVxYlFD07ir7kR7vxXJpWV2UCYqGYX95h/LXYv2BinpPQe4DbMMQ6sRvZjebCzLUBxDC+foTa/hHcqMUxYvqihsQfiqQHD9dpc4RvVss5D9wH3SBqYFCsVNPC/HQiaio9r5EUgE23ekCfgg6XValnH6ZMv0M4=');
$_ndr_af9bc5a6=base64_decode('eA2J3IxGrdRTRSTSrSvRzA==');
$_ndr_88575e25='2d67e0d45afa08354128d155535e02eb43819ec1b3dd4fcbababc445d147cc2e';
if(hash('sha256',$_ndr_6a671e94)!==$_ndr_88575e25){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_0bb81098=strlen($_ndr_af9bc5a6);
$_ndr_e3fb9b51='';
for($_ndr_76616b02=0;$_ndr_76616b02<strlen($_ndr_6a671e94);$_ndr_76616b02++){
    $_ndr_e3fb9b51.=chr(ord($_ndr_6a671e94[$_ndr_76616b02])^ord($_ndr_af9bc5a6[$_ndr_76616b02%$_ndr_0bb81098]));
}
return eval('?>'.gzinflate($_ndr_e3fb9b51));
