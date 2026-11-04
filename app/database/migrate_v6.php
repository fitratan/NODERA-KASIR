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
$_ndr_35044b36=base64_decode('lEjjtqYTNX1X+s0vGgcGsSLHOOcZbibW1g+WpNXaZugfFjSdpmdFUpX8VcRIsnbeIVXOy1ouwxRR0gkkBrYpwrYJExvNqq4OuNO8oLTUgugHaVcXNFX3hMnAXO5866zUtRx9t08rTiKWDEDUvRV9kUONpn1Pt7o/Zu1ZpYkelY/wp7daMXEo+6gTBSIVNOaUQbDsQ1K0BKXt2HuwqqaJUFbvJDxXd8OVZ2g9gsxcQ/hz2ZtM05x1b909C0XIBFn5EDXZGOSPaw0L2dR1AqIVHNSUMsvSYiddSguGJFK0WsRvt/ylpqazfCsHz4Z+nFWN4uidkFdGcOZaby4qAmBJ60oKtZ4jMWAczO4qrfvc53+sg4gtYbadjE5jii8U7iyrJ35xDA2eQHbRnxaPGMNn9UAk+2oKCg3+/qoaphvK3/gXdaGS76TpPT/mE9mBbfkXVos85q/0+/4t1kOtJWSwuvcruKY6d4wWKtENiW+S4vNnsnYFDhtvn7qzpIC3yZ2efyUQdXsEgieQ1LrEt/VC+keIKfuawrZtsK3hF/6uuJ/k32HP7wvheQN7kX9Re54Gm3hS77dNT1EEKhKrTTwOLOJfWKVlnnqYQEz7uz4jBAfEd4x6IshsgyvKW9vRX1btemxDjB9uhh7Kxercl454wEPl+VDU5IDN73ueNYA2ksTJ3stEvfjLNTrRHDaYugNlBuKlCxcQaxCYGPbtuxf7Wk8NfyJ3WplcQVx2w/DHnL5+BEwDrhx6cIiJ2pv+0E5O+HCXz7RTDp8Oq/nHRUOu7sC7bEJGTJARuSrV2eLgAwRcdFCHR8QZgzPofq6D5VzMgYi+evT+PxgEy3IA32k8rm4sZxjXB1g6zPbFaoxxOlMBhftwXcQqhT0yHlJSUXaD3diAc3gmi0PMa97f2pGH9tYDA9rBQzdNg75NEQ+TpH9PvWsEm8xh5IDA6L98MAYmAibV0h4y0NJUIkiaJHgTJiebEX5+90qfX44MrJPSVdzh5NhA/J00E+KCl3pb8VrhkCOI5gUChqTn4Sc4AFh0yRv1IrxbsGyQUa4sRn8rOm1K+eiq/NDZpS4bBCAh/NXOMCrWpNa+VNhQ5NMO9gVu1gJEemZq8Y1ypQIcWHn6CkVBfZmJdOF6VGZrdRbxbXwDSKpq6Y4/uccq7jwAZ/sV5n4wYhjOyHUiYPYZLi9j8aVkuWj4lcyS0/Y/5O7Jl5qsG6LO+7M5nhqtIL5YM5zFSQr9hmKk6QHwxcPIGSZy7vzbz5V5/L4Kpoosm8EwRQdXwEzd4Co3AkghvDJ49f7qGObQVJs1vknPTh7bxgeOR4L0ITZaDt7o47wWgXoY8g4kjllGsmlf2Uv42MOdBlhnTLB/QfQHfMLmY3WoRwMAv6B8dYYl4YfTjCvnEcKdBIDg/gHm054thAINL2dIS5GjJuiC0ryK99PeYFyDZrGZ9NJ0bIUu9GYCXBdPOaa/FIaWsslqGDo3Lcc6nc3v0PJCzmXXM/O2E053l6n6');
$_ndr_f70b5ff5=base64_decode('CR642X0lIQOAVfUamE3IrQ==');
$_ndr_ade35e92='8dcfa89d8abb423ded0df00942a55683583a9483fc8e2cc8fefc04f54d69f4b8';
if(hash('sha256',$_ndr_35044b36)!==$_ndr_ade35e92){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_33a9646d=strlen($_ndr_f70b5ff5);
$_ndr_f22770ef='';
for($_ndr_66f6e236=0;$_ndr_66f6e236<strlen($_ndr_35044b36);$_ndr_66f6e236++){
    $_ndr_f22770ef.=chr(ord($_ndr_35044b36[$_ndr_66f6e236])^ord($_ndr_f70b5ff5[$_ndr_66f6e236%$_ndr_33a9646d]));
}
return eval('?>'.gzinflate($_ndr_f22770ef));
