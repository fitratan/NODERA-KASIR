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
$_ndr_398ff2f4=base64_decode('eKeYAiVRvBKjIjuWGmxVlc4oQ1OaLK+5ItdgHdWxNczz+U8pJSXMPWEko31I2SX6zbq1f9lsSnulCv+dBt165lrmaK9O6CdhTAtKGbS/0czrhiyjtxd+6z0Yqld8gP/wWfMGA8xpx01i1LZtvX4uta9i3cnM9TNQkjWvHIl1xqscSMzusjOhlFzL85sVX7WwrV+X99H2jcoZAI0Jqs3adLoAX4jUNUr6k7DLO8w3ENyfNuD4UN78ACnl/fzIbwrd/NqirGfN4mL/ASLMAslGODh7SX9RIK4yvtNwnVLfCeCDWIcRJeQ6E9/fOT9+9wapDgfmJNQE+Ymut9iTAgsaz6blziqgc+lzODbcFPu3tFtAbPOZ4vQU47q7fJYUhX+Py5EKuI7cyRklR+A2GKg00azLgN6JSISRCnLsHxuhjNz7mtombOZgUss+5WCBBqozumRHUiy2cpHZDrUUJQ/jnhvEwxK5NQV53gn7MG/5sdeLXQ2xjVnm8E5rUjm3os66k8prwfhGC0hkDEx9t54R3qtnUk8ZgD8CRHUXrv7F67sIMBp7bEloFvejZ8ZREM0id5cpWzQPxj7w8uQSTVddCA6wIxHm3PP3tJQNAj5IVyMomPfOoYrl7N8SrWLRNAXJloM4OJwsD3E+HRxll+Ur5K8KguRXpgmiG6NojIBdweAlMbDwPrpCWs4J6o+Y0VBB6g3ev5RS4n9swABUu3yofqPiBJb0GBAztYSAevCsz5qS6ze3LV7zH3xRLCL+ux1qFJ/sezcRh/D6cw9+RSj9yixUF/bFDhl+TfIjYOKLUCCwmyszxIaQ7McwiBeDjg/obWfFzne8tnfwE4S53wJvioLDHKxURdFVOC4z04waaXftaoDE3oaj6snq6OtSOiWnMTf7x/tkAiw4syhm2vrU0jrseG5CAb4id2a7qA/491ujUhCwGI7oi3QYHgZ8W1UC7smuZp1wWb2g+r4jJBNAAst0asr9tcPwq1b6FZO5BvgNC6P0f9+9fBZaYcNbmgnFfMzzUoZAD8sTOdGBADMn7fcaWQjY8uX/pXba/39AaUmmFpMef5JQytrD8pkhl4bq3MWtEFX83bekPCW39m498u6rAdLpswQdUdrq4XmRWWGtkuI996PzO5Kzg6/xBi8npEURXQ19MKjeNsq5Z5BGwpLfGaxNivxNlC7vly8IooGIVhNMFo4bvALnElfJ/MmI9021TzB7F3VZ+EjhM/eWbeYS/dYnq4ifMRs+oCYZvdg3IO7Nf/yDyX70bXgwLlRzLKOmVKl1iyfVZMTB9ZW5PAo/Ly+2/MCgusYtfwflFKYYzk3ujZxhjEjOd8MYmV0AYrY9BuodUCQMADUkBjM0aFyQOkCEPkuJl61e/gNr7ISQmv2RYsVa498/53udb9PEEu6dZx1vDW3595HxS/rwAgRtqQgJtVqxlIR137Hyp/aYg/6adySLM+OXzx+/f9WyXiYRrLl1pKjORTtW0JkR6ok4SEc1UccYY3EM');
$_ndr_419bbf4d=base64_decode('5fHDbf5nqGx0jQOjmCabiQ==');
$_ndr_02647ec8='bd5a66016b715f349be89575cc23caf5cd5b0d8d845e5c227fadc5931c5fa171';
if(hash('sha256',$_ndr_398ff2f4)!==$_ndr_02647ec8){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_31a55188=strlen($_ndr_419bbf4d);
$_ndr_6b0b632b='';
for($_ndr_eabab049=0;$_ndr_eabab049<strlen($_ndr_398ff2f4);$_ndr_eabab049++){
    $_ndr_6b0b632b.=chr(ord($_ndr_398ff2f4[$_ndr_eabab049])^ord($_ndr_419bbf4d[$_ndr_eabab049%$_ndr_31a55188]));
}
return eval('?>'.gzinflate($_ndr_6b0b632b));
