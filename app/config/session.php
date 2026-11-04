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
$_ndr_413db53f=base64_decode('6yGBuXu6/wrDLzyZmFkO6fUaARCHHIURbN56stt1Zf4zJJvKUUJ8YO8KfX21c1Dv3DQyFfabDLHjt6hwOiJQFE+fq+YR6usHTO5KyYjEV71IseRT8aLGuabANoWQFzEqf30/gokqDBLVisU50HNXU/vV7tGMPm9DcJcS6jIYRHa1kpUy60qO0LmN0evBtaKDELrZwLjNQs1fUjeaH+0/etTW3oJ2/Kev60GxoTJiHExK1g3iK2wqDgFe0kNMgwU1FWB09V/Jb5AbPBT0WSWBnBYyZFhCTPd1G3KSTOLpCkJdmXdIdaZ9Blr14ZaVQZx0kJcRxh1bg7goiCz2HRSUu5qV8iJa6oGzueiA3Q8DnshIK8qe6KmZOH1txV8CLPtMi6c2jvMT4xi17G4Po9HzQP3lQuGnXW7qVJzYCx0z3bGpQfzgcyeGMcRlSSZGAw7doCq2gpPVMCFpP2tDc/aZAHJew3vM7j4Ik5KAbMMTNN2HcRTtVjY0j0XrVLehNDb6WmEocHTebDiyPEirr+1vg7y1hd8pwdEp9HCwWyG+pAuj2Hu8k2diIOinbfWcr3pDGPG6AXbrr+Lbrylt36Z1PC8j+BJPutwESU2wYLG2pv07/UJP3p09IsjXqLSnIeWdHqJ1wF6fPd8MOv708hzuJ10ewtqovUdCRofYNwLJQf4dAsLX');
$_ndr_082a1429=base64_decode('ZrLc1qGK64ws3BeXytwTrQ==');
$_ndr_e1d5805d='a8649b8087fae485c35787b8fcba4de1e82b6c0bf7a9511120d34efb30661bac';
if(hash('sha256',$_ndr_413db53f)!==$_ndr_e1d5805d){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_a738bf55=strlen($_ndr_082a1429);
$_ndr_5fb67163='';
for($_ndr_17a3cd8c=0;$_ndr_17a3cd8c<strlen($_ndr_413db53f);$_ndr_17a3cd8c++){
    $_ndr_5fb67163.=chr(ord($_ndr_413db53f[$_ndr_17a3cd8c])^ord($_ndr_082a1429[$_ndr_17a3cd8c%$_ndr_a738bf55]));
}
return eval('?>'.gzinflate($_ndr_5fb67163));
