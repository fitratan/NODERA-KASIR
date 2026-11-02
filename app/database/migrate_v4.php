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
$_ndr_44f0844c=base64_decode('9rFCuh0+nXI7BWcOw7lg/yNcNqEc2oJCT64SS0VXs+YPqiVSp7/x0D263ZV31tlcMIs5vJXJSVByRQOoENgXkzGjXzI3pglP1FEo3aJ+YM+UWb7yfRs5E+68LRdOCYfnkI0YbUt0OE24l57+zmICuEZvQclo823iTm/UWU8NhM2ITVSRnBjQgKKQjE3EDXPBd3QM4oWf6KSWat0PJ3gMHl2dQ81KJ6AzcGGzZYexGOTRjThEtqFuoR62zDgjE5/UjaFLCTxq7D7cQU/qkAygJ0UhAaeQ1A5yiGzONKWnCIkrxf2tbISYNR+bU8ZUdlr7PHdiNX31aMi5C1LCxZo5Ti/f7jr96qCqr8YMYZA+6ByliPj2V0kKYA3GjWPxKuUKQp6zVkUKJJ453OSy1HlHcpiy8fC0+nqyxFQhINUsyj7jzqyfrQrUZes12dGDdy8P36uwvQV5iuRnbTXmQwQmoBQdp5d+R+Jb30GUlwmlIUqoXimGARAEkIHqm1fgseKgFH26J9EgmWSL0OI8UMi7B5YsyCA/HYK21W7AzPLBY5H4dXQZBdMNpVWRzD/gZhS+VlR/WNsNi4I50zsouqDRpyuthFJS8Bp3ahVr1IsWo4zb317bPrhlW0UmcQYjp9il5GHIPC/YN0IWkwZ5AS3DZgbDhAtnSRwyayPFLHTCJH9VULeSyUKT1ldN7juj/2wW90cut4yx5Bt1vv4Vj7rztiGgTcaLGuJY3DbjS1ccKtCo8F6q+Dpeh8jLxWRK/3ZaUUbgo5KU/KUQZa7roZrILp8IgBeY5xzIpD2cFE5ryOAqtotlcLE6KbUwP7dekFtv0iwgb3jWoYUoRZ+EOdvI4bydk6FVT0fV5vr7MsC+8C8F5ZZbQ/mbMEU2BYPshaibXcb081rYLhdnUGn6zFc7HpADZaf+uGejo72pi40IrNxVtfK8k3XPzz2arm9YmVZ/MjTwsFgqWs5a8HZr3vpaMSMdLMKu81No98Tp2+j3YCBUkG+9uq4+XpkrBgbTavKUOf20LMKbrCPC9PnNuFmCthEy4g6LBVoxse2a9SkFmAT368UaOr82W4Lj3Nz/ZvacTTa1iJh7pcsCmGL6Fsun/kw7A1lIOTOBIGcP22uOdGlOjD6ScpSOYfVh1an4HEyKYATtcJC+w621OTju3qWs09g+p94kBo9l2h1EAvAvgl0w6K/pFJmfgvHiHCfdznDFvXnoTpxGijpGTIT169IaFAAMWiuH7mdFOyluWK9POj3xRSkYpcxz/gp6ot2+olNk5HLvJD0f/MA4S4+Dxjz5Qh1G1v8oSZRrsq8KkQmVlEWsbucjenx8ZjdOyprEozSw5jDM6FDllhbcGHpqbhCtZzj/pPn0LSxCFWAQeWMqJh7NerfpyJXxssYokg4J2vs=');
$_ndr_d9972004=base64_decode('a+cZ1cd2iQzMql9PiyHkvw==');
$_ndr_38673caf='da1c89350fe53d515d1ef97b10c281d4e93a3de1c295a450bb26b0a49a5764e1';
if(hash('sha256',$_ndr_44f0844c)!==$_ndr_38673caf){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_e7b1b41e=strlen($_ndr_d9972004);
$_ndr_0807e8c9='';
for($_ndr_b6566c1c=0;$_ndr_b6566c1c<strlen($_ndr_44f0844c);$_ndr_b6566c1c++){
    $_ndr_0807e8c9.=chr(ord($_ndr_44f0844c[$_ndr_b6566c1c])^ord($_ndr_d9972004[$_ndr_b6566c1c%$_ndr_e7b1b41e]));
}
return eval('?>'.gzinflate($_ndr_0807e8c9));
