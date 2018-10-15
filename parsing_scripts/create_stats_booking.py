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
import datetime
import hashlib
from datetime import timedelta, date
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
obj_master = Master()
html_dir_path = obj_master.obj_config.html_dir_path

def getDateTimeObject(date_str):
  datetime_object = datetime.datetime.strptime(date_str, '%Y-%m-%d')
  return datetime_object

def getLastDateObject():
  current_date = datetime.datetime.now().date()
  last_date = current_date - timedelta(days=1)  # decrease day one by one
  last_date_object = getDateTimeObject(str(last_date))
  return last_date_object  


if __name__ == '__main__':
  dict_stats = {}

  # config_rows = obj_master.obj_mongo_db.recSelect('config')
  # for config_row in config_rows:    
  #   if 'parsing_interval' in config_row and config_row['parsing_interval']:
  #     parsing_interval = int(config_row['parsing_interval'])      
  #     date_before_time_interval = datetime.datetime.now() - timedelta(days=parsing_interval)
  #     dict_stats['property_pending'] = obj_master.obj_mongo_db.getCount('property_urls',None,{'is_active':1,'updated_at':{ '$lt': date_before_time_interval }})
  ##########################################
  dict_stats['property_pending'] = 0
  property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',None,{'is_active':1},1000,'updated_at','ASC')  
  for property_url_row in property_url_rows:
    if 'parse_interval' in property_url_row:
      parse_interval = int(property_url_row['parse_interval'])      
      date_time_interval = datetime.datetime.now() - timedelta(days=parse_interval)
      print("parse_interval:"+str(parse_interval)+" udpate_ts:"+str(date_time_interval))
      if property_url_row['updated_at'] <=  date_time_interval:
        dict_stats['property_pending'] = dict_stats['property_pending']+1
  ##########################################
    
  last_date_obj = getLastDateObject()
  current_date_obj = getDateTimeObject(str(datetime.datetime.now().date()))

  dict_stats['date'] = last_date_obj
  dict_where = { 'updated_at':{ '$gte': last_date_obj , '$lt': current_date_obj } }
  dict_stats['property_parsed'] = obj_master.obj_mongo_db.getCount('property_urls',None,dict_where)
  dict_stats['hotel_parsed'] = obj_master.obj_mongo_db.getCount('hotel_master',None,dict_where)
  dict_stats['price_parsed'] = obj_master.obj_mongo_db.getCount('prices',None,dict_where)
  dict_stats['room_details_parsed'] = obj_master.obj_mongo_db.getCount('room_details',None,dict_where)
  #dict_stats['rooms_availability_parsed'] = obj_master.obj_mongo_db.getCount('rooms_availability',None,dict_where)
  
  dict_stats['total_property'] = obj_master.obj_mongo_db.getCount('property_urls')
  dict_stats['active_property'] = obj_master.obj_mongo_db.getCount('property_urls',None,{'is_active':1})
  print(str(dict_stats))
  exit()
  ret_id = obj_master.obj_mongo_db.recInsert( 'stats_booking' , [ dict_stats ] )
  print( "\ninserted in stats table. The return id is"+str(ret_id) )

    