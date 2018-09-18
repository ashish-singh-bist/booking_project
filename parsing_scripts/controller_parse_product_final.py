#!/usr/bin/python
# -*- coding: utf-8 -*- 

import os
import time
import sys, getopt
#import csv
#import requests
import re
#import hashlib
import json
#import threading

#####################
processoutput = os.popen("ps -A -L -F").read()
cur_script = os.path.basename(__file__)
res = re.findall(cur_script,processoutput)
if len(res)>2:
    print ("EXITING BECAUSE ALREADY RUNNING.\n\n")
    exit(0)
#####################

sys.path.append("modules")
sys.path.append("scripts")
sys.path.append("/usr/local/lib/python3.5/dist-packages")
from Master import Master
from Booking import Booking

if __name__ == '__main__':
  obj_master = Master()  
  obj_booking = Booking()

  records = obj_booking.obj_mongo_db.recSelectById( 'property_urls' , '5b9b6da952c92b0b137ac1b2' )
  print(records)
  exit()

  url = "https://www.booking.com/hotel/in/le-meridien-goa-calangute.en-gb.html?label=gen173nr-1DCAEoggJCAlhYSDNYBGhsiAEBmAEuwgEKd2luZG93cyAxMMgBFdgBA-gBAZICAXmoAgM&sid=ab5ef8101dc5ba536df5822762aeffbe&checkin=2018-10-01&checkout=2018-10-03&ucfs=1&srpvid=7ae537078f32005b&srepoch=1536306576&highlighted_blocks=252743304_103889513_2_41_0&all_sr_blocks=252743304_103889513_2_41_0&room1=A,A&hpos=2&hapos=2&dest_type=city&dest_id=-2093662&srfid=9ef8133084306d62f819a16344827e79e94caec1X2&from=searchresults;highlight_room=#hotelTmpl"
  result = obj_booking.parseProductDetails(url)
  print(str(result))

  #if 'hotel_info' in result:
  #  ret_id = obj_booking.obj_mongo_db.recInsert('parsed_data',[result['hotel_info']])
  #  print( "\nThe return id is"+str(ret_id) )

  exit()
  ret_id = obj_booking.obj_mongo_db.recInsert('parsed_data',[result])
  print( "\nThe return id is"+str(ret_id) )

  # rows = obj_master.obj_db.recCustomQuery("select * from ean_list WHERE (url IS NOT NULL AND status_flag='0') AND source='pneu guru::fr' order by id ASC limit 3000")
  # #del obj_master  #No need this object anymore
  
  
  # i = 0
  # while i < len(rows):
  #   print( "parsing " + str(i+1) + " of total:" + str( len(rows) ) )
  #   obj_ebay.parseProduct(url)
  #   i = i + 1
    
