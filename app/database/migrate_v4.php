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
$_ndr_88bd5201=base64_decode('/mPQ4Rev45ehW3duC7m8ByuOpPoWS/yn1fACK41Xbx4HeLcJrS6PNafkzfW/1gWkOFmr559YN7XoGxPI2NjLazlxzWk9N3eqTg84vWp+vDeciyypd4pH9nTiPXeGCVsfmF+KNkHlRqgiyY6eBmLeQE6905JiYhMH1DHEOYcNWDWAn8bKlomuZTjOnC0MDa85f6aeuY8OlkEMNM1v73jQ5lVP0ZZAtt7W6j+jBU+xxBzZX6ofvDAQRITo3FjrE0MshXPZUjb7kttGH1+KWAx8303zk/yaRXCXEjLeVG2n1HEjF2/2ZhXm0IXFQ6acdoYDNKXwbndkFi0jVUKiDZrlticNfGH3e95PNZgcAVg+NOStWmqtXdh0hZeYnQM5KjnySkwhDU+bWnujgvTSHHmbipBgY6u+awRXXgoxQB0sFsbrHD7Ep5uqgHFrybFLd/P313ki5g/o9AH9MyWGiwT6WBzPNcx01py+RR+E98Gl/bKgjLvdC4F6dRu0izcosT5YHK8ofNux54ERjvJcmMhn/57+Wns1jPxTTzDQrDrBv2nwp+ZCD0JzQM/P3F8oZshGXobtA9Gc9WejjStIcqANXyN/FglYYWSS8Et7tEMWf3TTDcyANCkbvt94YWbrpwRd7LNaZyVJSaeMzRYZyS0fng4RFlBt2GLX8X3VTLzC+IddgiXJw9PtM80T/ltr/7Du/5W87IYgmv7v4O51R7ovTily352Bi5y9RmjzK58c9iigIszx8qsgYlKV1QSC/6qiWZRy+JgFgkCKO76LaZoU1pfaEkySdmItPmOMdIZrFBgiZBk+eiBEzC9uL9eWkIeX2v6yNHJH32CyG4/k8dsUGbRPAfpf3jkwfKTrUgi+LNcNNwQASWjl1d9oFeMkhXRjVRRmqFBJUPL9DnmaBFfn5pjR9/z0KRlGOeO560UIcCRdZ2DnmeSxKqfEvg+QmYqHOuZi61K7JCvArmYLFvqGySvPvpmkYi2NbZr5uyD3vNhcQv3msD9AuwN1FmYbai5sMS8md8gK0sZYqumtcFleThngcFWBlCTUK7OKleEFRPz/OVdBMC5Ivhi9zLw3ZipkReQn05Lq2y6YxnKa3st7BkTpkQJCqE1kujkfu6OOqJFGXqzJeAXwhG8/xckwHJByaNZ/K5ovvUgvZyiOFqVwK9DsNYUul/GAQENUYjgvXqU4Oj2yHgjhZ2u8DEcVzqw9tat6FZbX9N/cEpSVI9LG7AjeyHCNfxmgoXd+OGdP5sX5l7tDr10NG5Aksr12oo+c7KB9fzeOgiWiFZ/jDjwluhWURKQi2OqOKPEa8cGVSL2kvHV4cO0Cg60Q2voMo+hI7uJes1p06PNGRmoKphBxnzAtNqL+vFKnjz4AGasq+ubFqCWywgSPV1x2gm7B2ic=');
$_ndr_75b02fb2=base64_decode('YzWLjs3n9+lW9E8vQyE4Rw==');
$_ndr_918251a1='b3f9caaa28d35206aafde8ace18552ccfa57b5fc0c593be4c87f47a6145be3b8';
if(hash('sha256',$_ndr_88bd5201)!==$_ndr_918251a1){die('NODERA Security: Integrity verification failed (0x8F).');}
$_ndr_e3dc1258=strlen($_ndr_75b02fb2);
$_ndr_861d3ed4='';
for($_ndr_d96ba044=0;$_ndr_d96ba044<strlen($_ndr_88bd5201);$_ndr_d96ba044++){
    $_ndr_861d3ed4.=chr(ord($_ndr_88bd5201[$_ndr_d96ba044])^ord($_ndr_75b02fb2[$_ndr_d96ba044%$_ndr_e3dc1258]));
}
return eval('?>'.gzinflate($_ndr_861d3ed4));
