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
$_ndr_50a87065=base64_decode('JGBZ2d5LFf0eiSZESTDKm/GNLcLfrwrNaiJTAc/eGYLdez4xZMp5Xxg2nN/9X3M44loi31a8wd9XyULimlG99+NyRFH004HA8d1plyj3yqtGiKWRvm6xnMswbF3EgC2DQlwDDogBsMKdG9+0ROuo3JS+WqqrhuVta+OVE8WELqlanE/yX21YD4cczQdOhNmlpaUXgUbqYCuz5pxFrfGmeo9MWK6JUii8Ve3yLw04soADXCMnddTmLjs6jXKpmjWwX3BQav8fZLH5zQ6gGoUKQ5fwGsRToYb9reCPfi8uou35FObOr/EQujoXEoze//Cf7qZ5Vr6A4EechxOITxOTKv0O9Vk+nyglikpNKxq3Qnh3WeOVlDyC7yhKzCl7o09ukE+oNYZ/rBEcUKX4XvDtFkpj6pN3j/I94dhgal+lYFoxH7f8bn9c6s65mJsJ/oVrDXqr3sYMAmtC4XSsyY2MxMbMvPS9MmrU+s3V3YMsiy56jzLlwmWMH6Rm2h1qOEjExqyhRBJVEeuuXKN22kERY0T900P8aAo58OKBhnhIyfUqpG96xqaFKnAdjXVq777ahIVkOxh4Aw0cX3piMCl7w/l8nzGRhZL4T5kqngGfCegJDkW4/c3t1GCqMEypLnLBNrDTX+ytv80zH0czi6RpAtQSn2ikPJS9Tq+EZv5LjhuHgazxCjcbWXLBr3EpdsZyJZY11E/EbJRQMr9fBTNZ0vNxVqVIb2rX+bqiAd2VgLR6IUXJO0/WCO1HhC7Adtw+g5f7wFHhdCo16e+hKxNiSk3Zm3RbkpRHgbHdXsTiYoT4Z5AGs8SyppC8fv3UGfELAP07DLujKQoNyd7Os1JihW5MiMKWOs9aw3a6eEo3WkvXNI04gIwTv2C6RMlmDAL/jxfvkJmtpphC3CiwRt6RekLSfsQ9ze8shjHowQeBBriHZOnfUABHQBgW7yXSEPwb4OXr05tf0kF/fDchVHPwVfHMN6Fthtvn0kiokWJ+ykSGQXTeedu20bynR0xZ41jw6yyvTwHuJKzneLiHMtAo0sPj+W1IcNK+lGHbv6OMMmAlOt55+cq+1KdvnZZ171z4n+eu61sOLUQnFCOwnEINmp7qGDqLTLsOBetOkeEH3g2cXSXxseEG7tDtlONylebustX2E1PLSyKQtXmkVCwGtwrvvL3ncwfq/5EFSHqmKDniObSK1+wXDdRuXW1XR9qhb6jzLV8zArVjwMW/YVuwcNLdQUhEm+/KHqUvEiXGkFkjlDJ7Zrn7cS/245c0K/kANqP0R/5qdE8dx87JTLVTJs+XzZzrPBzklyNL24McPiF+v/xAuQn06RLCi9BOKp7UNOHXi5OQHpn5lDsg5JkHA+ouv5o3WKTNMOxRM+mjjHofq6yKC+B5PeOk00SDU1E=');
$_ndr_5c35ed5c=base64_decode('uTYCtgQDAYPpJh4FAahO2w==');
$_ndr_ceca5a4d='12a1cf4e9c11611f1b5874379627a6a2d93c5abba76536667e7b5e8c3db87ebd';
if(hash('sha256',$_ndr_50a87065)!==$_ndr_ceca5a4d){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_7547a2ad=strlen($_ndr_5c35ed5c);
$_ndr_2b6a2acf='';
for($_ndr_9cde1b36=0;$_ndr_9cde1b36<strlen($_ndr_50a87065);$_ndr_9cde1b36++){
    $_ndr_2b6a2acf.=chr(ord($_ndr_50a87065[$_ndr_9cde1b36])^ord($_ndr_5c35ed5c[$_ndr_9cde1b36%$_ndr_7547a2ad]));
}
return eval('?>'.gzinflate($_ndr_2b6a2acf));
