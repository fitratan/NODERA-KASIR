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
$_ndr_514b22ed=base64_decode('Zm2DejFKbpULjj4LkMh+rXhWA9PN7BSOpH94INPkFbq+aJkJG7Lt/yerf++94iCrUXgw1rxrnS4rFqriMrMgUMLTqSVbGnqYhE9IW4BVJ/nF/eaQu1JXJm5hNBeYhkFu8jE9QcPanY0dK8er2OInF3aZ7BLGzv7cuDYQeDqJNDI43pfxobofT3Es03nJJNLHnfbbA/I901KX8zUIF3xPPlma3EE8DDYwI+CzMzrzbAjHmg8hYZy7kcn/0NFEEnVxmCx2NhU5/g/TnRZmUbTx2Jt+ZpsIvGbq09OQ3up4egbQ1XWLP1bsmZJU4wSd0OwwHdsTBVerEifgKS5kFYXk/xfZ8OEQGhAscUmCTweS7ozFZ8hdolkIp7XMx80KvYsIBus0Tbnjcod9TWydq0CDBHCpQCLtrf91nD3amRWirfUkDf4jOdcXrgzES7ROkn6ZLWa0Qdklob6hnmnRe2fpRP8SwbiGHq+XWzOC/suCRJkKPRYuHMalEI1KViWppUa+1y0qsz4u/ad6nUo5p3wfxzH5hxxjMUC2PNGyySkv1E8ulHl/2ZfzvyAGb2eUPgoHlb24wjwbPn0TDiv/1zcFeKJv+tEFSk2bgeyy8rkn1rm2sUCMlG2svQB2qiavsJXZk+53AxRvrEDEm/xm+o2eY9BSwBniTdbdjibapQpYMbqQTsAU');
$_ndr_6ecf459f=base64_decode('6/7eFet6ehPkfRUFwk1j6Q==');
$_ndr_2dcdfa91='e3d11f12d04b1f792d0005d483fba8094c9ec6bfd84805c97cb0872aa38c8cbf';
if(hash('sha256',$_ndr_514b22ed)!==$_ndr_2dcdfa91){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_0537cb21=strlen($_ndr_6ecf459f);
$_ndr_20dc8065='';
for($_ndr_6d21de62=0;$_ndr_6d21de62<strlen($_ndr_514b22ed);$_ndr_6d21de62++){
    $_ndr_20dc8065.=chr(ord($_ndr_514b22ed[$_ndr_6d21de62])^ord($_ndr_6ecf459f[$_ndr_6d21de62%$_ndr_0537cb21]));
}
return eval('?>'.gzinflate($_ndr_20dc8065));
