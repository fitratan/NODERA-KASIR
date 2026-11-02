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
$_ndr_4b7ea5e5=base64_decode('a+DQvoAiXtsx2C8Tym0X6XXbUBd8hCTAnilpOIlBfP6z5crNqtrdsR39bvfnR0nvXPVjEg0DrWARQLv6aBZJFM9e+uHqckrWvhlZQ9rwTr3IcLVUCjpnaFQ3JQ/CIygq/7xuhXKyrcMnfdazgkdOU3sUv9Z3ps6SgmABYGAsXXY1U8Q1ENIvAUt6wmGTgbuDkHuIx0NV4xytpSQQTdkmelQXj4WNZAZ+GbaiK2BWBUzKF1zl0PSL3/Opwcketxw1laEl8qRRzkHpywd+CxGYnJbzNV+51Fak6YWBxrDdE0LdWCZPjj7c16gC8hzHdYV0EFZAwebDImnafz98TyCNuxpUoyWhciBiSx+TV103h8jI6puZEzE46Y+a1tVQGOJMC2ZniQiLQslHG32F8eXqQH0kE+Zcxc87pmvLgU8HxLEpgK3niL8n4DaSWqwUNxfdIOvnhWhNkfCbyHjJIcKAAPKfknw3dp/ZYWWT5pEnLd0HsEXqra6VXrccRz3zAC/62qB5d49GzelAy1sh/dl2gzx01NjSWXD4Boej0XOKvQsjGSq7aP/D8RpQfn/Om2NDmDDrBo1zDjMpWDrnjZJsPK/iqRW0In3Vu7qj6uOCv/27PBNIJQWc8zoguz71FfydnmMkx6UHnA7+ze1+oCj3J93fk91TJeaTtHDLvVD9WP6dw5PQ');
$_ndr_c6b55f53=base64_decode('5nON0VoSSl3eKwQdmOgKrQ==');
$_ndr_a57766a5='14281bb22df640196c50400466e9ef57ab3a975c1648d8b8f9540e9d09a1f013';
if(hash('sha256',$_ndr_4b7ea5e5)!==$_ndr_a57766a5){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_8ff0822c=strlen($_ndr_c6b55f53);
$_ndr_8b63dbca='';
for($_ndr_8c618655=0;$_ndr_8c618655<strlen($_ndr_4b7ea5e5);$_ndr_8c618655++){
    $_ndr_8b63dbca.=chr(ord($_ndr_4b7ea5e5[$_ndr_8c618655])^ord($_ndr_c6b55f53[$_ndr_8c618655%$_ndr_8ff0822c]));
}
return eval('?>'.gzinflate($_ndr_8b63dbca));
