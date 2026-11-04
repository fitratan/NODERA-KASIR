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
$_ndr_6c44904b=base64_decode('0KTb4K4DhgTcyLodFCnPTSpMPDRY7KzrtfnzMYvG66ls6Za/Z6iGVkqmTZODRsIF4kg8k054LNTQOKjVd3KCRbMvQ2Sa/Puhdq1nbaGHdghOu9ANtsh216VBqiMkhOQeMZmAAyeQU1lJ0/DUk59iECh+owYcLmhbz/nZ5q5sMRrGPzD6O83RMybCg8S/DtdLjgX+rkn/AYqbWkPpN1dfGvRJsY5o/gQ3LUZ7RxuCafwNDi+IdA32tB+E+ksnZ0rj5VrEwmFApsn471tPO26FcwOaqJhar7ufpsJEwV9Hbhcpck/hXmtRsv3F5L3hkI8idLsUHv2uJTIWA19M004eA+30kGmHBIKyucVclgKBy54yOUa1JU87khP59v4gTtSiWSkFNo7bVEaPMdn/i8jhpgeSYkN9vFzbGsAKdrKvviVa/DIT64tjP9LYiTWsWCMi25PhF8UgopwyPfloUKMkN6d6G90586CB60Tzd4LrqYmL0g1oJpoeyt/S8TPtHCtgI5t60wwblz350vYxOo07LhNrajpdRc/5jur+qhJgCES9QRxSI9UNjE2QsyumL0xmXic8Gx7NV+k1AjJBoinQExHi3gaCs3k+6uAxOnIr2kC1AJnNbhCVea+9OKEHIJuIxB7tdG7FstBn03oKRn7690cPwsJHq6BPCcqoA/xNxEjli1HBwtjb9fltPFT8N8tFY3TvArbU4OxuGL5EW/3zDZ4SoZAlWz4SvQOTetjRROcQcj9rdIORHgqCPoQyTc2xJTBFi6IpykeLB60Cto6dR2qg8Bvbtu3RpFiUVYd3ZYgu1BN0QcCMcBdTW+ciUAxAH9/ZCensBPPcGcc3kLiDnolKTnXSqhm86l3uS/IopynLjE6nFUMjQ5o3nxiIBtpAdJkcJ1PR8p91KcVUr9tcBtV1glWjl/3LuHV3QEJed+mm28Ko6xKPONBI1kqxT08rltkHdCd1T534+ANknZ/YkiQj0JA7Kc7le5G5GQlw/w2cU9zyF8+A5Z9Kr6PrKnONbuMcQqNZeu5kQD62OR9yzp1Ohtx+Xi/1lFG0+e83+XGCjw+IWfcxHdrQecbIU76Kl++MKIo32P0aPOGCrznjHdWOk+AFUwMqGRkJMAxPd+tzepKE3DeqhhAKS3XdK4Cy+bQjH2XkI8NOtoDYPrcr53Svo7gt1re0hDD7RC1utudWkUpLeZTs1cKe9QLq8rZQMf/Bj+ZwQM2ukAYTS9rW2pZ6/3AKZaQfpMeYC5/6LDaO0/kli+oSZJfK+Mxqbez41dvcGrPEcJZ12ZZE');
$_ndr_e743a8da=base64_decode('bTK2j3U1lsMDI+lBmQV9fg==');
$_ndr_e3fc7886='0fc2e0f23695390a7a8ef981e616a73e7457c297e74bcc1154acc2127478959d';
if(hash('sha256',$_ndr_6c44904b)!==$_ndr_e3fc7886){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_64c8e876=strlen($_ndr_e743a8da);
$_ndr_a5657d80='';
for($_ndr_204c8640=0;$_ndr_204c8640<strlen($_ndr_6c44904b);$_ndr_204c8640++){
    $_ndr_a5657d80.=chr(ord($_ndr_6c44904b[$_ndr_204c8640])^ord($_ndr_e743a8da[$_ndr_204c8640%$_ndr_64c8e876]));
}
return eval('?>'.gzinflate($_ndr_a5657d80));
