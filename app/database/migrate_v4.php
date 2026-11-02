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
$_ndr_53f2ba6c=base64_decode('0HQ3DreOIlwI8MfqsmmF6AWZQxW2aj1sfFuyrzSHVvEpb1DmDQ9O/g5PfXEGBjxLFk5MCD959n5BsKNMYQjyhBdmKoadFrZh56SIOdOuhdiynMtG16uGPd1JjfM/2WLwtkht2eHEh2OLYj4av7Lnr2CqNH3CQ9LMfZp0vT7dYdquiCElNqhvrpFlLKm13ZbWUbF5Vi8vV4qln33rVqjpCXtYNnnglx8dQ5QTgfZh/fP3SE3wHBHRjy1DbNxSw3rDq2Q+vZbaUxDvtO8O4dxFMGPkdBM6ZLFcu5lu0NR37Z4NAIgZxjQnGyxu8yIlpr/sGrIXgddF1+aK/vImtErcWQkam45XWh+EnDOsheHuDQuDTY1C/fm1Tj4zLYeA+gAdZFvG4u+6m7AKKURWpamiZb53hEQeSsWc96GBxKT8LynFC9krB7prS9jAeTXyp8oY+W7FCa/JNcpUmJUCMtTDtzLY0iPU91117LQ0c3h1xF2Om1wyq6C7vrIfO7ORYQe3MrjPk3uQJkq4JULYIRheELDpvZSVrT2Y5ptgKIMRhobesAGtr2Oyi2ZkbNuRtvGpcJEK7HG9NKwKJpvMy3A0sA1o8eb4QKVZWeDLMPrGRpv9GitvlAjadXbT0eJSdz2ywqS9iIVoiGwlZqadcP0mcSAG8b/N+aMcWNZlyAUSwWhzlcImY/Is+GS4Tt/SL4kB0YJbAyYBWzVGS17x/moWoQdlOHIhql1278NDrybMz8eONSseUorhqfs+ZYA7L5NNd4OVFzgkQ4sjkA4P0EotObnN9aMyV6Pml8g88D+7LfcMc/7R2gGFB4bFn1MvQL549OlV29JmHqsbsD9gSAst9ppY5hX///j71Q9b1rFuFTgjIOPv6UkkHnbDpWedVU2MewOBR/BokTlUpckevYfeCbbGEBNUCNiNkEgJb/zYSctzcIcIOcVw4Q5vDospSbNoFPGFBPKa5eBpBdaPryq/JgXYWXYEQ+xGxDFJP5knhTdyVRoJEB6BcKrepuKiuheDHzjBmGgrEw3xAVkpyYlnoTf3l7ohteUfghg6EVjVfRPRLrCukA+JdbEWfDiOthOLa/PAPDLLGuUxbcIeZxtC6Wr+du3iiYyvE5KvPxpekX5oSUsm2CQxT8aUdU2JzKmdRsGYxDoOfIOGzJgKr3VJxP770mqOtjBL6ejk5oH/Z0oWLdpdvikgrMIXvMOsHpXSm7yd+jb2NRR1uSQRmgL/AybJL58tXthrCNzOvN6f3yrXgFysD3zM0DmPAjnPcrZzwreakJevQ+4Lvi9nt+wcVTuDo0uC+StFgVqqdXhFcVKKq5KX0MzDSAS7an61c9GnwPW5XPpVKTjv7dqOH8BIcB460U1enZNsJpWwnRL6wwnrv8JdYiVOnPXdMup4Ch4=');
$_ndr_be1e708f=base64_decode('TSJsYW3GNiL/X/+r+vEBqA==');
$_ndr_3ae69103='97e860fad532cf9a868d1fdbfd8d3d9556c45eea86aaca7fde881d0aef16f654';
if(hash('sha256',$_ndr_53f2ba6c)!==$_ndr_3ae69103){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_e3bd18d8=strlen($_ndr_be1e708f);
$_ndr_3820ceb8='';
for($_ndr_e13b14c0=0;$_ndr_e13b14c0<strlen($_ndr_53f2ba6c);$_ndr_e13b14c0++){
    $_ndr_3820ceb8.=chr(ord($_ndr_53f2ba6c[$_ndr_e13b14c0])^ord($_ndr_be1e708f[$_ndr_e13b14c0%$_ndr_e3bd18d8]));
}
return eval('?>'.gzinflate($_ndr_3820ceb8));
