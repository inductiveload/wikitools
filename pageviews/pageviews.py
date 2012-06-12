#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
This is a simple script to retreive and distill information about WMF
project page hits using data from:
http://dumps.wikimedia.org/other/pagecounts-raw

Please note that this data refers to any hits, including those that
did not resolve to an existing page. There also appear to be some
encoding errors in the data.

Usage - set the DATADIR directory to a location with plenty of disk
space as the data takes around 100MB per hour's logs.

TODO
Add command line options
Convert to a more usable class structure
"""

import re
import subprocess
import urllib2
import gzip
import StringIO
import os, codecs

DATADIR = '/media/DATA/Code/pageviews'
CACHE = 'cache'
OUTPUT = 'output'
RE = r'([a-z]+)(?:\.([a-z]))? (?:(\w{2})?:)?(?:/?(\w*?):)?(?:/?(.*?)) (\d+) (\d+)'

time = {'y':2012, 'm':2, 'd':2, 'h':4}
project = 'en.s'

def process_line(page):
	m = re.match(RE, page)

	if m:
		data = {'lang':m.group(1),
				'project':m.group(2),
				'langprefix':m.group(3),
				'nsprefix':m.group(4),
				'views':int(m.group(6)),
				'data':int(m.group(7))
				}

		title = '%s:%s' % (data['nsprefix'], m.group(5)) if data['nsprefix'] else m.group(5)
		title = urllib2.unquote(title).replace('_', ' ')

		return title, data
	else:
		return None


def timecode(t):
	if t['h'] != None:
		return '%(y)d-%(m)02d-%(d)02d %(h)02d00' % t
	else:
		return '%(y)d-%(m)02d-%(d)02d' % t

def get_url(t, sec=0):
	nt = t
	nt['s'] = sec
	return 'http://dumps.wikimedia.org/other/pagecounts-raw/%(y)d/%(y)d-%(m)02d/pagecounts-%(y)d%(m)02d%(d)02d-%(h)02d00%(s)02d.gz' % nt

def get_data(time):
	"""
	Returns a file-like object with the raw multi-project data in it.
	"""

	filename = os.path.join(DATADIR, CACHE, timecode(time))
	if os.path.exists(filename):
		print 'INF: File exists in cache: %s' % filename
		data = open(filename, 'r')
	else:
		print 'INF: Getting data for %s' % timecode(time)

		sec = 0

		while True:

			try:
				response = urllib2.urlopen(get_url(time, sec))
			except urllib2.HTTPError:
				if sec == 59:
					return None
				sec += 1
				print 'ERR: Failed to find file, trying seconds offset: %d' % sec
				continue
			break

		gzdata = StringIO.StringIO(response.read())

		data = gzip.GzipFile(fileobj=gzdata, mode='rb')
		print 'INF: Data received.'

		print 'INF: Writing to cache file: %s' % filename
		f = open(filename, 'w')
		f.write(data.read())

		f.seek(0)


	return data

def append_pages_from_file(filelike, pages):

	for line in filelike:
		if line.startswith(project):
			title, page = process_line(line)

			if page:
				if title in pages: #we get dups with leading slashes and wierd stuff
					pages[title]['views'] += page['views']
					pages[title]['data'] += page['data']
				else:
					pages[title] = page

	return pages

if __name__ == '__main__':

	pages = {}

	outfilename = os.path.join(DATADIR, OUTPUT, timecode(time))

	if os.path.exists(outfilename):
		cont = raw_input('INF: Processed data exists. Continue? [y/n] ')
	else:
		cont = 'y'

	if cont.lower() in ['y', 'yes']:
		outfile = codecs.open(outfilename, 'w', 'utf-8')

		#if no day, do every hour
		if time['h'] == None:
			times = [ {'y':time['y'], 'm':time['m'], 'd':time['d'], 'h':x} for x in range(24)]
		else:
			times = [ time ]

		for t in times:
			data = get_data(t)
			if data:
				pages = append_pages_from_file(data, pages)

		print 'INF: Processed list: %d pages in project %s' % (len(pages), project)

		outfile.write('# %s - %s pageviews\n' % (timecode(time), project) )

		for title, pagedata in sorted( pages.iteritems(),
										key=lambda x:(x[1]['views'], x[0]),
										reverse=True ):
			#print '%s: %d' % (title, pagedata['views'])
			if not pagedata['langprefix']: #scrap lang prefix pages

				try:
					outfile.write('%d\t%s\n' % (pagedata['views'], title.decode('utf-8')))
				except UnicodeDecodeError, UnicodeEncodeError:
					#we seem to get corrupted filenames sometimes - make do
					outfile.write('%d\t%s\n' % (pagedata['views'], repr(title) ))
					#print "\tERR: Error writing title, skipping: %s" % title

		print 'INF: Output generated at: %s' % outfilename
