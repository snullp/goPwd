#!/usr/bin/python

import re

wordset = set()

with open('dict.raw') as f:
    for line in f:
        if line[0]=='#': continue
        words_raw = re.findall(r"[\w']+",line)
        words = []
        for i in words_raw:
            if len(i) <= 3: continue
            words.append(i.lower().replace('\'',''))
        wordset = wordset.union(set(words))

with open('dict','w') as f:
    for word in wordset:
       f.write(word+"\n")
