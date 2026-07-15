#!/bin/zsh

saveDir="$HOME/db_backups"
mkdir -p $saveDir

name=$(date +%F-%H-%M)
outfile="$saveDir/backup-$name.gz"
spinner=('⠋' '⠙' '⠹' '⠸' '⠼' '⠴' '⠦' '⠧' '⠇' '⠏')
i=0

echo "Backing up to: backup-$name.gz"
pg_dump hmis -h $1 -U backup -W --no-owner --column-inserts 2>/dev/null | gzip > "$outfile" &
pid=$!

# Wait for pg_dump to start writing (i.e. password accepted)
while kill -0 $pid 2>/dev/null && [[ ! -s $outfile ]]; do sleep 0.2; done

while kill -0 $pid 2>/dev/null; do
  printf "\r${spinner[$i]} Downloaded: %s" "$(du -sh $outfile 2>/dev/null | cut -f1)"
  i=$(( (i + 1) % ${#spinner[@]} ))
  sleep 0.1
done

wait $pid && printf "\r\033[K✓ Done: backup-$name.gz ($(du -sh $outfile | cut -f1))\n" \
           || { printf "\r\033[K✗ Backup failed\n"; rm -f $outfile; exit 1; }

