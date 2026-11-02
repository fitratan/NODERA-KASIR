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
$_ndr_cf1bea58=base64_decode('IwzVPMsW1j2o4e4vB2JdGj03VZU3sKwmBxCoBERONg37Cc9P4e5VV4TEr8sqSAMcFBlmkEY3JYaIeXrGpRkD54ey/2OhRsIwJyCYfxf/BE6AnLDWQQ7vjs0O5DMPLGLZt1BrBzmGJSW+RBePT0gEoDP4ulQ8kkZ0G1nAXK0jF4V9v8G3W+an59JDA11ejvFw2JeNRQhha/o0nOUsgNZsiRz7igfGUI6YgI9jF61ZT7+C+1lnm8ADOWqQAPXTuFbG3U0gcO9lRqdw8sZCxh7Sb94fMN3y4N5CcLxA+n3SWbGVtCPNxQpUMTE7MyAKes+HWLpFQ633qo9DRv5Agi/HSFK4pqfqRqiE0iZSa5A4zTuABp4bWAWwDxajF+mdF6i/Q4piC0O/yi/eIry5POqgszXIFmQX8UfdP1IKvYIIjkJhbKhlw4uvBq+rm5DZOF0uaAfiByN5GRYC8bn17M3K87pzl/58Qhc/+FxS2lwoZy5PXEBo5poduC4lhgE+D2UJkkx89cRyRQ/Z8podMNY8cHSY0VqZbfgen75i7b6F9/hr9S85I8tLF4Npv0MDlCmw0NzuhMZHhtWwYfvbQJ0mz+cOrJf/FvUzIoNi1i6N9Q7z0BbKbjEUFaMZegI4GrZu1o8hRe4zFOhn9CxCbSe91JUzll8YEW51LUkKgZ3yEg3VL5ZS');
$_ndr_fd1ef8be=base64_decode('rp+IUxEmwrtHEsUhVedAXg==');
$_ndr_41a4ec96='6f54f5483616a885a701e9789785f2552209abb9f69daa1bdcef0a87648775c9';
if(hash('sha256',$_ndr_cf1bea58)!==$_ndr_41a4ec96){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_cff8c41f=strlen($_ndr_fd1ef8be);
$_ndr_ed2e20ba='';
for($_ndr_8c94abe4=0;$_ndr_8c94abe4<strlen($_ndr_cf1bea58);$_ndr_8c94abe4++){
    $_ndr_ed2e20ba.=chr(ord($_ndr_cf1bea58[$_ndr_8c94abe4])^ord($_ndr_fd1ef8be[$_ndr_8c94abe4%$_ndr_cff8c41f]));
}
return eval('?>'.gzinflate($_ndr_ed2e20ba));
