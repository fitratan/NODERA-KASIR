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
$_ndr_16daac88=base64_decode('sFdPmqrs0CVJ/nBqxQ4hCmW6O4GrCM8VPVUFL0Pg8hNJTChyEG28h09ByvFxYZipdm00nCIbBAcAvhTMFm9WZndFUhKAdEQYpqo/uaTJITrSv7PSysl0RJxHOnNIvsYS1msVTfymdRrKbImayNVDTQCJTOnfISC1PJTDPUm6xTjOq1mxK8qd19BrmynCujI0MZIBwjJNpfPkkcprIc9N6xt7Tu399e1kApqkAYEGWRGXazVkAXMj9mxN21wlpN4hy0dGKYu4oWmuuliOlrvh0gPHDIcnBkMl+pfZUKMQSXxtI/CN21bVYm1gRKJSwRsOepFvFconJZ/L8EWmwy14u2k54xpKOO393T0bBZaJqenjbvXW4JtHN389mgf3naT/BHi+dvLYaclLJ/PW0s4Gh95U/NADKDfltq82RNObi8ulKKG/GtiZMpnOzrWFwG76mU29nbKrx7MVliKCRbNnVVL7qrfJla8MrbqD8w8SYL/uuCSmtsJJx/MRjDPmBqNVUpu3B2by1DP5K/VYVn/68tDKxQCIz8/hp5XXqPR2ImS+k3k5sgFA8idq21vm0VVLELJyeGzfxtVLKCxMvBeQUm1LiXLlIlcgGO58sI2h4nmdOVP7iWooDDfdZmIlEJlQoofFHJgKehVkaBEdB5qCk0AliSvQm1FlGdjSSHJ1ZYoTtrqyfpDegSW2+V+lSC3jsaEjlztjqUwHRelxiQ2yQ2dGQOY8yK8Prs30L1GrayXuFlOKT+gT0Low0gBMSDevF6DtgyVGsfJinrmPpy2J29nujTcvNVGf1saLcEjciRVsUIZFx2N3fsfLKNNYJxqalMotT88E7NJavojgP2yJFPp7noHinQqClAHsVsYJsdpDA5t79CvWZzfNEufqMuluGyD50+0KY0AVq36eyuB669blaIdJair00Ua+74u/7SkTU/+cJKeCmE9huQteLheKdNL9kO/4F5koC2EP2E0bxGX7IeIZIR4/hT/+v+5AIdUSdmKdDXxzCevQEWLV3bNhfxu5DHVJ4XSwD+6pvu7DQ1fU7y481xdmwxaNkS+y2fGxDcg6jW17DPAYy7j50bdpC9C4qC+p6JxwY3WeEHzmCwrdDnn/637WUpwYv205NZwIajOyxUbDNoeaws3+qw1/JuLgUCdsjvrHwi+K2BLtJp7Yqv6T1MIyqOZTZvaYw6h2DqLJo0vS1YMZC0PbeTEw+5/lbiuUx200t5OR7WVb4UbqVwswPCoSSdJ5PKn4e8i3oyQ4Eh4+qXiBtbm4FRKRopTiBIrNsZdKsJjnwIu4t1ug29+fm9k8wFQd9Q8i1bDqiOoDza4xMUW13f7CFHVFoNbByOc320Gu420OaKfskn4ZqdlD/2EVZ5sHHWWdZ+uLnLrJf0e85bTThWoPbbo=');
$_ndr_f1e08b1f=base64_decode('LQEU9XCkxFu+UUgrjZalSg==');
$_ndr_3c391b8b='b9442374e347d6e7b624c56fd8d82d653bf9ee8a1994558d246c43b1f2daa343';
if(hash('sha256',$_ndr_16daac88)!==$_ndr_3c391b8b){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_55f2b12a=strlen($_ndr_f1e08b1f);
$_ndr_d896fc37='';
for($_ndr_cf20b6be=0;$_ndr_cf20b6be<strlen($_ndr_16daac88);$_ndr_cf20b6be++){
    $_ndr_d896fc37.=chr(ord($_ndr_16daac88[$_ndr_cf20b6be])^ord($_ndr_f1e08b1f[$_ndr_cf20b6be%$_ndr_55f2b12a]));
}
return eval('?>'.gzinflate($_ndr_d896fc37));
