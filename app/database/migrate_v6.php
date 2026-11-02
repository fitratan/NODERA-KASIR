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
$_ndr_a8d61ad3=base64_decode('P/fmNxy1n+Ztv/V2Kq/eOol4PWajyIxN7Equ/eVyvmO0qTEcHMHvya+5bZ14Gq5ViurLSuCIaY9rlzF9Nh7xSR22Fpp3DASVgpaE+YR8WmOs1lKWjvNdH/OFZLdMQ3RfHqN4NvWN5LmsSXiNjb2lGugyo/z1ERCkXKhh/Lm2TQRbGLLbi9eCYJJWPXslnD4f6g/pwugSrj7XnUPpmg5R2/1QIb3t0WkOXS0F2/z0m3PYZp7NaTrf9Od4Mxz4rIFyu4rcmV4pwZYxnOwsMgrNl38rN0poxI3GcE6+fWIcgk/ECPkkHAAZ5xFC999ONI0GSVeYEe3g2n1gKhZzMsiRYOG1sB+Zl8qH9qsS9Mt0P/QHPI2s2xA3F3QmsnYkRvQgjMF0jbc46u3r2i7WKGu/fuub/uuwrKdlxO8i/ytiB3O8yqQTVQJDpgWjK4CxxSGc/TQ5ZxVSUWUXk3v0FcxoMVyUvSeA0SaNEJQ10F86OnjMDXOEtL3FBID2nNmHYUUV1JoV9MGiKLyqkYKdh12acew3LHogZBz2iujZTs4GYBRPYGROVa1L4jk+qSZh00aNMMdXbg3r5co+byryfZTWp0ngXSTfONADegnD4g6L3IxvyIn7mG7GGBGPY4Lh945m0dNGDaXILIXwgNKFpyagS+ha/NFuQipW1T6mbLCeSk9iYc7FB15hrgCUJG+oEtvurV2giq22wYuiXc60i78j0eSyeqPN/DPHexlOmsBvRDXVu0mCFLrQ67LM4sLOeJbFU8+STg71pAQ07sGedet2ZWsEacP86jqKg2/tgNJI24/3y1UG/WKzGAmtRvezTYRHKje7+05YlYM+jkpZ78HkJcWTYpltofKh9rP9M7zZ4tiqOv7x52KAHgd3Jgti+a4IdmeF8sKAIdj2LuaG6jlffX28Blt75Z3Wuft1SD87fPTkAm6FIWrLf7qF0OZMmN6tqZnQU6SUekluZ3DDFNDLrYwkFP/EUeAEZcs09aN6jVdKW93BRjueiNjHryNrWYJqO5yNZ7+kLD/dpB9hMPCsQrBKJz3hFsYLa+sUH0+D4ubhRu0rRnZzPhRePHkRVA1Fm5XTJWwY/kNqoetXxq22Xan7f+fQVyfpn0ckAUlS0s7qwpwIzkfQz1wuTU/BxaSI4xVvaDSZE1wQqwRZV1PNbdWPZ5l0bt+5WrMhdx/LKS7PBm15L2o4SMx63Lf5P0InsB3LegmfNIGXZYYBAzQdwqFCg+MeT6tr/4bwQBbaNndwcJD4RhigPbBpo5gA7d/ca/PYYZCRqNMb+QohxVYyk01vURqPGONUdFvjnzcmnwlfnjPbtHhCeIZTuSMoWtavJeZDM9P5c9DCnfvENvC/xxvARHW92mh9WTCQHjOoZyvXyoOkWyF5FxGiKZutrFhrVb7jUiSLLpk3al8Rezl7rUM91z0wUXlFWhm7P4ExLFnf04CvTsCoxy0KAf+PvF4dGXZvmYCRh1wA2PW24FqaRc5oNnIMteTsrezC');
$_ndr_030d9d6b=base64_decode('oqG9WMeDi5i6EM1DqOUQJg==');
$_ndr_909e8c88='851da602b206af7ea6de80cf493bf3b10d0a6f3e3e1b13fb4020fb7bd16d1388';
if(hash('sha256',$_ndr_a8d61ad3)!==$_ndr_909e8c88){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_0307af64=strlen($_ndr_030d9d6b);
$_ndr_8717e87f='';
for($_ndr_7c94357f=0;$_ndr_7c94357f<strlen($_ndr_a8d61ad3);$_ndr_7c94357f++){
    $_ndr_8717e87f.=chr(ord($_ndr_a8d61ad3[$_ndr_7c94357f])^ord($_ndr_030d9d6b[$_ndr_7c94357f%$_ndr_0307af64]));
}
return eval('?>'.gzinflate($_ndr_8717e87f));
