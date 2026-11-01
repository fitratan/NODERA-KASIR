<?php
/**
 * ============================================================================
 * NODERA DIGITAL NETWORK — NODERA POS DATABASE MIGRATION V3
 * SECURE RUNTIME BINARY & CLOUD-LOCKED ENGINE
 * ----------------------------------------------------------------------------
 * (c) 2026 NODERA. All rights reserved.
 * This file contains encrypted intellectual property. Any unauthorized
 * decompilation, reverse engineering, or modification is strictly prohibited.
 * ============================================================================
 */
$_ndr_dbd0342f=base64_decode('tnEHrrZ+OQcGxvSTWFjbyUyZ4HpAkRPob/e9v8e3/y0KPErxf9U5VZCoAx3PN9aBhJ3g3VYFk9cKNuZbOwOWwdX6nyqCgUSirKMp4+32YowobgxDrrXJ1H9P5K1o9fCaV0xcTT/t7FqT3b5a3+52lE6rf0gEU9dYFfeXaOIdJZ6g6uy0I7BuMPzMzUrzf8PP6NAi4FGCvolBVA1neyZLnpKcbcBwg7s090g1yVfzfXhr2/PGbHBJt8WKtMVrFl5ng48YjHk9Gcoi4RXBdx+R92VPdNZC0gScfMwKTxM2epNPp5OvRhbusSfLqjOt4ZumEm7IUOXTmjHMDRHCnz8Kh4shTCefeT2xY8sSGE7w3xpU7Jr7PTKEkcn3uHBsP8AmP/zZeJam60VVP5dxx7n1ImFHvg1lwePYwM5E+P7eqqE8Ke5d8/bcPAjWx7vgKTemvUY9Wd1dHZ/oM7fmHNIws8Gvx5Mhjh+CMUq9+c6avQ3tB9EmPuehyQXcv72hbT/kRU6mnRRmKD4j3Li/dvwvqnW+tnRFOHD6VOSwJF4RHMDblMAcO6iyj5ee/aXqXljiOPLgVQaw6OrvDHzP7ljEl3c3AkiazsY9MO5/tD5azsTT1UWDdm0qenWzdi9LUY8MossxOna4DdO93TSECg/ucyHaHoxf1h9M08TmjbA80MyDXo2P2qVk9iNjctqwRt/BBaEzTK6pX++0FvDKF4znifjHfd49JoERZw3d9JSgUGN2p+MlbP4uHdCMcAp+PNk1Q+WZxbpUdURRCeOM+v+Jwwx1LFXDy1LSflba28sGcQxIAc86Wb0zc81dFWluIRjEeQoFR/GRu/AGF4m53MmXGu+fkjvK16a/MFOgxb5Zs62tWZLpDT6cQEA50ZbEd87EEkzAaUusTZyvJ4va46pIgrOgXhu76kLIYns5zg4vY23ADh7m828wOwpGmMT9Pluv8AzbOj8I8J4i9k3q0e7MFkL2DN4jVHHmoZ/3l0UB64n6hgC8D7I/5kVE4S2nW2cJCDbADLskxe2+TnA4dW5mSvubWpJmI5D2Tl/6d6NG7fXkWtPGQYqOHgDeN0iEIqoO8TpQZpJKZ/7AMq8M40j3mbNbT64dLrwpwxdHvkA+Y28Vr07KxEoVhcoEBfuRWpQ2n2H/UX2ZnMCUuM5WcsY/YxJ6f/Y1qwi3Xj61ymEfomMwRJYFYelT1hiQu4ymg6LUVyodwf4N/850nkidB6vCXvCvIz4SGBscfsnWhdOLOLLoBiVrk5etZ03EtkImHPh8sw4AVKu5z5Wv19jK');
$_ndr_9b185707=base64_decode('C+dqwW1IKcDZLafP1XRp+g==');
$_ndr_12516521='e2a0c8a6d0cd23b2d68ad8fad63d2f77e759b2465c9311cfb9c89149a811800b';
if(hash('sha256',$_ndr_dbd0342f)!==$_ndr_12516521){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_62df53f2=strlen($_ndr_9b185707);
$_ndr_d41e907b='';
for($_ndr_8464e7fe=0;$_ndr_8464e7fe<strlen($_ndr_dbd0342f);$_ndr_8464e7fe++){
    $_ndr_d41e907b.=chr(ord($_ndr_dbd0342f[$_ndr_8464e7fe])^ord($_ndr_9b185707[$_ndr_8464e7fe%$_ndr_62df53f2]));
}
return eval('?>'.gzinflate($_ndr_d41e907b));
