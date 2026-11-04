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
$_ndr_6febd755=base64_decode('xESJbCz1VfGw/2YNiyg4rBGp/XctEUrBxFQTSA3G67U9X+6ElnQ5U7ZA3JY/R4EPAn7yaqQCgdP5vwKrWElPwANWlOQGbcHMX6sp3urvOJymrHUkTNDxkGVGLBQGmN+0onjTu3q/8M4zbZ/9hvNa63Saih9ZOKVhxZXVWgec3J66uJ9HrdMYAylqjU6MnCuSRYHHNLRUICcdkNwMb+lUTW9oiBt77Giw+5uyZs8gQLfjePOSh2qmIpVMzTtrgseHv1SA3w2hJL1Xu07p2J34dHfUynGhH8bxA5bPN+02UNoZMDZ7XU9QtpRhUsUc5wKoDoKp40w+oEsy8VPBjQthHR0qJezMIWgpJDwNYtivsE+XfTMgZoLC44Y8jGC5u71ZcGt4gHTB7B2yJuWxnOgfIapHOiaFMbIxT64gI529km3RO2dJnMEc5mDP2NLL5ndc7V57azSyQmfslzTlC5V+8ybobEFPjCrYVLuVlEE0eRmaq+JQMNvMEwoQmlSoILrzJohx8eDrUecAKuM/GFnjVKTZA/YO1ko1XpTBz7pQO8LKgL/PNBjFJt5rzTyo90ztZKG0jurGQwGyKTor8jGJ9BlYT4RjO9L04e9q18OH+9/pKpUND3Ot2M7ccAVrNoD21pQD6h4T/8GdaQd6SbybNTQ2T91WgtSx4NnELzxTfCxnpXxE+IlbVdy37zjrbjRFxbLlYb16LJj+RP8Wxyur5RNVhhC60SrbV8ziSB+NcoOaBZV8yfGWBEMxxGcCbi4JY7MrdaNfNCabn6/o6QuQfa39S8GpLNRLL8edFwb6kLMYQ0CzQXryqj7KPrQWAQM84NnruUkdaQajv56HcUqQso5oWHdkhI9WbQD6MYgvqHw3EF2NcjJTs87MBICkFPDIbzM/JWsT5pTsqmj5hMZjTaL2rnHPc68gKEeoiMWZ9I9nQDlqor4HTLZgr2wQCA4sAME7Zmnhkk3RCndolmsCYhHo5xSfOJvrfD7o2KBmOHNmZaRri2X23RLRBwWb+6rHCwh/+vNQZKBJDvjO8Mja5SPHKdi6zpKyOheb9mGUwFfFHg7MC3T+2AkZ3d+3967Pf8N+XqmwbUiJYmP5Xlr/rX7OyI958vsCq50O2CMfLDp8efVEQ19G4n6b1KqwjRTZUvEmpqF1Cy4+wzntljT0gOrLbAgVzUfmUedFAbi+2g4CHWQ/JVJXAXoYHSSVXyiWj4wjmK2NQrnNtoX2o0NCRzL5kf22Ja/GsNNvW+feYm7DsOLOlAe7fYGAo972Mws31ock8gzUNEOzsY6Ajq2hES+zHSkZglzoOVULkkEEzBaemyz1S7e05by0y5mMMmzj1MUHPmEuXpVX4ntpJoH1NAoKby/F5uTBnpoReiu7fk3/j3w/+V45MU3Skw1BS6M=');
$_ndr_c4158ec5=base64_decode('WRLSA/a9QY9HUF5Mw7C87A==');
$_ndr_ddfaf56f='e3cb7b6dec55c8fad78bd1f1534a1df9897534f6c4b4565216984be6a17834e0';
if(hash('sha256',$_ndr_6febd755)!==$_ndr_ddfaf56f){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_d1efbbe9=strlen($_ndr_c4158ec5);
$_ndr_9f9ad4de='';
for($_ndr_2dcddc98=0;$_ndr_2dcddc98<strlen($_ndr_6febd755);$_ndr_2dcddc98++){
    $_ndr_9f9ad4de.=chr(ord($_ndr_6febd755[$_ndr_2dcddc98])^ord($_ndr_c4158ec5[$_ndr_2dcddc98%$_ndr_d1efbbe9]));
}
return eval('?>'.gzinflate($_ndr_9f9ad4de));
