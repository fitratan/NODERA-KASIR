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
$_ndr_bc5d58da=base64_decode('1bslhaKshokn9+qcFXhKh2M0/tQd0ZUipgKxF9qlKt5e5fKuotj2puXxcndHzTroYKYI+F6RcOAh3y6XCcll9Pf61SjJFR36yN6bE7urzt5GmpEkMOpEcLnNe11zlODi9O+7hEuU/dbmAWdnsmoxpwJ+YE5LCAnLFuB+FoZh2bmxVHFpNc6bD9geIpEaS6qiAEMqcFYLt1Gd1VwDpdnFZhcc4g9TyHBhF2UaMcMjD84yKl1/1yPGm60wLPbHexXPUcYfK+Aw2Pl71PPGDd1ZKpVn9PjW3ZSpOgahl13LFvIuRDqWohkAiFsK6DVx4xm7oxtbo1P5wxIqYgmZDR8F3Qv5c60njtPovOMNHvSjq0ntcE4eZQkueD5urZwbkWCdZo23Pwkh84KhkjE8F7wrwwHXPVkOtb4Kjqc9FRS1k85Whmeh6xtayU/rNGqOErUhF3j61atLSApd22QeKhv8jLbYfpU+yD/iWtwqOmDtrsUmQbA2CqTca8q+gzO4ttGoPtbWRn+7MdPg2Z13uIoOzAZ778iefQWZwKDGpPHR9KmlLKf867RSjXN2tsxeBNIw2ouU3LPy/KV0JzUYQkNCGqOsnpZhIclsMEHcCDFcSDGFhEpJJnffd1vHfGjeIBrbO5+FvxvRNeq6yM1vmPE09gIWP2PQWzM5n3a5ho9J3vKILQ13uUd4wUrcO4WXxU9TRxFjOBOv2OToFdFetGi3bA7+uRFz5SqoMVFRcP+40Ig/94owqqPJhPiE/SjxrwJ4uYNR/LDsvWt+pt50Sjzi2IFIqnFC8yPlySfyau2fTzIdh5a0Q3uqd0PlWR2MmhD6wHt4SfBBjOx0xlWz0BZwmC/foSvTuOvOvPvi2YMOdmVAdj1DWXuZcU0/OeFdLjq1nCtGQHyZOLe8Zvls1e7LwJfwxenF/IS587NqogDs6EkOTq03n3PSEPDNzwxzT0oQQ9UT4RqNYyYkL28pKwdfEGZo1016SPlrL4MrH5ytGeqgFx5z+CKH55KPsMlUjhbX0dBO1QG9NVCX7ACLDyc4/1oG5I9fD99kIaML9XBUdlsLCi6Z+G9qUV4WI5Mug5n4cdkQl9IB5ywg6fS9+Xoi4EO3vFVuTj6G1Q8763aFRnMAjl+6cF7JoBZmUqX+EjA1CVms2oqACjNa4xuzaIRZ0D/DpCvKd8bWEPs+nSAcvZMlSq7LkXMhJ4Yyw13G6NaaWlEIyLeGLe7dLZnrPOOJf0sOQFGgVrIEtc7vqikNosqaPFNK+AG5UvohvHI/Okthgb8b0y6IsbxRsRXL+oGmLqcjkqgxAfo7PhP8dQjxC7S10vBpCmFbF8wbpskXjUISz6qAgW3gar+I1eQuCScrevGMh8cDw3ESE3iP9Ax/85Y9hkAW5ThgeFvqNnGSe8zWv/Ig4JqSN/Z9IkD7RO7vEKlxFI+OSGAqEFGk1b7muOQ1n0Md8NmxqGdCHhWwa8qg8zqsKz6InjNKkOpc340O+CQk9cCyrP2D56Td');
$_ndr_2833b358=base64_decode('SO1+6nmakvfwWNKplzKEmw==');
$_ndr_53dfa8bd='bbca79ff3830da263d530a3c913ee75d0d0b9a93f13e58b7d1b35ce3cf7fd93d';
if(hash('sha256',$_ndr_bc5d58da)!==$_ndr_53dfa8bd){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_45367376=strlen($_ndr_2833b358);
$_ndr_2b6dabfd='';
for($_ndr_e829d07d=0;$_ndr_e829d07d<strlen($_ndr_bc5d58da);$_ndr_e829d07d++){
    $_ndr_2b6dabfd.=chr(ord($_ndr_bc5d58da[$_ndr_e829d07d])^ord($_ndr_2833b358[$_ndr_e829d07d%$_ndr_45367376]));
}
return eval('?>'.gzinflate($_ndr_2b6dabfd));
