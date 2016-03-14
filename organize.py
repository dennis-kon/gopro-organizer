#!/usr/bin/env python

import os
import sys
import time
import getopt
import pprint
import hashlib
from ConfigParser import SafeConfigParser

BLOCKSIZE = 1048576 #65536
DIR = os.path.dirname(os.path.realpath(__file__))
configfile = DIR + '/config.ini'
dryRun = False
argv = sys.argv[1:]
try:
    opts, args = getopt.getopt(argv,"hc:",["dry-run", "config"])
except getopt.GetoptError as e:
    pprint.pprint(e)
    print('Usage: organize.py [--dry-run] <microSD path>/DCIM/100GOPRO')
    sys.exit(2)
for opt, arg in opts:
    if opt == '-h':
        print('Usage: organize.py [--dry-run] <microSD path>/DCIM/100GOPRO')
        sys.exit()
    elif opt in ("-c", "--config"):
        configfile = arg
    elif opt == "--dry-run":
        dryRun = True
    elif opt in ("-c", "--config"):
        configfile = arg

config = SafeConfigParser()
config.read(configfile)

def sha256sum(file):
    hasher = hashlib.sha256()
    with open(file, 'rb') as afile:
        buf = afile.read(BLOCKSIZE)
        while len(buf) > 0:
            hasher.update(buf)
            buf = afile.read(BLOCKSIZE)
    return hasher.hexdigest()

def parseDate(date):
    return {
        "year": time.strftime('%Y', date),
        "month": time.strftime('%Y-%m', date),
        "day": time.strftime('%Y-%m-%d', date)
    }

def tryCreateDir(path):
    try:
        os.makedirs(path)
    except OSError as e:
        if e.errno != 17:
            raise e

directory = args[0]
unfilteredPath = config.get('gopro', 'destination')

files = [];
for file in os.listdir(directory):
    if file.endswith(".MP4"):
        origin = directory + file
        date = parseDate(time.localtime(os.path.getmtime(origin)))
        print("Calculating SHA256 for %s (microSD)" % (file))
        originHash = sha256sum(origin)
        destinationHash = ''
        path = '%s/%s/%s/%s/' % (unfilteredPath, date['year'], date['month'], date['day'])
        tryCreateDir(path)
        destination = path + file

        if os.path.isfile(destination):
            print("Calculating SHA256 for %s (NAS)" % (file))
            destinationHash = sha256sum(destination)

        if destinationHash == originHash:
            print("File %s already copied" % (file))
            continue

        files.append({"origin": {"file": origin, "sha256sum": originHash}, "destination": {"file": destination, "sha256sum": destinationHash}})

pprint.pprint(files)
