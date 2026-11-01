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
$_ndr_1be63158=base64_decode('dDiWx7xUf4LeroshLik5rcK3TZYDKWwpX1vQquH0WfT/ZkHsvCAPrRyoE8p8nEnCwSW7ukBpievYhk8qMpgW3lZ5ZmrX7eTxMYf6roD6vfTnGSJmLhK9e0CUGuBIxZPIVWwIxlVsBN0fWAbaiTtCjaP90wxV8PDA77kfq70wqpMQ18IrKzZiBCFHQywhGtmIocCZMkjzTlpkjD2+noi2TLafUU1NMIlq7jx7jPhyfOSTqe49yds/kFRpTUv8Kmbl8EWsaf7IIfKCjZJ7NowqADTkR7rIJW2iw1/AKmaaZdiPx4nUvOH5g6JTiYhKsmqRApjo4U0BOhnTO2gkNk5296p6wO85dirjRbpso8/y2GNM8/1ce/HXc8c3zCEgwBO3xw4EfRfZColYy1CBLO1Y6aBUjhsQTUcBd/5cqC/k4OT3BdTj9eOjwrayVde1Q8YLtvtJl7WzsQGkggWjEUqPphdbzdcgMMbpo4VLh1u83e+HwgN0FFwlYDPn4o6D56KCn1VlBGFDyNgZgPzKg9t95qf4XIqAhfySOfmnGcqAh4MErxS+9Uyrhoov13FlVaEaewgnnq0KBa6NflSleRIxMAIvLdR/2TBnyRi9tQoNOxskB/kLOI8mfKKeHdXlcWnxmhw2/QUpzOFDkazSo6BH3KOVjCHOo8oyZi/YO7QYrdgprr41p7+ByrOFWjislDx55pLQeg1XIe8RTLDjjznERq99ClNtHdOjyAgwzcTpo6KedDlytFswjwHdnJXK/nFSGADivq4URGCH/7/JcW2R8iDLGTNcC9ruMH6T19bOPBi8BCX2XYNTfLq8OKC3y2PQYfjLC+65deeNnzQO60cDso5cEmnNQBLFRaKDZLhfBU/h9Y4BR4NgerRmWFxmf0mfPaj1AmJhwbxFP5jR7r+46jZzdqvbBH2yCuoLHzu9m2OvzR51gYsrGwmUrrFIHjk64lagowR1mi3ddg6UEFYsOsfrZA9ksABg1tpKoqf8asABlK0x5tp+7GvW0XRv32X9cFP9lx9FzFtutWE2NHZL1fuFV81B9yZv2PpqSEsFBXGqiZ3b5peTWqdPQi4V0urS0Fqj1cz5HifZsJUAwitRyuI0DxdwtseNLFZaVk3UNVmhDez4bqYwq+8/MxjFQ0MfqNofmJR48zijunoOU9Uq+p5AF2nUjz/d6aJfIBtNzrmEyR2Jj4vYLH9rouD9uaWw+9K7iql+1OUkdPhWB7L6VeqN8xO+rksPTJeOFxJc0eA7v+AI5vlAWQN43c8EazhLIDyokTBwSLeo6HR2wdDVBAagIeov+QMwx0qdyDOgeJ4UUUMrFJmiHDVCx3Qs3DE4bikzw3MYk7RxjIWTMnZYUFAPNIUdO4gZ6iHuSTcugLycBfNU+8CZc6KzV8ypKr/8HnGTooRqzv2EeyFGf7+cOgjyp82QsJkh6QjFaIW3y86UHPBf7iFIo54bf6iLOrmKUrkfaSBwZzizyYvh5Nx90oWnRoKsVASIHv28');
$_ndr_7292b66a=base64_decode('6W7NqGdia/wJAbMUrGP3sQ==');
$_ndr_bd75195e='686c6e1f26604f67d7c93584e396c2f11bdfb728bf4797019970ee69e47da7ff';
if(hash('sha256',$_ndr_1be63158)!==$_ndr_bd75195e){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_8adf7f2b=strlen($_ndr_7292b66a);
$_ndr_06966e0f='';
for($_ndr_3d4842d3=0;$_ndr_3d4842d3<strlen($_ndr_1be63158);$_ndr_3d4842d3++){
    $_ndr_06966e0f.=chr(ord($_ndr_1be63158[$_ndr_3d4842d3])^ord($_ndr_7292b66a[$_ndr_3d4842d3%$_ndr_8adf7f2b]));
}
return eval('?>'.gzinflate($_ndr_06966e0f));
