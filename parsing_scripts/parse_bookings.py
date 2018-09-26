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
import threading
import multiprocessing
from multiprocessing import cpu_count
from multiprocessing.dummy import Pool
from bson.objectid import ObjectId

#####################
processoutput = os.popen("ps -A -L -F").read()
cur_script = os.path.basename(__file__)
res = re.findall(cur_script,processoutput)
#print(str(res))
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


# def getDateTimeObject(date_str):
#   import time
#   datetime_object = time.strptime(date_str, '%Y-%m-%d')
#   return datetime_object
def getDateTimeObject(date_str):
  datetime_object = datetime.datetime.strptime(date_str, '%Y-%m-%d')
  return datetime_object

#def parseAndSaveData(url,temp_file,checkin_date,checkout_date,temp_prop_id):
def parseAndSaveData(temp_dict):
  obj_booking = Booking()
  url = temp_dict['url']
  prop_url = temp_dict['property_url']
  temp_file = temp_dict['temp_file']
  checkin_date = temp_dict['checkin_date']
  checkout_date = temp_dict['checkout_date']
  temp_prop_id = temp_dict['temp_prop_id']
  length_stay = temp_dict['length_stay']
  number_of_guests = temp_dict['number_of_guests']
  #if not '06da05365e66f4a7e791da884815a511.html' in temp_file:
  if not '05b4095c20c397893c4823a9e074e014.html' in temp_file:
    return 1  
  start_time = datetime.datetime.now()
  print( "\nParsing start:"+str(start_time) )
  print( "\nParsing file:"+temp_file )
  for i in range(1):
    result = obj_booking.parseProductDetails(url,temp_file,checkin_date,checkout_date)
  end_time = datetime.datetime.now()
  print( "\nParsing End:"+str(end_time) )
  elapsed_time = end_time - start_time
  print( "total_time:"+str(elapsed_time.total_seconds()) )
  
  #print(str(result))  
  #print(result)
  #return 1
  if 'hotel_info' in result:
    print( "hotel info extracted......" )
    hotel_id = result['hotel_info']['hotel_id']
    ###################
    if hotel_id:
      redis_hotel_id = obj_booking.obj_redis_cache.getKeyValue(temp_prop_id)
      if not redis_hotel_id:
        obj_booking.obj_mongo_db.recUpdate( 'property_urls' , { 'hotel_id':hotel_id } , { '_id':ObjectId(temp_prop_id) }  )
        obj_booking.obj_redis_cache.setKeyValue(temp_prop_id,redis_hotel_id)      
    ###################
    result['hotel_info']['length_stay'] = length_stay
    #showing error while insert in hotel details
    del result['hotel_info']['hotel_equipments']
    print( "\nDB function start:"+str(datetime.datetime.now()) )
    #obj_booking.obj_mongo_db.connect()    
    record_count = obj_booking.obj_mongo_db.getCount( 'hotel_master' , { 'hotel_id':1 }, { 'hotel_id':hotel_id } )
    #record_count = obj_booking.obj_mongo_db.getCount( 'hotel_master' , { 'hotel_id':1 }, { 'hotel_id':hotel_id , 'checkin_date':checkin_date , 'length_stay':length_stay } )
    #print("prop_id:"+str(temp_prop_id))
    #################    
    result['hotel_info']['prop_url'] = prop_url
    #################
    if record_count:
      print( "alredy inserted..." )
      #result['hotel_info']['prop_id'] = temp_prop_id
      #ret_id = obj_booking.obj_mongo_db.recUpdate( 'hotel_master' , result['hotel_info'] , { 'hotel_id':hotel_id } )
      #print( "\nUpdated in hotel_master The return id is"+str(ret_id) )
    else:
      #result['hotel_info']['prop_id'] = temp_prop_id
      ret_id = obj_booking.obj_mongo_db.recInsert( 'hotel_master' , [ result['hotel_info'] ] )
      print( "\ninserted in hotel_master The return id is"+str(ret_id) )
    if 'room_price_details' in result:
      dict_room_price_details = result['room_price_details']
      for dict_price_details in dict_room_price_details['price_details']:        
        for key_room_type in dict_price_details:                  
          dict_room_info = dict_price_details[key_room_type]                  
          if 'price_info' in dict_room_info:
            arr_price_info = dict_room_info['price_info']            
            if 'room_equipment' in dict_room_info:                      
              dict_room_info['hotel_id'] = hotel_id
              dict_room_info['room_type'] = key_room_type
              ###################
              #dict_room_info['prop_id'] = temp_prop_id
              dict_room_info['checkin_date'] = getDateTimeObject(checkin_date)
              dict_room_info['number_of_days'] = length_stay
              ###################
              record_count = obj_booking.obj_mongo_db.getCount( 'room_details' , { 'hotel_id':1 }, { 'hotel_id':hotel_id,'room_type':key_room_type , 'checkin_date':{ '$eq': getDateTimeObject(checkin_date) },'number_of_days':length_stay } )
              if not record_count:
                ret_id = obj_booking.obj_mongo_db.recInsert( 'room_details' , [ dict_room_info ] )
                print( "\ninserted in room_details The return id is"+str(ret_id) )                    
            available_only = ""            
            for dict_price_info in arr_price_info:              
              #available_only = dict_price_info['max_persons']
              if 'nr_stays' in dict_price_info and dict_price_info['nr_stays']:
                available_only = dict_price_info['nr_stays']
              ################
              #craeting these field manully
              dict_price_info['room_type'] = key_room_type
              dict_price_info['number_of_days'] = length_stay                    
              dict_price_info['number_of_guests'] = number_of_guests
              dict_price_info['hotel_id'] = hotel_id
              dict_price_info['checkin_date'] = getDateTimeObject(checkin_date)
              ################
              choices_str = "bp"
              if 'mealplan_included_name' in dict_price_info and dict_price_info['mealplan_included_name']:
                choices_str = dict_price_info['mealplan_included_name']
              if 'mealplan_desc' in dict_price_info and dict_price_info['mealplan_desc']:
                choices_str = choices_str + dict_price_info['mealplan_desc']
              if 'cancellation_type' in dict_price_info and dict_price_info['cancellation_type']:
                choices_str = choices_str + dict_price_info['cancellation_type']
              if 'cancellation_desc' in dict_price_info and dict_price_info['cancellation_desc']:
                choices_str = choices_str + dict_price_info['cancellation_desc']
              if 'other_desc' in dict_price_info and dict_price_info['other_desc']:
                for temp_other_desc in dict_price_info['other_desc']:
                  choices_str = choices_str + temp_other_desc
              #########################              
              #set the key in redis cache
              str_to_md5 = str(hotel_id)+str(checkin_date)+str(key_room_type)+str(length_stay)+str(number_of_guests)+choices_str
              #for now not including price we will add it later...
              if 'raw_price' in dict_price_info and dict_price_info['raw_price']:
                raw_price = dict_price_info['raw_price']
                temp_key_md5 = obj_booking.obj_helper.getMd5(str_to_md5)                      
                redis_value = obj_booking.obj_redis_cache.getKeyValue(temp_key_md5)
                print( "\n======REDIS VALUE:"+str(redis_value) )
                ####################
                update_count = 0
                if redis_value:
                  arr_redis_val = redis_value.split("#")
                  if len(arr_redis_val) == 3:
                    update_count = int(arr_redis_val[2])
                    #if raw_price is same which is parsed last time update the count
                    if raw_price == float(arr_redis_val[0]):
                      update_count = update_count + 1
                    else:
                      update_count = update_count + 1
                      dict_price_info['update_count'] = update_count
                      ###################                  
                      ret_id = obj_booking.obj_mongo_db.recInsert( 'hotel_prices' , [ dict_price_info ] )
                      print( "\ninserted in hotel_prices The return id is"+str(ret_id) )
                ####################
                else:
                  ###################
                  #dict_price_info['prop_id'] = temp_prop_id
                  dict_price_info['update_count'] = update_count
                  ###################                  
                  ret_id = obj_booking.obj_mongo_db.recInsert( 'hotel_prices' , [ dict_price_info ] )
                  print( "\ninserted in hotel_prices The return id is"+str(ret_id) )
                ################
                #update the redis value
                curr_date = datetime.datetime.now().date()
                temp_redis_value = str(dict_price_info['raw_price'])+"#"+str(curr_date)+"#"+str(update_count)
                obj_booking.obj_redis_cache.setKeyValue(temp_key_md5,temp_redis_value)
                ################
              #########################
            if available_only:
              dict_availability = {}
              dict_availability['hotel_id'] = hotel_id
              dict_availability['room_type'] = key_room_type
              dict_availability['checkin_date'] = getDateTimeObject(checkin_date)
              dict_availability['number_of_days'] = length_stay
              dict_availability['available_only'] = available_only
              #print(str(dict_availability))
              ###################
              #dict_availability['prop_id'] = temp_prop_id
              ###################
              record_count = obj_booking.obj_mongo_db.getCount( 'rooms_availability' , { 'hotel_id':1 }, { 'hotel_id':hotel_id,'room_type':key_room_type, 'checkin_date':{ '$eq': getDateTimeObject(checkin_date) },'number_of_days':length_stay } )
              if not record_count:
                ret_id = obj_booking.obj_mongo_db.recInsert( 'rooms_availability' , [ dict_availability ] )
                print( "\ninserted in rooms_availability The return id is"+str(ret_id) )
    #obj_booking.obj_mongo_db.disconnect()
    print( "\nDB function end:"+str(datetime.datetime.now()) )
    #exit()
    #print("===DB... disconnected......")
  return {}
    

