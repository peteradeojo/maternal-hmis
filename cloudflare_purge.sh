#!/bin/bash
source .env

curl -X POST https://api.cloudflare.com/client/v4/zones/${CLOUDFLARE_ZONE_ID}/purge_cache -H "Authorization: Bearer ${CLOUDFLARE_TOKEN}" --json '{"purge_everything": true}'
