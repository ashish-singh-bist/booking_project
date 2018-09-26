#!/usr/bin/python
# -*- coding: utf-8 -*- 
import os
import time
import re
import os
import sys

scraper_path = '';
action = ''

if(len(sys.argv)>1):
  action = sys.argv[1]
  if(len(sys.argv)>2):
    scraper_path = sys.argv[2]  
else:
  print( '{"status":"error", "message": "Invalid command line argument..."}' )
  exit()


#####################
processoutput = os.popen("ps -A -L -F").read()
cur_script = os.path.basename(__file__)
res = re.findall(cur_script,processoutput)
if len(res)>2:
    #print ("EXITING BECAUSE ALREADY RUNNING.\n\n")
    exit(0)

# #####################
def stopScript():
  processoutput = os.popen("ps -A -L -F").read()
  arr_response = re.findall(r'.+parse_bookings_thread\.py',processoutput)

  p_kill_status = 0
  for response_line in arr_response:

    #rtech    30544 29953 30544  0    1 10851 13264   1 12:42 pts/14   00:00:00 python3 -m pdb parse_bookings_thread.py
    m = re.search(r'.+?(\d+)\s+', response_line)
    if m:
      process_id = m.group(1)
      #print(response_line)
      #print(process_id)
      command_str = 'kill '+process_id
      returned_value = os.system(command_str)
      if returned_value == 0:
        p_kill_status = 1

  if p_kill_status:
    print ( '{"status":"success", "message": "Scraper stopped successfully!"}' )
  else:
    print ( '{"status":"error", "message": "Error!"}' )

def startScript():
  if(scraper_path != ''):
    returned_value = os.system('cd ' + scraper_path + '; /usr/bin/python3 parse_bookings_thread.py > /dev/null 2>/dev/null &')
    if returned_value:
      print ( '{"status":"success", "message": "Scraper restart successfully!"}' )
    else:
      print ( '{"status":"error", "message": "Got some error in running scraper.." ' + returned_value+ '}' )
  else:
    print ( '{"status":"error", "message": "Scraper path not found..."}' )

def restartScript():
  stopScript()
  startScript()

if __name__ == '__main__':
  if(action == 'stop'):
    stopScript()
  elif(action == 'restart'):
    restartScript()
  else:
    print ( '{"status":"error", "message": "Invalid command line argument..."}' )