if __name__ == '__main__':
  max_process = 200
  parsing_interval = 1
  number_of_guests = 2
  scraper_active = 1
  config_rows = obj_master.obj_mongo_db.recSelect('config')
  for config_row in config_rows:
    if 'thread_count' in config_row and config_row['thread_count']:
      max_process = int(config_row['thread_count'])
    if 'parsing_interval' in config_row and config_row['parsing_interval']:
      parsing_interval = int(config_row['parsing_interval'])
    if 'number_of_guests' in config_row and config_row['number_of_guests']:
      number_of_guests = int(config_row['number_of_guests'])
    if 'scraper_active' in config_row and config_row['scraper_active']:
      scraper_active = config_row['scraper_active']
  print( "The thread:"+str(max_process) )
  time.sleep(2)
  if not scraper_active:
    print( "SCRAPER IS NOT ACTVE" )
    time.sleep(2)
    exit()
  obj_master.obj_helper.writeFile( "LogScriptStatus-111.txt" , "\nStart:"+str(datetime.datetime.now()) )
  #pool = multiprocessing.Pool(processes=max_process)
  temp_date_today = datetime.datetime(2018, 9, 18)#datetime.datetime.now()  
  date_time_interval = temp_date_today - timedelta(days=parsing_interval)  # increase day one by one    
  print("Date Calculated from time interval:"+str(date_time_interval))  
  #exit()
  #property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',{'url':1},{'updated_at':{ '$lt': temp_date_today }})
  property_url_rows = obj_master.obj_mongo_db.recSelect('property_urls',None,{'is_active':1,'updated_at':{ '$lt': date_time_interval }})
  #obj_master.obj_mongo_db.disconnect()
  for property_url_row in property_url_rows:    
    temp_prop_id = property_url_row['_id']
    property_url = property_url_row['url']
    start_date = datetime.datetime(2018, 9, 18).date()#datetime.datetime.now().date()
    end_date = datetime.datetime.now().date() + timedelta(days=365)    
    arr_args_dict = []
    print(property_url)    
    #continue
    while start_date < end_date:        
      checkin_date = str(start_date)        
      arr_length_stay = [1,2,3,5,7]      
      for length_stay in arr_length_stay:
        checkout_date = str( start_date + timedelta(days=length_stay) )  # increase day one by one        
        url = property_url+"?checkin="+str(checkin_date)+"&checkout="+str(checkout_date)+"&selected_currency=USD"+"&group_adults="+str(number_of_guests)        
        ###############################################        
        url_md5 = obj_master.obj_helper.getMd5(url)
        temp_file = url_md5+".html"        
        if not obj_master.obj_helper.isFileExists( html_dir_path + temp_file ):
          obj_master.obj_helper.writeFile( "LogFileNotExists.txt" , "\nFile not exists for url"+url );
          continue
        #################
        arr_args_dict.append({'url':url,'property_url':property_url,'temp_file':temp_file,'checkin_date':checkin_date,'checkout_date':checkout_date,'temp_prop_id':temp_prop_id,'length_stay':length_stay,'number_of_guests':number_of_guests})
      start_date = start_date + timedelta(days=1)  # increase day one by one    
    #result = pool.map_async(parseAndSaveData, [args_dict for args_dict in arr_args_dict])
    for args_dict in arr_args_dict:
      result = parseAndSaveData(args_dict)
    #obj_master.obj_mongo_db.connect()    
    #ret_id = obj_master.obj_mongo_db.recUpdate( 'property_urls' , { 'updated_at':datetime.datetime.now() } , { '_id':ObjectId(temp_prop_id) } )
    #print( "\nUpdated in property_urls The return id is"+str(temp_prop_id) )    
    #obj_master.obj_mongo_db.disconnect()
    obj_master.obj_helper.writeFile( "LogScriptStatus-111.txt" , "\nEnd:"+str(datetime.datetime.now()) )
    #exit()  
  #wait till all the threads are almost done
  #while threading.activeCount() > 1:
  #  time.sleep(2)
  exit()

