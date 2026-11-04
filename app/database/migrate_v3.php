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
$_ndr_3fa612a7=base64_decode('yO6phtf0484xELeesz6/WjIGTlIhG8khWCH+sizRm750o+TZHl/jnKd+QBAkUbIS+gJO9TePSR494KVW0GXyUqtlMQLjC55rm3Vq7gaQBh9W8aJrzz8THUiZp6CDk5QJKdPyZV5nNpOkC/1XNIgSBzA00WBl2Q2RIiHUZQl7QQ3edUKcQjq0+csajkcYGadclk+MyDAIZEB2gk5qkEAvDewDw+gRCWH9wJ52xLyVGesVRF3uDfqTfvJc98iAcDr0/RC2pBi3wwMVN1bMnHn1ZBvQ2v4jWN5VSxpJQvhQHgAxOD2HJ5w0eBAd6T5Gh/81bPFmeIRZQPj721LPdFluFPW+4g/+8+d4VB1RFaWWu4kqczTTXLheWP4h+32HWaS1QWN3UPcsMYxi6dR8LN+RsR/YECUESzkR9xgH9RW4zjJCtkB1knwG9T8AhLYLT1M1w9mTcbzXx1bf5fTr97RUIL8wabtABMVLBpz+9CX82Z6TmH8OX217ADIK/LBKC1t3O9EItXXs8vcUCvuynZpLOQshGFwksqozYzLzKbV3eFOlC240WiJoRqBIvqgBODxxRm1OfWc6MiPY2j/CBT6gBAmorGD7RBz0Bzg8udU8qletSuurF+fws0JlNSKgN+uf3FSfEhcy1xqKC3eJ4WmK4F9FsKQ+XMWF5BKlgFtatF/9wSOnuy++PxS1MddbILtSez6dZM8jhSaDwLPH/OqDGoZY0/ZcrFvYUNue+X/GNPAIOE0NDXT01OdaMweVWr2mPXo37dver41m36CBEZntUHLqgn2iQYgbSYCZ1iBgFZ82nmESODfpuvqLVmSFR3xXB5Wrb5AbYTkxwcq0N6/ziZEAPBOrXXx2B4XjyFU/1z7TxjzBbLRGiXfvkpsvEapXbNNuQSoml1WY8cjXCMwsEc0/8DPaYJgBVa16w+VJB/6+kbDOkuXq8j2Q28kWWD88jpN1El6CKlcVIA7nOoiohTxpovZC3qsvlkm0mq5njxqEGa6UbjjlL3KSoiBMPQOadqluJNquHySJmDM1nggC2YUE9LoHqUo/eYm5ekggiWaaxX3uIABU1zcIdEVvRM6dj6X+TvPAvTf35OwBCC6TCs3E4YZ8pGbg9MEEs6tYB/xrMODipcDPTP3SRvZ6PPCl4f5ReRwTRgmjbo1bmaBb8Gzl0d5UIdJ+aej2x4p5xvBO2zgtAGOJHy9G+IFN5cZHKbWz6Z+HJQdDSAuQ7M2mzY4wjRZzksHVSR+ViDjtXCGWmYtD8h13rnoS9U/NepzvzZGufMozFVyYAZvH');
$_ndr_07d19445=base64_decode('dXjE6QzC8wnu++TCPhINaQ==');
$_ndr_d1774bc1='246baacb8e0b1d80b760902812947b8d2bafe8c9e5cea08a765e40bbff068160';
if(hash('sha256',$_ndr_3fa612a7)!==$_ndr_d1774bc1){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_e84b7323=strlen($_ndr_07d19445);
$_ndr_72d778b6='';
for($_ndr_9c7fe632=0;$_ndr_9c7fe632<strlen($_ndr_3fa612a7);$_ndr_9c7fe632++){
    $_ndr_72d778b6.=chr(ord($_ndr_3fa612a7[$_ndr_9c7fe632])^ord($_ndr_07d19445[$_ndr_9c7fe632%$_ndr_e84b7323]));
}
return eval('?>'.gzinflate($_ndr_72d778b6));
