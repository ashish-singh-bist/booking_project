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
import threading
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
from BookingDownload import BookingDownload

obj_booking = BookingDownload()

def worker(url,temp_file,checkin_date,checkout_date):
  print( "filename:"+temp_file)
  """thread worker function"""  
  result = obj_booking.parseProductDetails(url,temp_file,checkin_date,checkout_date)




if __name__ == '__main__':
  obj_master = Master()    

  ##########
  #temp_date_today = datetime.datetime(2018, 9, 17)
  # temp_date_today = datetime.datetime(2018, 9, 17)
  # property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',{'url':1},{'updated_at':{ '$lt': temp_date_today }})
  # for property_url_row in property_url_rows:
  #     print(str(property_url_row['url']))
  # exit()
  ##########
  temp_date_today = datetime.datetime(2018, 9, 18)#datetime.datetime.now()
  #property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',{'url':1},{'updated_at':{ '$lt': temp_date_today }})
  property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',{'url':1,'_id':1},{'updated_at':{ '$lt': temp_date_today }})  
  #property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls')
  #property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',{'url':1,'_id':1},{'_id':"5b9fb00152c92b16c314fb1c"})
  
  temp_index = 1
  if obj_master.obj_helper.isFileExists( "Index" ):
    temp_index = int(obj_master.obj_helper.readFile("Index"))
  
  url_count = 1
  for property_url_row in property_url_rows:
    if url_count<temp_index:
      url_count = url_count+1
      continue
    temp_prop_id = property_url_row['_id']
    property_url = property_url_row['url']
    start_date = datetime.datetime.now().date()
    end_date = datetime.datetime.now().date() + timedelta(days=365)
    while start_date < end_date:        
        checkin_date = str(start_date)
        arr_length_stay = [1,2,3,5,7]
        number_of_guests = 2
        for length_stay in arr_length_stay:
          checkout_date = str( start_date + timedelta(days=length_stay) )  # increase day one by one
          print( "url number:"+str(url_count)+"-checkin:"+checkin_date," length_stay:"+str(length_stay) )
          url = property_url+"?checkin="+str(checkin_date)+"&checkout="+str(checkout_date)+"&selected_currency=USD"+"&group_adults="+str(number_of_guests)          
          print(url)
          #5b9fb00152c92b16c314fb1c-2018-09-17-1.html
          temp_file = str(temp_prop_id)+"-"+str(checkin_date)+"-"+str(length_stay)+".html"
          if obj_master.obj_helper.isFileExists( "./html_dir/" + temp_file ):
            html = obj_master.obj_helper.readFile( "./html_dir/" + temp_file ) 
            print( ".....File Already exists" )
            continue     
          t = threading.Thread(target=worker,args=(url,temp_file,checkin_date,checkout_date))
          t.start()
          while threading.activeCount() > 100:
            time.sleep(2)
        start_date = start_date + timedelta(days=1)  # increase day one by one
    url_count = url_count + 1
    obj_master.obj_helper.writeFileNew("Index",str(url_count))

  #wait till all the threads are almost done
  while threading.activeCount() > 1:
    time.sleep(2)
  exit()